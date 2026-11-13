<?php

namespace Webkul\Invoice;

use Filament\Panel;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Overview\InvoiceAttention;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Invoice\Filament\Pages\Invoices;
use Webkul\Invoice\Filament\Widgets\InvoiceHeadlineWidget;
use Webkul\Invoice\Livewire\InvoiceSummary;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Support\Filament\Contributions\DashboardBannerRegistry;

class InvoiceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'invoices';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasTranslations()
            ->hasViews()
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
            attention: fn (): array => InvoiceAttention::items(),
            actions: fn (): array => InvoiceAttention::actions(),
        );

        // The greeting's "today" line and quick actions come from the same
        // registry the Needs attention list reads (support cannot name
        // Invoices, so the contribution goes this way round).
        DashboardBannerRegistry::summary('overview', fn (): array => array_map(
            fn (array $item): string => $item['label'],
            OverviewRegistry::attentionItems(),
        ));

        DashboardBannerRegistry::actions('overview', fn (): array => OverviewRegistry::quickActions());
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(InvoicePlugin::make());
        });
    }
}
