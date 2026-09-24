<?php

namespace Webkul\Logistics\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Exceptions\StopLinkUnavailable;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Models\StopLink;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\CompanyContext;

/**
 * One-time POD capture links (D15, WP-5b).
 *
 * Drivers have no logins (D6), so proof of delivery is captured through a
 * single-stop URL the dispatcher sends by SMS or WhatsApp. No messaging
 * provider is built in: issue() returns the URL and the dispatcher shares it.
 *
 * ## How an unauthenticated request is authorised
 *
 * The token is the credential, and capture() exchanges it for the identity of
 * the dispatcher who issued the link - `Auth::onceUsingId()`, which does not
 * persist into the session. Nothing in DeliveryService is relaxed: its
 * `LogisticsAccess::ensureEnabled()`, `Gate::authorize('markDelivered')`,
 * `Gate::authorize('capturePod')`, company scope and POD validation all run
 * exactly as they do for a user in the panel.
 *
 * That choice is deliberate. The alternative - a "skip the checks when there is
 * no user" path through DeliveryService - would leave a second, weaker way into
 * the same service, and the next caller to forget a flag gets it for free. It
 * also gives the right failure modes: deactivate the dispatcher or take away
 * their capture_pod permission, and their outstanding links stop working.
 */
class StopLinkService
{
    /**
     * Tokens are 64 random characters, stored only as a SHA-256 hash.
     */
    protected const TOKEN_BYTES = 64;

    public function __construct(protected DeliveryService $delivery) {}

    /**
     * Issue a link for a delivery stop and return the URL to share.
     *
     * The plaintext token is returned here and nowhere else - it is not stored,
     * not logged and not recoverable. Re-issuing revokes any earlier unused
     * link for the same stop, so a URL that has already been shared (or leaked)
     * stops working the moment a replacement is made.
     */
    public function issue(Stop $stop): string
    {
        // Read back under the company scope: the ability check below grants on
        // permission plus the switch, so without this a user could issue a link
        // for a stop belonging to a company they cannot see.
        $stop = Stop::query()->whereKey($stop->getKey())->firstOrFail();
        $shipment = Shipment::query()->whereKey($stop->shipment_id)->firstOrFail();

        LogisticsAccess::ensureEnabled((int) $shipment->company_id);
        Gate::authorize('sendPodLink', $shipment);

        if ($stop->type !== StopType::DELIVERY) {
            throw StopLinkUnavailable::make();
        }

        $token = Str::random(self::TOKEN_BYTES);
        $ttlHours = max(1, (int) CompanySetting::forCompany((int) $shipment->company_id)->stop_link_ttl_hours);

        DB::transaction(function () use ($stop, $token, $ttlHours): void {
            StopLink::query()
                ->where('stop_id', $stop->getKey())
                ->whereNull('used_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            // company_id is left to InheritsParentCompany: the link takes the
            // stop's company, never the session's.
            StopLink::create([
                'token_hash'    => $this->hash($token),
                'expires_at'    => now()->addHours($ttlHours),
                'stop_id'       => $stop->getKey(),
                'created_by_id' => Auth::id(),
            ]);
        });

        return route('logistics.stop-link.show', ['token' => $token]);
    }

    public function revoke(Stop $stop): void
    {
        $stop = Stop::query()->whereKey($stop->getKey())->firstOrFail();
        $shipment = Shipment::query()->whereKey($stop->shipment_id)->firstOrFail();

        Gate::authorize('sendPodLink', $shipment);

        StopLink::query()
            ->where('stop_id', $stop->getKey())
            ->whereNull('used_at')
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }

    /**
     * Resolve a plaintext token to its link, or refuse.
     *
     * Scopes are removed because there is no session here at all - the token is
     * the only thing identifying the company. It is looked up *only* by hash,
     * so nothing but the matching row is reachable.
     */
    public function resolve(string $token): StopLink
    {
        $link = StopLink::withoutGlobalScope(CompanyScope::class)
            ->where('token_hash', $this->hash($token))
            ->first();

        if (! $link?->isUsable()) {
            throw StopLinkUnavailable::make();
        }

        return $link;
    }

    /**
     * The stop a link is for, with only what the capture page needs.
     */
    public function stopFor(StopLink $link): Stop
    {
        $stop = Stop::withoutGlobalScope(CompanyScope::class)
            ->whereKey($link->stop_id)
            ->with(['shipment:id,name,company_id,state,customer_reference'])
            ->first();

        if (! $stop || in_array($stop->state, [StopState::DEPARTED, StopState::SKIPPED], true)) {
            throw StopLinkUnavailable::make();
        }

        return $stop;
    }

    /**
     * Capture proof of delivery through a link, consuming it.
     *
     * The link is locked and re-checked inside the transaction, so two
     * simultaneous submissions cannot both spend it, and it is marked used only
     * once the delivery has succeeded - a rejected photo leaves the link usable
     * so the driver can try again rather than being locked out at the door.
     */
    public function capture(string $token, PodData $podData): Shipment
    {
        $link = $this->resolve($token);
        $stop = $this->stopFor($link);

        return $this->asIssuer($link, fn (): Shipment => DB::transaction(function () use ($link, $stop, $podData): Shipment {
            $locked = StopLink::withoutGlobalScope(CompanyScope::class)
                ->whereKey($link->getKey())
                ->lockForUpdate()
                ->first();

            if (! $locked?->isUsable()) {
                throw StopLinkUnavailable::make();
            }

            $shipment = Shipment::query()->whereKey($stop->shipment_id)->firstOrFail();

            $result = $this->delivery->deliver($shipment, $podData);

            $locked->forceFill(['used_at' => now()])->save();

            return $result;
        }));
    }

    /**
     * Run a callback as the dispatcher who issued the link, in their company.
     *
     * Both halves are needed: the company context so the company scope can see
     * the shipment and the tenant disk resolves to the right prefix, and the
     * user so every Gate check in DeliveryService has a real principal to judge.
     * `onceUsingId` does not touch the session, so nothing about this request
     * stays logged in afterwards.
     */
    protected function asIssuer(StopLink $link, callable $callback): mixed
    {
        if (! $link->created_by_id) {
            throw StopLinkUnavailable::make();
        }

        $context = app(CompanyContext::class);
        $activeIds = $context->activeIds();
        $currentId = $context->currentId();
        $companyId = (int) $link->company_id;

        // The web guard by name, not the default one. onceUsingId() exists on
        // the session guard but not on every guard Laravel can have as its
        // default - the API's token guard has no such method and throws a
        // BadMethodCallException - and which guard is "default" depends on what
        // ran earlier in the process. The panel authenticates on web, and that
        // is the guard whose user the policies below are about.
        $guard = Auth::guard('web');

        // Whoever was on this request before, if anyone: restored below rather
        // than logged out. Calling logout() here would end the session of an
        // admin who happened to open a stop link in their own browser.
        $previousUser = $guard->user();

        $context->setActive([$companyId], $companyId);

        try {
            if (! $guard->onceUsingId($link->created_by_id)) {
                // The issuing user is gone or deactivated, so the link no
                // longer has an authority behind it.
                throw StopLinkUnavailable::make();
            }

            return $callback();
        } finally {
            $previousUser ? $guard->setUser($previousUser) : $guard->forgetUser();

            $context->setActive($activeIds, $currentId);
        }
    }

    protected function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
