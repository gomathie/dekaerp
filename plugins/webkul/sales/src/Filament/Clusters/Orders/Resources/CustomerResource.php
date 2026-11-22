<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\CustomerResource as BaseCustomerResource;
use Webkul\PluginManager\Package;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\CreateCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\EditCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageAddresses;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageBankAccounts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageBills;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageContacts;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ManagePurchases;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Pages\ViewCustomer;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource\Tables\CustomersTable;
use Webkul\Sale\Models\Partner;

class CustomerResource extends BaseCustomerResource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = true;

    protected static ?string $cluster = Orders::class;

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/customer.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/customer.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewCustomer::class,
            EditCustomer::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ];

        // Purchases and bills on every contact screen (unified contacts, user
        // 2026-11-11). The pages check the company switch and the user's
        // Purchases permissions themselves.
        // What the customer was invoiced (user, 2026-11-22). The page checks
        // the Invoices switch and the invoice permission itself.
        if (Package::isPluginInstalled('invoices')) {
            $items[] = ManageInvoices::class;
        }

        if (Package::isPluginInstalled('purchases')) {
            $items[] = ManagePurchases::class;
            $items[] = ManageBills::class;
        }

        return $page->generateNavigationItems($items);
    }

    public static function getPages(): array
    {
        $pages = [
            'index'        => ListCustomers::route('/'),
            'create'       => CreateCustomer::route('/create'),
            'view'         => ViewCustomer::route('/{record}'),
            'edit'         => EditCustomer::route('/{record}/edit'),
            'contacts'     => ManageContacts::route('/{record}/contacts'),
            'addresses'    => ManageAddresses::route('/{record}/addresses'),
            'bank-account' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];

        if (Package::isPluginInstalled('invoices')) {
            $pages['invoices'] = ManageInvoices::route('/{record}/invoices');
        }

        if (Package::isPluginInstalled('purchases')) {
            $pages['purchases'] = ManagePurchases::route('/{record}/purchases');
            $pages['bills'] = ManageBills::route('/{record}/bills');
        }

        return $pages;
    }
}
