<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\Concerns\ShownOnContactScreens;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageBills extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;
    use ShownOnContactScreens;

    protected static string $resource = VendorResource::class;

    // Registered on the base contact model by PurchaseServiceProvider, so the
    // tab works on every contact screen. Not "accountMoves": other contact
    // models may define that name differently.
    protected static string $relationship = 'vendorBills';

    protected static function listResourceAllowsViewing(): bool
    {
        return BillResource::canViewAny();
    }

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/orders/resources/vendor/pages/manage-bills.navigation.title');
    }

    public function table(Table $table): Table
    {
        return BillResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->where('partner_id', $this->record->getKey()))
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => BillResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(false),

                EditAction::make()
                    ->url(fn ($record) => BillResource::getUrl('edit', ['record' => $record]))
                    ->openUrlInNewTab(false),
            ]);
    }
}
