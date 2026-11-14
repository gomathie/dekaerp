<?php

namespace Webkul\Accounting;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Webkul\Accounting\Filament\OverviewCard;
use Webkul\Accounting\Filament\Widgets\AccountingHeadlineWidget;
use Webkul\Accounting\Filament\Widgets\JournalChartWidget;
use Webkul\Invoice\Filament\Overview\OverviewRegistry;
use Webkul\Support\Filament\Contributions\DashboardCardRegistry;
use Webkul\Accounting\Livewire\InvoiceSummary;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class AccountingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'accounting';

    public static string $viewNamespace = 'accounting';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies([
                'accounts',
            ])
            ->icon('accounting')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->installDependencies();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {});
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->registerLivewireComponents();

        $this->contributeOverview();
    }

    /**
     * Accounting on the combined Overview (plan A5): its card on the All tab,
     * and a section with headline figures, a "needs attention" item and a
     * quick action. The Overview belongs to Invoices, which Accounting does
     * not depend on, so the section is added only when that registry exists.
     * Both registries are keyed, so a second boot replaces, not duplicates.
     */
    protected function contributeOverview(): void
    {
        DashboardCardRegistry::card(
            'accounting',
            fn (): bool  => OverviewCard::visible(),
            fn (): array => OverviewCard::data(),
            sort: 5,
        );

        if (! class_exists(OverviewRegistry::class)) {
            return;
        }

        OverviewRegistry::register(
            'accounting',
            fn (): string => __('accounting::filament/overview-card.label'),
            [],
            fn (): array => [],
            fn (): bool  => OverviewCard::visible(),
            sort: 35,
            headline: AccountingHeadlineWidget::class,
            attention: fn (): array => OverviewCard::attention(),
            actions: fn (): array => OverviewCard::actions(),
        );
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AccountingPlugin::make());
        });
    }

    public function registerLivewireComponents()
    {
        Livewire::component('accounting-journal-chart', JournalChartWidget::class);

        Livewire::component('accounting-invoice-summary', InvoiceSummary::class);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            Css::make('accounting', __DIR__.'/../resources/dist/accounting.css'),
        ], 'accounting');
    }
}
