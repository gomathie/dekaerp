<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Webkul\Security\Models\Role;
use Webkul\Support\Enums\GuideAudience;
use Webkul\Support\Filament\Pages\Guide;
use Webkul\Support\Services\GuideLibrary;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/FilamentHelper.php';

/**
 * Three guides in one page, separated by who the reader is (user, 2026-10-23:
 * "We should have admin guide and user guide, and also superadmin guide").
 *
 * The guide has no policy and no API of its own, so GuideLibrary::visible() is
 * the entire access boundary. These tests exercise it from both directions:
 * what each kind of reader is offered, and what happens when someone asks for
 * content they are not entitled to by putting it in the URL.
 */
const ADMIN_CATEGORY = 'company-administration';

const SUPERADMIN_CATEGORY = 'installation-administration';

const ADMIN_ARTICLE = 'admin-users';

const SUPERADMIN_ARTICLE = 'superadmin-roles';

/**
 * @return array<int, string>
 */
function guideCategoryIds(): array
{
    return array_column(app(GuideLibrary::class)->visible(), 'id');
}

function assignRoleByName(object $user, string $name): void
{
    // Company Admin is deliberately not a protected system role, so it can be
    // created here. super_admin is, and creating one would throw, so it must
    // already exist - which the assertion makes explicit rather than skipping.
    $role = Role::query()->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();

    if (! $role && mb_strtolower($name) === mb_strtolower(Role::COMPANY_ADMIN)) {
        $role = Role::create(['name' => Role::COMPANY_ADMIN, 'guard_name' => 'web']);
    }

    expect($role)->not->toBeNull("the '{$name}' role does not exist, so this test would prove nothing");

    $user->assignRole($role);
    $user->unsetRelation('roles');

    // Re-seat the user on both guards by name. The helper authenticates through
    // sanctum with Sanctum::actingAs, so the instance auth() resolves is not
    // guaranteed to be the one the role was just assigned to - and a guide that
    // looked gated only because the role had not landed would be a false pass.
    Auth::guard('web')->setUser($user);
    Auth::guard('sanctum')->setUser($user);

    expect(auth()->user()?->getRoleNames()->map(fn (string $n): string => mb_strtolower($n))->all())
        ->toContain(mb_strtolower($name));
}

it('ships all three guides, so the tests below are not asserting against absence', function () {
    // Without this, every "cannot see the admin guide" assertion would pass on
    // a build where the admin guide simply was not shipped.
    FilamentHelper::actingAs();

    $all = app(GuideLibrary::class)->categories();
    $ids = array_column($all, 'id');
    $audiences = array_unique(array_column($all, 'audience'));

    expect($ids)->toContain(ADMIN_CATEGORY)
        ->and($ids)->toContain(SUPERADMIN_CATEGORY)
        ->and($audiences)->toContain('user', 'admin', 'superadmin');
});

it('offers an ordinary user the user guide only', function () {
    FilamentHelper::actingAs();

    expect(app(GuideLibrary::class)->audiences())->toBe([GuideAudience::User])
        ->and(guideCategoryIds())->not->toContain(ADMIN_CATEGORY)
        ->and(guideCategoryIds())->not->toContain(SUPERADMIN_CATEGORY);
});

it('keeps administrative articles unreachable for an ordinary user', function () {
    FilamentHelper::actingAs();

    $library = app(GuideLibrary::class);

    // By id: a shared deep link must not resolve.
    expect($library->article(ADMIN_ARTICLE))->toBeNull()
        ->and($library->article(SUPERADMIN_ARTICLE))->toBeNull();

    // By search: the title must not even be listed, or the summary leaks.
    $hits = array_merge(
        $library->search('Letterhead'),
        $library->search('Administrator Roles'),
    );

    expect(array_column($hits, 'categoryId'))
        ->not->toContain(ADMIN_CATEGORY)
        ->not->toContain(SUPERADMIN_CATEGORY);
});

it('offers a company admin the user and admin guides but not the superadmin one', function () {
    $user = FilamentHelper::actingAs();
    assignRoleByName($user, Role::COMPANY_ADMIN);

    $library = app(GuideLibrary::class);

    expect($library->audiences())->toBe([GuideAudience::User, GuideAudience::Admin])
        ->and(guideCategoryIds())->toContain(ADMIN_CATEGORY)
        ->and(guideCategoryIds())->not->toContain(SUPERADMIN_CATEGORY)
        ->and($library->article(ADMIN_ARTICLE))->not->toBeNull()
        ->and($library->article(SUPERADMIN_ARTICLE))->toBeNull();
});

it('offers a super admin every guide', function () {
    $user = FilamentHelper::actingAs();
    assignRoleByName($user, 'super_admin');

    $library = app(GuideLibrary::class);

    expect($library->audiences())
        ->toBe([GuideAudience::User, GuideAudience::Admin, GuideAudience::SuperAdmin])
        ->and(guideCategoryIds())->toContain(SUPERADMIN_CATEGORY)
        ->and($library->article(SUPERADMIN_ARTICLE))->not->toBeNull();
});

it('discards an audience in the URL that the viewer may not read', function () {
    FilamentHelper::actingAs();

    // ?audience=superadmin in a link shared by an administrator must not open
    // the installation guide for an ordinary user.
    Livewire::test(Guide::class, ['audience' => GuideAudience::SuperAdmin->value])
        ->assertOk()
        ->assertSet('audience', GuideAudience::User->value);
});

it('falls back to browsing when a deep-linked article is out of reach', function () {
    FilamentHelper::actingAs();

    Livewire::test(Guide::class, ['articleId' => SUPERADMIN_ARTICLE])
        ->assertOk()
        ->assertSet('articleId', null)
        ->assertSet('audience', GuideAudience::User->value);
});

it('moves a company admin to the audience of the article they open', function () {
    $user = FilamentHelper::actingAs();
    assignRoleByName($user, Role::COMPANY_ADMIN);

    Livewire::test(Guide::class)
        ->assertSet('audience', GuideAudience::User->value)
        ->call('selectArticle', ADMIN_ARTICLE)
        ->assertSet('articleId', ADMIN_ARTICLE)
        ->assertSet('audience', GuideAudience::Admin->value)
        ->assertSet('categoryId', ADMIN_CATEGORY);
});

it('shows a company admin the admin guide card on the help page', function () {
    $user = FilamentHelper::actingAs();
    assignRoleByName($user, Role::COMPANY_ADMIN);

    // Was super-admin-only, so the card was hidden from the people it is for.
    expect(app(\Webkul\Support\Filament\Pages\Help::class)->canViewAdminGuide())->toBeTrue();
});
