<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\EditCompany;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Webkul\Support\Filament\Resources\CompanyResource\Pages\ViewCompany;
use Webkul\Support\Filament\Resources\CompanyResource\RelationManagers\BranchesRelationManager;
use Webkul\Support\Filament\Resources\CompanyResource\Schemas\CompanyForm;
use Webkul\Support\Filament\Resources\CompanyResource\Schemas\CompanyInfolist;
use Webkul\Support\Filament\Resources\CompanyResource\Tables\CompaniesTable;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\AllowedCompanyScope;

class CompanyResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Company::class;

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    /**
     * Not registered with the panel: Security (a core plugin) registers its
     * subclass on the same slug. With both registered, this base won the
     * /admin/companies routes, so Security's create and edit pages never ran
     * and the Administrator section's email and password were silently
     * dropped - no administrator or invitation was ever created in
     * production (2026-11-11). Pinned by security CompanyRouteTest.
     */
    protected static bool $isDiscovered = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->ownership()
            ->withoutGlobalScope(AllowedCompanyScope::class);
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/company.navigation.title');
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
            __('support::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            BranchesRelationManager::class,
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

    public static function isArchivedTab($livewire = null): bool
    {
        if (! is_object($livewire) || ! property_exists($livewire, 'activeTab')) {
            return false;
        }

        return $livewire->activeTab === 'archived';
    }

    public static function cancelIfDefaultCompany(int|array $ids, $action): void
    {
        $ids = (array) $ids;

        if (! in_array(settings(UserSettings::class)->default_company_id, $ids)) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('support::filament/resources/company.table.actions.delete.notification.default-company.title'))
            ->body(__('support::filament/resources/company.table.actions.delete.notification.default-company.body'))
            ->send();

        $action->cancel();
    }
}
