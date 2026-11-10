<?php

namespace Webkul\PlatformBilling\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Models\Message;
use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

/**
 * Billing writes already land in the chatter audit log. This page only reads
 * those rows. CompanyScope stays on, so one selected company does not become
 * every tenant's history.
 */
class BillingAudit extends Page implements HasTable
{
    use ChecksPagePermission;
    use HasPageShield;
    use InteractsWithTable;

    protected static ?string $cluster = Billing::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 6;

    protected string $view = 'platform-billing::filament.pages.table';

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.audit.navigation');
    }

    public function getTitle(): string
    {
        return __('platform-billing::filament/billing.audit.title');
    }

    /**
     * The cluster check is not this page's permission. A canAccess() replaces
     * HasPageShield, so the generated page permission has to be checked here
     * or the audit opens for every Super Admin regardless of the role.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User
            && $user->is_active
            && $user->isSuperAdmin()
            && static::hasPagePermission();
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_platform_billing_billing_audit';
    }

    public function summaryLines(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Message::query()
                ->where('log_name', 'security')
                ->where('event', 'like', 'platform_billing.%'))
            ->columns([
                TextColumn::make('event')
                    ->label(__('platform-billing::filament/billing.audit.columns.event'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('platform-billing::filament/billing.audit.columns.when'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->emptyStateHeading(__('platform-billing::filament/billing.audit.empty'));
    }
}
