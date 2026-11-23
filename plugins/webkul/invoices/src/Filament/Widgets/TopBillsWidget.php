<?php

namespace Webkul\Invoice\Filament\Widgets;

use BackedEnum;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Filament\Widgets\Concerns\GuardsInvoiceDashboardWidget;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasDashboardEmptyState;

class TopBillsWidget extends BaseWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 3];

    use GuardsInvoiceDashboardWidget, HasInvoiceDashboardFilters;
    use HasDashboardEmptyState;

    protected static ?int $sort = 8;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        // DB-3 (2026-11-22): an empty list offers the one next step.
        return $this->withDashboardEmptyState($table, 'bill', BillResource::canCreate() ? BillResource::getUrl('create') : null)
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            ->recordUrl(fn ($record): ?string => $record && BillResource::canAccess()
                ? BillResource::getUrl('view', ['record' => $record])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.top-bills.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->vendorBills()
            ->with(['partner', 'invoiceUser', 'currency'])
            ->where('amount_total', '>', 0)
            ->orderByRaw('ABS(amount_total_signed) DESC')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-bills.columns.reference'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->partner?->name ?: '—'),

            TextColumn::make('invoice_date')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-bills.columns.date'))
                ->date()
                ->color('gray'),

            TextColumn::make('payment_state')
                ->label(__('invoices::filament/widgets/invoice-dashboard.payment-state'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'paid'           => 'success',
                    'partial'        => 'warning',
                    'not_paid'       => 'danger',
                    'reversed'       => 'info',
                    default          => 'gray',
                }),

            TextColumn::make('amount_total')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-bills.columns.amount'))
                ->money(fn ($record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
