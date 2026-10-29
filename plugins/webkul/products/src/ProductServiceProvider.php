<?php

namespace Webkul\Product;

use Filament\Panel;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Product\Console\Commands\ProductsCompanyBackfill;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Product\Observers\CompanyObserver;
use Webkul\Product\Observers\UOMObserver;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

class ProductServiceProvider extends PackageServiceProvider
{
    public static string $name = 'products';

    public static string $viewNamespace = 'products';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasRoutes(['api'])
            ->hasMigrations([
                '2025_01_05_063925_create_products_categories_table',
                '2025_01_05_100751_create_products_products_table',
                '2025_01_05_100830_create_products_tags_table',
                '2025_01_05_100832_create_products_product_tag_table',
                '2025_01_05_104456_create_products_attributes_table',
                '2025_01_05_104512_create_products_attribute_options_table',
                '2025_01_05_104759_create_products_product_attributes_table',
                '2025_01_05_104809_create_products_product_attribute_values_table',
                '2025_01_05_105626_create_products_packagings_table',
                '2025_01_05_113357_create_products_price_rules_table',
                '2025_01_05_113402_create_products_price_rule_items_table',
                '2025_01_05_123412_create_products_product_suppliers_table',
                '2025_02_18_112837_create_products_product_price_lists_table',
                '2025_02_21_053249 _create_products_product_combinations_table',
                '2025_07_28_080116_alter_products_products_table',
                '2026_04_15_044431_add_columns_in_products_product_suppliers_table',
                '2026_10_29_120000_add_company_id_to_products_categories_and_tags_tables',
            ])
            ->hasSeeder('Webkul\\Product\\Database\Seeders\\DatabaseSeeder')
            ->hasCommand(ProductsCompanyBackfill::class)
            ->runsMigrations()
            ->hasSettings([
                '2025_01_17_094022_create_products_product_settings',
            ])
            ->runsSettings()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Category::class, Product::class]);
                });
            });
    }

    public function packageBooted(): void
    {
        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Company::observe(CompanyObserver::class);

        UOM::observe(UOMObserver::class);
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(ProductPlugin::make());
        });
    }
}
