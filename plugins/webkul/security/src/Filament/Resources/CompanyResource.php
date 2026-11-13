<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\CompanyAdminContributions;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Security\Filament\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers\UsersRelationManager;
use Webkul\Security\Models\Company;
use Webkul\Security\Models\User;
use Webkul\Security\Services\CompanyAdministratorInviter;
use Webkul\Security\Services\CompanyProvisioningStatus;
use Webkul\Security\Services\MultiCompanyAdminService;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CompanyResource as BaseCompanyResource;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;

class CompanyResource extends BaseCompanyResource
{
    protected static ?string $model = Company::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    /**
     * The base opts out of discovery so this class alone owns the company
     * routes; see the base's $isDiscovered (2026-11-11).
     */
    protected static bool $isDiscovered = true;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->ownership()
            ->withoutGlobalScope(AllowedCompanyScope::class);

        $user = Auth::user();

        if (! $user instanceof User) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin()) {
            return $query;
        }

        $companyIds = app(MultiCompanyAdminService::class)->assignedCompanyIds($user);

        return $companyIds === []
            ? $query->whereRaw('1 = 0')
            : $query->whereIn('companies.id', $companyIds);
    }

    /**
     * Setup status, its filter and "Resend invitation" (CR-7, 2026-11-13), so
     * staff can see which new companies still have nobody able to sign in.
     * Installation staff only, like Users and logins: a tenant has no use for
     * other companies' onboarding state. Every value comes from
     * CompanyProvisioningStatus, so the column and the filter cannot disagree.
     */
    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        if (! CompanyAdminContributions::administersInstallation()) {
            return $table;
        }

        $lang = 'security::filament/resources/company.provisioning';

        return $table
            ->pushColumns([
                TextColumn::make('provisioning_status')
                    ->label(__($lang.'.column'))
                    ->state(fn (Company $record): string => app(CompanyProvisioningStatus::class)->status($record))
                    ->formatStateUsing(fn (string $state): string => __($lang.'.statuses.'.$state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        CompanyProvisioningStatus::ACTIVE  => 'success',
                        CompanyProvisioningStatus::INVITED => 'warning',
                        CompanyProvisioningStatus::CLOSED  => 'gray',
                        default                            => 'danger',
                    }),
            ])
            ->pushFilters([
                SelectFilter::make('provisioning_status')
                    ->label(__($lang.'.filter'))
                    ->multiple()
                    ->options(collect(CompanyProvisioningStatus::ALL)
                        ->mapWithKeys(fn (string $status): array => [$status => __($lang.'.statuses.'.$status)])
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        $statuses = array_values(array_filter((array) ($data['values'] ?? [])));

                        if ($statuses === []) {
                            return $query;
                        }

                        // Status is derived per company (see the service), so
                        // the matching ids are worked out over the companies
                        // this actor may list, then applied as a plain filter.
                        return $query->whereIn('companies.id', app(CompanyProvisioningStatus::class)
                            ->idsWithStatus(static::getEloquentQuery()->withTrashed()->get(), $statuses));
                    }),
            ])
            ->pushRecordActions([
                Action::make('resendAdministratorInvitation')
                    ->label(__($lang.'.resend.label'))
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (Company $record): bool => app(CompanyProvisioningStatus::class)->status($record) === CompanyProvisioningStatus::INVITED)
                    ->requiresConfirmation()
                    ->modalHeading(__($lang.'.resend.heading'))
                    ->modalDescription(fn (Company $record): string => __($lang.'.resend.description', [
                        'email' => app(CompanyProvisioningStatus::class)->resendableInvitation($record)?->email ?? '—',
                    ]))
                    ->action(function (Company $record) use ($lang): void {
                        $actor = Auth::user();
                        $invitation = app(CompanyProvisioningStatus::class)->resendableInvitation($record);

                        // The inviter re-checks who may manage the invitation
                        // and that it is still pending; visibility is only UX.
                        $reason = $actor instanceof User && $invitation
                            ? app(CompanyAdministratorInviter::class)->resend($invitation, $actor)
                            : CompanyAdministratorInviter::INVITE_FAILED;

                        Notification::make()
                            ->title(__($lang.'.resend.'.$reason))
                            ->{$reason === CompanyAdministratorInviter::INVITED ? 'success' : 'danger'}()
                            ->send();
                    }),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/company.navigation.title');
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
            __('security::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function getRelations(): array
    {
        return [
            BranchesRelationManager::class,
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view'   => ViewCompany::route('/{record}'),
            'edit'   => EditCompany::route('/{record}/edit'),
        ];
    }
}
