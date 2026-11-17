<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Filament\Resources\UserResource\Pages\CreateUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\EditUser;
use Webkul\Security\Filament\Resources\UserResource\Pages\ListUsers;
use Webkul\Security\Filament\Resources\UserResource\Pages\ViewUsers;
use Webkul\Security\Filament\Resources\UserResource\Schemas\UserForm;
use Webkul\Security\Filament\Resources\UserResource\Schemas\UserInfolist;
use Webkul\Security\Filament\Resources\UserResource\Tables\UsersTable;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Security\Services\TenantImpersonation;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Enums\NavigationGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return app(MultiCompanyAdminService::class)->scopeManageableUsers($query, $user);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/user.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/user.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    /**
     * Shows the impersonation grant actions. Only to an active Super Admin
     * who is not impersonating; TenantImpersonation::grant() re-checks.
     */
    public static function managesImpersonationGrants(): bool
    {
        $actor = Auth::user();

        return $actor instanceof User
            && $actor->is_active
            && $actor->isSuperAdmin()
            && ! app(TenantImpersonation::class)->isActive();
    }

    public static function canDeleteUser(User $record): bool
    {
        $actor = Auth::user();

        if (! $actor instanceof User || $record->is_default || $record->id === $actor->id) {
            return false;
        }

        if (! $actor->isSuperAdmin() && ($record->isSuperAdmin() || $record->isMultiCompanyAdmin())) {
            return false;
        }

        return ! $actor->isMultiCompanyAdmin()
            || app(MultiCompanyAdminService::class)->canManageUser($actor, $record);
    }

    public static function ensureAdminRoleConstraints(?User $record, array $roleIds): void
    {
        $adminRoleIds = self::getProtectedAdminRoleIds();

        if ($adminRoleIds === []) {
            return;
        }

        $normalizedRoleIds = array_map('intval', $roleIds);
        $isAdminCurrently = $record?->roles()->whereKey($adminRoleIds)->exists() ?? false;
        $willBeAdmin = count(array_intersect($adminRoleIds, $normalizedRoleIds)) > 0;
        $adminUsersCount = User::query()
            ->whereHas('roles', fn (Builder $query) => $query->whereKey($adminRoleIds))
            ->count();

        $firstUserId = User::min('id');
        $isFirstUser = $record && $record->id === $firstUserId;

        if (
            $isFirstUser
            && ! $willBeAdmin
        ) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.first-user-must-be-admin'),
            ]);
        }

        if (
            $adminUsersCount === 1
            && $isAdminCurrently
            && ! $willBeAdmin
        ) {
            throw ValidationException::withMessages([
                'roles' => __('security::filament/resources/user.form.validation.cannot-remove-last-admin'),
            ]);
        }
    }

    protected static function getProtectedAdminRoleIds(): array
    {
        $defaultRoleId = settings(UserSettings::class)->default_role_id;

        $roleIdsFromNames = Role::query()
            ->get(['id', 'name'])
            ->filter(fn (Role $role): bool => $role->isSuperAdminRole())
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();

        return collect($roleIdsFromNames)
            ->when($defaultRoleId, fn ($collection) => $collection->push((int) $defaultRoleId))
            ->unique()
            ->values()
            ->all();
    }

    public static function ensureUserAssignmentConstraints(
        ?User $record,
        array $roleIds,
        array $companyIds,
        ?int $defaultCompanyId,
    ): void {
        $actor = Auth::user();

        abort_unless($actor instanceof User, 403);

        app(MultiCompanyAdminService::class)->assertUserAssignment(
            $actor,
            $record,
            $roleIds,
            $companyIds,
            $defaultCompanyId,
        );
    }

    public static function scopeAssignableCompanies(Builder $query): Builder
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return app(MultiCompanyAdminService::class)->scopeAssignableCompanies($query, $actor);
    }

    /**
     * Roles an actor may hand out, as Filament select options.
     *
     * @return array<int, string>
     */
    public static function assignableRoleOptions(): array
    {
        return static::scopeAssignableRoles(Role::query())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * Every role the user holds today, from both stores, so editing a user does
     * not present a half-filled role list and then save it back.
     *
     * @return array<int, int>
     */
    public static function currentRoleIds(User $record): array
    {
        return app(MultiCompanyAdminService::class)->effectiveRoleIds($record);
    }

    public static function scopeAssignableRoles(Builder $query): Builder
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        return app(MultiCompanyAdminService::class)->scopeAssignableRoles($query, $actor);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
            'view'   => ViewUsers::route('/{record}'),
        ];
    }
}
