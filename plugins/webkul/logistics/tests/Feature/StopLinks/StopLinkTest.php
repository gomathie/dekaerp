<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Logistics\Enums\ProofCaptureChannel;
use Webkul\Logistics\Enums\ShipmentState;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Models\StopLink;
use Webkul\Logistics\Models\Trip;
use Webkul\Logistics\Services\StopLinkService;
use Webkul\Support\Models\Scopes\CompanyScope;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    Storage::fake('public');
});

/**
 * A shipment out for delivery with one open delivery stop - the only state in
 * which a POD link is any use.
 */
function deliverableStop($company): Stop
{
    $shipment = LogisticsHelper::shipment($company, [
        'state' => ShipmentState::OUT_FOR_DELIVERY,
    ]);

    return Stop::create([
        'shipment_id' => $shipment->id,
        'sequence'    => 1,
        'type'        => StopType::DELIVERY,
        'state'       => StopState::PENDING,
        'company_id'  => $company->id,
    ]);
}

/**
 * Become a stranger with the link and nothing else.
 *
 * The session is flushed as well as the login: the dispatcher who issued the
 * link left a company in the session, and a driver opening the URL on their own
 * phone has none. Without this the tests would pass on session state the real
 * request never has.
 */
function asGuest(): void
{
    Auth::guard('web')->logout();

    session()->flush();
}

/**
 * The token out of a URL that issue() returned.
 */
function tokenFrom(string $url): string
{
    return basename(parse_url($url, PHP_URL_PATH));
}

function issuedLink($company, array $permissions = ['send_pod_link', 'mark_delivered', 'capture_pod']): array
{
    $stop = deliverableStop($company);

    CompanyHelper::actingAsCompanyUser($company, array_map(
        fn (string $ability): string => $ability.'_logistics_shipment',
        $permissions,
    ));

    $url = app(StopLinkService::class)->issue($stop);

    return [$stop, tokenFrom($url), $url];
}

it('issues a link whose token is never stored, only its hash', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, $token] = issuedLink($company);

    $link = StopLink::withoutGlobalScope(CompanyScope::class)->sole();

    expect($link->stop_id)->toBe($stop->id)
        ->and($link->company_id)->toBe($company->id)
        ->and($link->token_hash)->toBe(hash('sha256', $token))
        // The plaintext token must not be recoverable from the database.
        ->and($link->token_hash)->not->toBe($token)
        ->and($link->isUsable())->toBeTrue();
});

it('refuses to issue a link without the send_pod_link permission', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());
    $stop = deliverableStop($company);

    CompanyHelper::actingAsCompanyUser($company, ['view_any_logistics_shipment']);

    expect(fn () => app(StopLinkService::class)->issue($stop))
        ->toThrow(AuthorizationException::class)
        ->and(StopLink::withoutGlobalScope(CompanyScope::class)->count())->toBe(0);
});

it('refuses to issue a link for another company’s stop', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $stop = deliverableStop($b);

    CompanyHelper::actingAsCompanyUser($a, ['send_pod_link_logistics_shipment']);

    // The company scope hides it, so it cannot even be read to be issued for.
    expect(fn () => app(StopLinkService::class)->issue($stop))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('revokes an earlier unused link when a new one is issued', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, $firstToken] = issuedLink($company);

    $secondUrl = app(StopLinkService::class)->issue($stop->refresh());

    asGuest();

    // The first URL, which may already have been sent or leaked, is dead.
    $this->get(route('logistics.stop-link.show', ['token' => $firstToken]))
        ->assertNotFound();

    $this->get($secondUrl)->assertOk();
});

it('opens the capture page for a valid token with no login', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, $token, $url] = issuedLink($company);

    asGuest();

    $this->get($url)
        ->assertOk()
        ->assertSee($stop->shipment->name)
        ->assertSee('recipient_name', false);
});

it('gives the same 404 for an unknown, expired, used or revoked token', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [, , $url] = issuedLink($company);
    $link = StopLink::withoutGlobalScope(CompanyScope::class)->sole();

    asGuest();

    // Unknown.
    $this->get(route('logistics.stop-link.show', ['token' => str_repeat('a', 64)]))
        ->assertNotFound();

    // Expired.
    $link->forceFill(['expires_at' => now()->subMinute()])->save();
    $this->get($url)->assertNotFound();

    // Used.
    $link->forceFill(['expires_at' => now()->addDay(), 'used_at' => now()])->save();
    $this->get($url)->assertNotFound();

    // Revoked.
    $link->forceFill(['used_at' => null, 'revoked_at' => now()])->save();
    $this->get($url)->assertNotFound();
});

it('leaks nothing about the shipment on the refusal page', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    StopLink::withoutGlobalScope(CompanyScope::class)->sole()
        ->forceFill(['revoked_at' => now()])->save();

    asGuest();

    $this->get($url)
        ->assertNotFound()
        ->assertDontSee($stop->shipment->name)
        ->assertDontSee($company->name);
});

it('captures proof of delivery through the link and marks it used', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    asGuest();

    $response = $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'notes'          => 'Left with the security desk.',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
        'latitude'       => 5.6037,
        'longitude'      => -0.187,
    ]);

    $response->assertOk();

    $shipment = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole();
    $proof = $shipment->deliveryProofs()->sole();

    expect($shipment->state)->toBe(ShipmentState::DELIVERED)
        ->and($proof->recipient_name)->toBe('Ama Mensah')
        ->and($proof->captured_via)->toBe(ProofCaptureChannel::STOP_LINK)
        ->and($proof->photo_path)->not->toBeNull()
        ->and((float) $proof->latitude)->toBe(5.6037)
        ->and(StopLink::withoutGlobalScope(CompanyScope::class)->sole()->used_at)->not->toBeNull();
});

it('does not leave the capturing request logged in as the issuing user', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [, , $url] = issuedLink($company);

    asGuest();

    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
    ])->assertOk();

    // The token buys one delivery, not a session.
    //
    // Asserted on the web guard by name. Auth::check() would consult whichever
    // guard is default in this process - Sanctum's request guard, which caches
    // the user it resolved during the request and has no session to leave
    // behind. The web guard is the one the service borrows an identity from,
    // so it is the one that could strand a login.
    $this->assertGuest('web');
});

it('cannot be used twice', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [, , $url] = issuedLink($company);

    asGuest();

    $payload = [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
    ];

    $this->post($url, $payload)->assertOk();
    $this->post($url, $payload)->assertNotFound();

    expect(Shipment::withoutGlobalScopes()->sole()->deliveryProofs()->count())->toBe(1);
});

it('leaves the link usable when the submission is rejected', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    // This company insists on a photo, and none is sent.
    CompanySetting::forCompany($company->id)->forceFill(['require_pod_photo' => true])->save();

    asGuest();

    $this->post($url, ['recipient_name' => 'Ama Mensah'])
        ->assertSessionHasErrors('photo');

    $link = StopLink::withoutGlobalScope(CompanyScope::class)->sole();

    // A rejected photo must not lock the driver out at the door.
    expect($link->used_at)->toBeNull()
        ->and($link->isUsable())->toBeTrue()
        ->and(Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->state)
        ->toBe(ShipmentState::OUT_FOR_DELIVERY);
});

it('stores the proof under the shipment’s own company, not the session’s', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    asGuest();

    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->company_id)->toBe($company->id)
        ->and($proof->photo_path)->toContain('logistics/delivery-proofs/'.$stop->shipment_id);
});

it('accepts a canvas signature as a real image file', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    // A 1x1 transparent PNG, the shape a canvas toDataURL() produces.
    $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8AAAwAB/wFrqAAAAABJRU5ErkJggg==';

    asGuest();

    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'signature'      => $png,
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->signature_path)->not->toBeNull();
});

it('ignores a signature field that is not a PNG data URL', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    asGuest();

    // Not an error: the signature is optional, so junk is treated as absent
    // rather than blocking a delivery at the door.
    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'signature'      => 'data:text/html;base64,PHNjcmlwdD4=',
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->signature_path)->toBeNull();
});

it('throttles a link on its own token and says when to retry', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['stop_link_rate_limit' => 3])->save();

    [, , $url] = issuedLink($company);

    asGuest();

    foreach (range(1, 3) as $ignored) {
        $this->get($url)->assertOk();
    }

    $this->get($url)
        ->assertStatus(429)
        // A driver whose phone retried needs to know how long to wait.
        ->assertHeader('Retry-After');
});

it('does not let one company’s link spend another’s rate limit', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($a->id)->forceFill(['stop_link_rate_limit' => 2])->save();
    CompanySetting::forCompany($b->id)->forceFill(['stop_link_rate_limit' => 2])->save();

    [, , $urlA] = issuedLink($a);
    [, , $urlB] = issuedLink($b);

    asGuest();

    // Company A exhausts its own budget. Drivers share mobile carrier NAT
    // addresses, so if the limit were keyed on the IP this would also lock out
    // company B - which is the whole reason it is keyed on the token.
    $this->get($urlA)->assertOk();
    $this->get($urlA)->assertOk();
    $this->get($urlA)->assertStatus(429);

    $this->get($urlB)->assertOk();
});

it('records the driver on the proof only when the company asks for it', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    $driver = Driver::factory()->create(['company_id' => $company->id]);
    $trip = Trip::create(['company_id' => $company->id, 'driver_id' => $driver->id]);

    $stop->forceFill(['trip_id' => $trip->id])->save();

    asGuest();

    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    // Off by default, so an existing company's proofs are unchanged.
    expect($proof->driver_id)->toBeNull();
});

it('records the driver from the trip when the option is on', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['capture_driver_on_pod' => true])->save();

    [$stop, , $url] = issuedLink($company);

    $driver = Driver::factory()->create(['company_id' => $company->id]);
    $trip = Trip::create(['company_id' => $company->id, 'driver_id' => $driver->id]);

    $stop->forceFill(['trip_id' => $trip->id])->save();

    asGuest();

    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
        // Sent by whoever holds the link, and ignored: the driver is taken from
        // the trip, so this cannot be claimed from the request.
        'driver_id'      => 999999,
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->driver_id)->toBe($driver->id);
});

it('requires the recipient’s ID when the company asks for one', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['capture_recipient_id' => true])->save();

    [$stop, , $url] = issuedLink($company);

    asGuest();

    // Enforced by DeliveryService, not only by the page: the form is not the
    // boundary, and a company that asks for evidence is asking for evidence.
    $this->post($url, ['recipient_name' => 'Ama Mensah'])
        ->assertSessionHasErrors('recipient_id_reference');

    $this->post($url, [
        'recipient_name'         => 'Ama Mensah',
        'recipient_id_reference' => 'GHA-0123456789',
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->recipient_id_reference)->toBe('GHA-0123456789');
});

it('does not keep a recipient ID that was not asked for', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    asGuest();

    // The option is off, so the field is not on the page - but a crafted post
    // could still send it. Nothing is stored: turning the option off means the
    // company has decided not to hold this.
    $this->post($url, [
        'recipient_name'         => 'Ama Mensah',
        'recipient_id_reference' => 'GHA-0123456789',
    ])->assertOk();

    $proof = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->sole();

    expect($proof->recipient_id_reference)->toBeNull();
});

it('offers only the sanctioned link lifetimes', function () {
    expect(array_keys(StopLinkService::ttlOptions()))->toBe([4, 8, 16, 24, 48]);
});

it('issues a link that expires after the company’s chosen lifetime', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    CompanySetting::forCompany($company->id)->forceFill(['stop_link_ttl_hours' => 4])->save();

    issuedLink($company);

    $link = StopLink::withoutGlobalScope(CompanyScope::class)->sole();

    expect($link->expires_at->diffInHours(now()->addHours(4), absolute: true))->toBeLessThan(1);
});

it('cancels a live link without having to issue a replacement', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    $shipment = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole();

    expect(app(StopLinkService::class)->hasLiveLink($shipment))->toBeTrue();

    // The case this exists for: the URL went to the wrong number, and the
    // dispatcher wants it dead rather than replaced.
    expect(app(StopLinkService::class)->revokeForShipment($shipment))->toBe(1)
        ->and(app(StopLinkService::class)->hasLiveLink($shipment))->toBeFalse();

    asGuest();

    $this->get($url)->assertNotFound();
});

it('refuses to cancel links on a shipment the user cannot see', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($b);

    $shipment = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole();

    CompanyHelper::actingAsCompanyUser($a, ['send_pod_link_logistics_shipment']);

    expect(fn () => app(StopLinkService::class)->revokeForShipment($shipment))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);

    asGuest();

    // Still usable: another company's attempt to revoke it did nothing.
    $this->get($url)->assertOk();
});

it('treats an expired link as nothing left to cancel', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop] = issuedLink($company);

    StopLink::withoutGlobalScope(CompanyScope::class)->sole()
        ->forceFill(['expires_at' => now()->subMinute()])->save();

    $shipment = Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole();

    expect(app(StopLinkService::class)->hasLiveLink($shipment))->toBeFalse();
});

it('refuses cleanly when the shipment stopped being deliverable', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    [$stop, , $url] = issuedLink($company);

    // Ordinary sequence: the link was issued, then the shipment was put on hold
    // or cancelled, or someone else delivered it.
    Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()
        ->forceFill(['state' => ShipmentState::ON_HOLD])->save();

    asGuest();

    // The driver gets the same refusal as an expired link, not a 500 - and is
    // not told the shipment was withdrawn, which is not theirs to learn.
    $this->post($url, [
        'recipient_name' => 'Ama Mensah',
        'photo'          => UploadedFile::fake()->image('door.jpg'),
    ])->assertNotFound();

    $link = StopLink::withoutGlobalScope(CompanyScope::class)->sole();

    // Nothing was recorded and the link was not spent, so it still works if the
    // shipment goes back out for delivery.
    expect($link->used_at)->toBeNull()
        ->and(Shipment::withoutGlobalScopes()->whereKey($stop->shipment_id)->sole()->deliveryProofs()->count())->toBe(0);
});
