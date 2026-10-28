<?php

namespace Webkul\Support\Services;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CompanyContext
{
    public const SESSION_KEY = 'active_company_ids';

    protected ?Collection $allowed = null;

    protected ?Company $currentCompany = null;

    protected bool $currentCompanyResolved = false;

    /**
     * An explicit company set for the current execution, overriding the session.
     *
     * Set by `runFor()` for queued work and by the API company selector, neither
     * of which has a web session to write to. `null` means "no override, use the
     * session as usual", which is why it is not an empty array by default.
     *
     * @var array<int, int>|null
     */
    protected ?array $forcedActiveIds = null;

    public function bypassed(): bool
    {
        if ($this->internalUser()?->isMultiCompanyAdmin()) {
            return false;
        }

        return Gate::allows('bypass_company_scope');
    }

    public function internalUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public function allowedCompanies(): Collection
    {
        if ($this->allowed !== null) {
            return $this->allowed;
        }

        $user = $this->internalUser();

        if (! $user) {
            return collect();
        }

        if ($this->seesAllCompanies()) {
            return $this->allowed = Company::query()->get();
        }

        return $this->allowed = $user->allowedCompanies()->get();
    }

    public function seesAllCompanies(): bool
    {
        return $this->bypassed();
    }

    public function allowedIds(): array
    {
        return $this->allowedCompanies()->pluck('id')->all();
    }

    public function defaultId(): ?int
    {
        return $this->internalUser()?->default_company_id;
    }

    public function activeIds(): array
    {
        if ($this->forcedActiveIds !== null) {
            // No acting user (a job or console command): the forced company is
            // the context it was queued with, and there is nothing to intersect
            // it against. With an acting user, membership still wins - a job
            // must not be able to hand someone access to a company they do not
            // belong to. That includes a user with no memberships at all, which
            // is why this asks "is anyone acting" rather than "is the allow-list
            // empty": the two used to be the same test and a memberless user
            // was handed the forced company unchecked.
            if (! $this->internalUser() instanceof User) {
                return $this->forcedActiveIds;
            }

            return array_values(array_intersect($this->forcedActiveIds, $this->allowedIds()));
        }

        $allowed = $this->allowedIds();

        if (empty($allowed)) {
            return [];
        }

        $stored = session(self::SESSION_KEY);

        if (! is_array($stored) || empty($stored)) {
            $default = $this->defaultId();

            return [$default && in_array($default, $allowed) ? $default : $allowed[0]];
        }

        $active = array_values(array_intersect($stored, $allowed));

        return empty($active) ? [$allowed[0]] : $active;
    }

    public function currentId(): ?int
    {
        if ($this->forcedActiveIds !== null) {
            // No default-company fallback: a forced context that resolved to
            // nothing must read as "no company", or every fail-closed check
            // downstream would quietly act on the user's default tenant instead.
            return $this->activeIds()[0] ?? null;
        }

        return $this->activeIds()[0] ?? $this->defaultId();
    }

    /**
     * Run a callback with one company as the active context, restoring the
     * previous context afterwards - including when the callback throws.
     *
     * This is the contract for queued work and console commands: the company has
     * to be explicit, because `CompanyScope` is skipped outside tests in console
     * execution and there is no session to fall back on. Without the `finally`,
     * one early return would leave the next thing in the process - a second job
     * on the same worker, the rest of a command - acting on the wrong tenant.
     */
    public function runFor(?int $companyId, Closure $callback): mixed
    {
        $previous = $this->forcedActiveIds;

        $this->forcedActiveIds = $companyId === null ? [] : [$companyId];
        $this->forgetCurrentCompany();

        try {
            return $callback();
        } finally {
            $this->forcedActiveIds = $previous;
            $this->forgetCurrentCompany();
        }
    }

    /**
     * Pin the context for the rest of the request, without a restore step.
     *
     * Used by the API company selector: a request is its own process lifetime, so
     * there is nothing to restore to. Prefer `runFor()` anywhere the process
     * continues afterwards.
     */
    public function forceActive(?int $companyId): void
    {
        $this->forcedActiveIds = $companyId === null ? [] : [$companyId];
        $this->forgetCurrentCompany();
    }

    public function currentCompany(): ?Company
    {
        if (
            $this->currentCompanyResolved
            && (! $this->currentCompany || in_array($this->currentCompany->getKey(), $this->allowedIds(), true))
        ) {
            return $this->currentCompany;
        }

        $this->currentCompanyResolved = true;

        $id = $this->currentId();

        return $this->currentCompany = $id ? Company::find($id) : null;
    }

    public function toggle(int $id): void
    {
        $active = $this->activeIds();

        if (in_array($id, $active)) {
            if (count($active) <= 1) {
                return;
            }

            $active = array_values(array_diff($active, [$id]));
        } else {
            $active[] = $id;
        }

        $this->setActive($active);
    }

    public function setActive(array $ids, ?int $current = null): void
    {
        $requested = array_values(array_unique(array_map('intval', $ids)));
        $allowed = $this->allowedIds();
        $unauthorized = array_diff($requested, $allowed);

        if ($unauthorized !== [] || ($current && ! in_array($current, $allowed, true))) {
            throw new AuthorizationException;
        }

        $valid = array_values(array_intersect($requested, $allowed));

        $current ??= $this->activeIds()[0] ?? null;

        if ($current && in_array($current, $valid)) {
            $valid = array_values(array_unique([$current, ...$valid]));
        }

        session([self::SESSION_KEY => $valid]);

        $this->forgetCurrentCompany();
    }

    public function reset(): void
    {
        session()->forget(self::SESSION_KEY);

        $this->forgetCurrentCompany();
    }

    protected function forgetCurrentCompany(): void
    {
        $this->currentCompany = null;
        $this->currentCompanyResolved = false;
    }
}
