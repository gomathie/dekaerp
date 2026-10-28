<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Webkul\Security\Models\Role;
use Webkul\Support\Exceptions\MissingCompanyContextException;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\Traits\BelongsToCompany;

require_once __DIR__.'/../../Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/SecurityHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * SAAS-02: an execution context that says which company the work is for.
 *
 * Two gaps this closes. Queued work and console commands had no way to declare
 * their company: `CompanyScope` is skipped outside tests in console execution,
 * so a job read across every tenant and wrote rows whose `company_id` came from
 * whatever happened to be in the session - usually nothing. And an API client
 * could not address any company but its default, because the session it would
 * have read does not exist on a token request.
 */
beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();

    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
});

afterEach(function (): void {
    Auth::forgetGuards();
    session()->forget(CompanyContext::SESSION_KEY);
    app()->forgetInstance(CompanyContext::class);
});

/**
 * A tenant model that does not opt out of company stamping - every support model
 * overrides `autoAssignsCompany()` to false, so the trait's own behaviour needs
 * its own fixture.
 */
function tenantContextProbeModel(): Model
{
    return new class extends Model
    {
        use BelongsToCompany;

        protected $table = 'calendars';

        protected $fillable = ['name', 'timezone', 'company_id'];
    };
}

it('runs a callback under an explicit company and restores the previous context', function () {
    $companyA = CompanyHelper::company(['name' => 'Context A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Context B', 'is_active' => true]);

    // A member of both, with A active: the forced company has to be one the
    // actor may use, or membership (rightly) wins and the context is empty.
    CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]);

    expect(app(CompanyContext::class)->currentId())->toBe((int) $companyA->getKey());

    $inside = app(CompanyContext::class)->runFor(
        (int) $companyB->getKey(),
        fn (): ?int => app(CompanyContext::class)->currentId(),
    );

    expect($inside)->toBe((int) $companyB->getKey())
        ->and(app(CompanyContext::class)->currentId())->toBe((int) $companyA->getKey());
});

it('restores the previous context when the callback throws', function () {
    $companyA = CompanyHelper::company(['name' => 'Context Restore A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Context Restore B', 'is_active' => true]);

    CompanyHelper::actingAsCompanyUser([$companyA, $companyB], [], [$companyA->getKey()]);

    $seenInside = null;

    expect(function () use ($companyB, &$seenInside): void {
        app(CompanyContext::class)->runFor(
            (int) $companyB->getKey(),
            function () use (&$seenInside): void {
                $seenInside = app(CompanyContext::class)->currentId();

                throw new RuntimeException('job failed');
            },
        );
    })->toThrow(RuntimeException::class);

    // Without the `finally` the rest of the worker keeps running as company B.
    // The inside check proves the switch really happened, so "A afterwards" is a
    // restore and not a switch that never took place.
    expect($seenInside)->toBe((int) $companyB->getKey())
        ->and(app(CompanyContext::class)->currentId())->toBe((int) $companyA->getKey());
});

it('does not hand an acting user a forced company they do not belong to', function () {
    $member = CompanyHelper::company(['name' => 'Context Member', 'is_active' => true]);
    $foreign = CompanyHelper::company(['name' => 'Context Foreign', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser($member, [], [$member->getKey()]);

    $insideForMember = app(CompanyContext::class)->runFor((int) $foreign->getKey(), fn (): ?int => app(CompanyContext::class)->currentId());

    // The same with no membership at all: an empty allow-list is not "nobody is
    // acting", and must not let the forced company through unchecked.
    $user->allowedCompanies()->sync([]);
    app()->forgetInstance(CompanyContext::class);

    $insideForMemberless = app(CompanyContext::class)->runFor((int) $foreign->getKey(), fn (): ?int => app(CompanyContext::class)->currentId());

    expect($insideForMember)->toBeNull()
        ->and($insideForMemberless)->toBeNull();
});

it('stamps the forced company on a tenant write with no session', function () {
    $company = CompanyHelper::company(['name' => 'Context Stamp', 'is_active' => true]);

    $saved = app(CompanyContext::class)->runFor(
        (int) $company->getKey(),
        fn (): bool => tenantContextProbeModel()->newInstance(['name' => 'Job Calendar', 'timezone' => 'UTC'])->save(),
    );

    expect($saved)->toBeTrue();
});

it('fails closed when an acting user has no company', function (): void {
    $user = SecurityHelper::authenticateWithPermissions([]);

    $user->forceFill(['default_company_id' => null])->saveQuietly();
    $user->allowedCompanies()->sync([]);

    app()->forgetInstance(CompanyContext::class);

    expect(app(CompanyContext::class)->currentId())->toBeNull();

    // A null company is not "no tenant": CompanyScope reads it as *shared*, so
    // the row would appear in every company. Failing here beats a quiet leak.
    expect(fn () => tenantContextProbeModel()->newInstance(['name' => 'Leaky Calendar', 'timezone' => 'UTC'])->save())
        ->toThrow(MissingCompanyContextException::class);
});

it('still allows a write when nobody is acting', function () {
    CompanyHelper::withoutCompanyContext(function (): void {
        // Seeders and installers create tenant rows outside a request; they set
        // the company explicitly where it matters, and must not be blocked.
        expect(tenantContextProbeModel()->newInstance(['name' => 'Seeder Calendar', 'timezone' => 'UTC'])->save())->toBeTrue();
    });
});

it('lets an API client select a company it belongs to', function () {
    $companyA = CompanyHelper::company(['name' => 'Api Company A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Api Company B', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser($companyA, [], [$companyA->getKey()]);

    Route::middleware('api.company')->get('/__api_company_probe', fn () => response()->json([
        'company' => app(CompanyContext::class)->currentId(),
    ]));

    $this->actingAs($user)
        ->getJson('/__api_company_probe', ['X-Company-Id' => $companyA->getKey()])
        ->assertOk()
        ->assertJson(['company' => (int) $companyA->getKey()]);

    // Same user, a company they do not belong to: refused, not silently ignored.
    $this->actingAs($user)
        ->getJson('/__api_company_probe', ['X-Company-Id' => $companyB->getKey()])
        ->assertForbidden();
});

it('does not treat a company_id field as the company selector', function () {
    $companyA = CompanyHelper::company(['name' => 'Api Field A', 'is_active' => true]);
    $companyB = CompanyHelper::company(['name' => 'Api Field B', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser($companyA, [], [$companyA->getKey()]);

    Route::middleware('api.company')->post('/__api_company_field_probe', fn () => response()->json([
        'company' => app(CompanyContext::class)->currentId(),
    ]));

    // Record data that happens to carry a company_id must neither switch the
    // tenant nor be refused as a forged selector; only the header selects.
    $this->actingAs($user)
        ->postJson('/__api_company_field_probe', ['company_id' => $companyB->getKey()])
        ->assertOk()
        ->assertJson(['company' => (int) $companyA->getKey()]);
});

it('leaves an unauthenticated request to the route authentication', function () {
    $company = CompanyHelper::company(['name' => 'Api Guest', 'is_active' => true]);

    Route::middleware(['api.company', 'auth:sanctum'])->get('/__api_company_guest_probe', fn () => response()->json([]));

    // 401, not 403: a client with an expired token must be told to authenticate.
    $this->getJson('/__api_company_guest_probe', ['X-Company-Id' => $company->getKey()])
        ->assertUnauthorized();
});

it('keeps the default company when an API client sends no selector', function () {
    $companyA = CompanyHelper::company(['name' => 'Api Default A', 'is_active' => true]);

    $user = CompanyHelper::actingAsCompanyUser($companyA, [], [$companyA->getKey()]);

    Route::middleware('api.company')->get('/__api_company_default_probe', fn () => response()->json([
        'company' => app(CompanyContext::class)->currentId(),
    ]));

    // The documented compatibility fallback: existing integrations that never
    // send the header keep behaving exactly as they did.
    $this->actingAs($user)
        ->getJson('/__api_company_default_probe')
        ->assertOk()
        ->assertJson(['company' => (int) $companyA->getKey()]);
});

it('lets a user who sees every company select any company', function () {
    $companyB = CompanyHelper::company(['name' => 'Api Admin B', 'is_active' => true]);

    $user = SecurityHelper::authenticateWithPermissions([], bypassCompanyScope: true);

    // Precondition: not a member of B, so only the bypass can make B selectable.
    expect($user->allowedCompanies()->pluck('companies.id')->all())
        ->not->toContain((int) $companyB->getKey());

    Route::middleware('api.company')->get('/__api_company_admin_probe', fn () => response()->json([
        'company' => app(CompanyContext::class)->currentId(),
    ]));

    $this->actingAs($user)
        ->getJson('/__api_company_admin_probe', ['X-Company-Id' => $companyB->getKey()])
        ->assertOk()
        ->assertJson(['company' => (int) $companyB->getKey()]);
});

/**
 * `Admin` is in Role::getSuperAdminRoleNames(), but only `bypass_company_scope`
 * makes every company visible. The selector used to trust the role name, so this
 * request was accepted and then resolved to no company at all (200, company null).
 */
it('refuses an Admin role holder a company outside their memberships', function () {
    $companyB = CompanyHelper::company(['name' => 'Api Admin Outside B', 'is_active' => true]);

    $user = FilamentHelper::actingAs();

    $role = null;

    foreach (Role::getSuperAdminRoleNames() as $name) {
        $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

        if ($role) {
            break;
        }
    }

    expect($role)->not->toBeNull('no super-admin role exists, so this test would prove nothing');

    $user->assignRole($role);
    $user->unsetRelation('roles');

    expect($user->isSuperAdmin())->toBeTrue()
        ->and($user->can('bypass_company_scope'))->toBeFalse('the role sees every company, so this would not test the refusal')
        ->and($user->allowedCompanies()->pluck('companies.id')->all())->not->toContain((int) $companyB->getKey());

    Route::middleware('api.company')->get('/__api_company_admin_outside_probe', fn () => response()->json([
        'company' => app(CompanyContext::class)->currentId(),
    ]));

    $this->actingAs($user)
        ->getJson('/__api_company_admin_outside_probe', ['X-Company-Id' => $companyB->getKey()])
        ->assertForbidden();
});
