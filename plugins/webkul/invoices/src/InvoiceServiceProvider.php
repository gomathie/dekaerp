<?php

namespace Webkul\Invoice;

use Filament\Panel;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Widgets\InvoiceHeadlineWidget;
use Webkul\Invoice\Livewire\InvoiceSummary;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class InvoiceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'invoices';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasDependencies([
                'accounts',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('invoices');
    }

    public function packageBooted(): void
    {
        Livewire::component('invoice-invoice-summary', InvoiceSummary::class);

        // The Invoices section of the combined Overview dashboard. Keyed, so
        // a second packageBooted() replaces it rather than adding a copy.
        OverviewRegistry::register(
            'invoices',
            fn (): string => __('invoices::filament/pages/overview.sections.invoices'),
            Invoices::sectionWidgets(),
            fn (): array => Invoices::sectionFilterFields(),
            fn (): bool  => Invoices::canAccess(),
            sort: 30,
            headline: InvoiceHeadlineWidget::class,
        );
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(InvoicePlugin::make());
        });
    }
}
