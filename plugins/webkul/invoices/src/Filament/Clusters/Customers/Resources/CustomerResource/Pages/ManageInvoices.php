<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CreditNoteResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Traits\HasRecordNavigationTabs;

/**
 * The customer invoices tab on every customer and contact screen (user,
 * 2026-11-22, adopted with UF-028 from upstream dd55acdc3). The customer
 * screens showed Bills and Purchases but nothing the customer was invoiced.
 *
 * Two guards, as on the Bills tab: the company must have Invoices, and the
 * user must be allowed to list invoices (the list screen's own canViewAny(),
 * so no second rule exists). The relation holds customer invoices and credit
 * notes only, never bills or entries.
 */
class ManageInvoices extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = CustomerResource::class;

    // Registered on the base contact model by InvoiceServiceProvider, so the
    // tab works on every contact screen.
    protected static string $relationship = 'customerInvoices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function canAccess(array $parameters = []): bool
    {
        return PluginEntitlement::enabledForCurrent('invoices')
            && InvoiceResource::canViewAny()
            && parent::canAccess($parameters);
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/customers.navigation.in-sales');
    }

    public function table(Table $table): Table
    {
        return InvoiceResource::table($table)
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Move $record) => static::documentUrl($record, 'view'))
                    ->openUrlInNewTab(false),
                EditAction::make()
                    ->url(fn (Move $record) => static::documentUrl($record, 'edit'))
                    ->openUrlInNewTab(false),
            ]);
    }

    /** A credit note opens in its own screen (upstream 4af5f31bc). */
    protected static function documentUrl(Move $record, string $page): string
    {
        $resource = $record->move_type === MoveType::OUT_REFUND ? CreditNoteResource::class : InvoiceResource::class;

        return $resource::getUrl($page, ['record' => $record]);
    }
}
