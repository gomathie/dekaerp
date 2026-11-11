<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\QuotationResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\Concerns\ShownOnContactScreens;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManagePurchases extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;
    use ShownOnContactScreens;

    protected static string $resource = VendorResource::class;

    // Registered on the base contact model by PurchaseServiceProvider. Not
    // "orders": the Sales contact model already uses that name for sales
    // orders, so this tab would list those on a customer screen.
    protected static string $relationship = 'purchaseOrders';

    protected static function listResourceAllowsViewing(): bool
    {
        return QuotationResource::canViewAny();
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/vendor/pages/manage-purchases.navigation.title');
    }

    public function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->where('partner_id', $this->record->getKey()))
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => QuotationResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(fn ($record) => QuotationResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
