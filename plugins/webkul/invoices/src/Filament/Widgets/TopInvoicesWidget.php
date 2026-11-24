<?php

namespace Webkul\Invoice\Filament\Widgets;

use BackedEnum;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Widgets\Concerns\GuardsInvoiceDashboardWidget;
use Webkul\Invoice\Filament\Widgets\Concerns\HasInvoiceDashboardFilters;
use Webkul\Support\Filament\Concerns\HasDashboardEmptyState;

class TopInvoicesWidget extends BaseWidget
{
    // DB-4 layout on the Overview's six-column grid (2026-11-23).
    protected int|string|array $columnSpan = ['default' => 'full', 'xl' => 3];

    use GuardsInvoiceDashboardWidget, HasInvoiceDashboardFilters;
    use HasDashboardEmptyState;

    protected static ?int $sort = 7;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        // DB-3 (2026-11-22): an empty list offers the one next step.
        return $this->withDashboardEmptyState($table, 'invoice', InvoiceResource::canCreate() ? InvoiceResource::getUrl('create') : null)
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            // "View all" opens the whole list (DB-9).
            ->headerActions($this->viewAllActions(InvoiceResource::canViewAny() ? InvoiceResource::getUrl('index') : null))
            ->recordUrl(fn ($record): ?string => $record && InvoiceResource::canAccess()
                ? InvoiceResource::getUrl('view', ['record' => $record])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('invoices::filament/widgets/invoice-dashboard.top-invoices.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->customerInvoices()
            ->with(['partner', 'invoiceUser', 'currency'])
            ->where('amount_total', '>', 0)
            ->orderByDesc('amount_total_signed')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.reference'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->partner?->name ?: '—'),

            TextColumn::make('invoice_date')
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.date'))
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
                ->label(__('invoices::filament/widgets/invoice-dashboard.top-invoices.columns.amount'))
                ->money(fn ($record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
