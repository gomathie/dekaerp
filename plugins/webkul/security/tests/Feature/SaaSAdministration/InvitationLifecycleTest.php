<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyAdminRoleProvisioner;
use Webkul\Security\Services\UserInvitationService;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

/**
 * SAAS-07: an invitation that can be sent again, and only while it is pending.
 *
 * The gap: when the first mail failed, the only route to the customer was
 * revoke-and-re-invite, which threw the row away. Renegotiating the window is
 * deliberate - re-sending a link that expires in ten minutes looks like it
 * worked and then fails for the recipient.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();

    Auth::forgetGuards();
    app(CompanyAdminRoleProvisioner::class)->provision();

    settings(UserSettings::class)->enable_user_invitation = true;
    settings(UserSettings::class)->save();

    Mail::fake();
});

afterEach(function (): void {
    Auth::forgetGuards();
});

function lifecycleInvitation(Company $company, array $attributes = []): Invitation
{
    return Invitation::query()->create([
        'email'              => fake()->unique()->safeEmail(),
        'default_company_id' => $company->getKey(),
        'company_ids'        => [$company->getKey()],
        'role_ids'           => [],
        'expires_at'         => now()->addHour(),
        ...$attributes,
    ]);
}

it('renews a pending invitation and extends its window', function () {
    $company = Company::factory()->create(['is_active' => true, 'seat_limit' => null]);
    $actor = FilamentHelper::actingAs();

    $invitation = lifecycleInvitation($company);

    $before = $invitation->expires_at->copy();

    $renewed = app(UserInvitationService::class)->renew($invitation, $actor);

    expect($renewed->expires_at->greaterThan($before))->toBeTrue()
        ->and($renewed->expires_at->isAfter(now()->addDays(Invitation::ACCEPT_WINDOW_DAYS)->subMinute()))->toBeTrue()
        ->and(Invitation::query()->count())->toBe(1);
});

it('refuses to renew an accepted or revoked invitation', function () {
    $company = Company::factory()->create(['is_active' => true, 'seat_limit' => null]);
    $actor = FilamentHelper::actingAs();

    $accepted = lifecycleInvitation($company, ['accepted_at' => now()]);
    $revoked = lifecycleInvitation($company, ['revoked_at' => now()]);

    $service = app(UserInvitationService::class);

    expect(fn () => $service->renew($accepted, $actor))->toThrow(ValidationException::class);
    expect(fn () => $service->renew($revoked, $actor))->toThrow(ValidationException::class);
});

it('resends a pending invitation and reports the outcome instead of throwing', function () {
    $company = Company::factory()->create(['is_active' => true, 'seat_limit' => null]);
    $actor = FilamentHelper::actingAs();

    $invitation = lifecycleInvitation($company);

    $reason = app(CompanyAdministratorInviter::class)->resend($invitation, $actor);

    expect($reason)->toBe(CompanyAdministratorInviter::INVITED);

    // `hasTo()` rather than the invitation property: the mailable keeps it
    // private, and asserting on the recipient is the part that matters here.
    Mail::assertSent(UserInvitationMail::class, fn (UserInvitationMail $mail): bool => $mail->hasTo($invitation->email));
});

it('reports a resend of an invitation that is no longer pending as a failure', function () {
    $company = Company::factory()->create(['is_active' => true, 'seat_limit' => null]);
    $actor = FilamentHelper::actingAs();

    $revoked = lifecycleInvitation($company, ['revoked_at' => now()]);

    $reason = app(CompanyAdministratorInviter::class)->resend($revoked, $actor);

    expect($reason)->toBe(CompanyAdministratorInviter::INVITE_FAILED);

    Mail::assertNothingSent();
});
