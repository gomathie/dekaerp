<?php

use Webkul\Security\Mail\UserInvitationMail;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * The invitation is often the first thing a client sees from DEKA, so it must
 * be recognisable (logo, company, inviter) and must not render user-typed
 * names as HTML.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

function brandedInvitation(Company $company, ?User $inviter = null): Invitation
{
    return Invitation::query()->create([
        'email'              => fake()->unique()->safeEmail(),
        'default_company_id' => $company->getKey(),
        'company_ids'        => [$company->getKey()],
        'role_ids'           => [],
        'inviter_id'         => $inviter?->getKey(),
        'expires_at'         => now()->addDays(Invitation::ACCEPT_WINDOW_DAYS),
    ]);
}

it('names the company and inviter, shows the logo and a plain link', function () {
    $company = Company::factory()->create(['name' => 'Acme Freight']);
    $inviter = User::factory()->create(['name' => 'Ama Mensah']);

    $mail = new UserInvitationMail(brandedInvitation($company, $inviter));
    $html = $mail->render();

    expect($mail->envelope()->subject)->toContain('Acme Freight')
        ->and($html)->toContain('Ama Mensah has invited you to join Acme Freight.')
        ->and($html)->toContain('images/icons/icon-192.png')
        ->and($html)->toContain('#d97706')
        ->and($html)->toContain('This invitation expires on')
        ->and($html)->toContain('If the button does not work')
        ->and($html)->not->toContain('<pre')
        ->and($html)->not->toContain('<code');
});

it('escapes a company name typed as HTML', function () {
    $company = Company::factory()->create(['name' => '<b onmouseover=x>Evil</b>']);

    $html = (new UserInvitationMail(brandedInvitation($company)))->render();

    expect($html)->not->toContain('<b onmouseover=x>')
        ->and($html)->toContain('You have been invited to join');
});
