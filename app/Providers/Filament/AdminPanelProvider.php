<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyBrandSettings;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Exception;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webkul\PluginManager\Models\Plugin;
use Webkul\Support\Filament\Pages\Profile;
use Webkul\Support\GlobalSearchProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        set_time_limit(300);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('2rem')
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->unsavedChangesAlerts()
            ->topNavigation()
            ->maxContentWidth(Width::Full)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->userMenuItems([
                'profile' => Action::make('profile')
                    ->label(fn () => Auth::user()?->name)
                    ->url(fn (): string => Profile::getUrl()),
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.dashboard'))
                    ->icon('icon-dashboard'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.contact'))
                    ->icon('icon-contacts'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.sale'))
                    ->icon('icon-sales'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.purchase'))
                    ->icon('icon-purchases'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.maintenance'))
                    ->icon('icon-maintenance'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.manufacturing'))
                    ->icon('icon-manufacturing'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.inventory'))
                    ->icon('icon-inventories'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.invoice'))
                    ->icon('icon-invoices'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.accounting'))
                    ->icon('icon-accounting'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.project'))
                    ->icon('icon-projects'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.employee'))
                    ->icon('icon-employees'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.time-off'))
                    ->icon('icon-time-offs'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.recruitment'))
                    ->icon('icon-recruitments'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.barcode'))
                    ->icon('icon-barcode'),
                NavigationGroup::make()
                    ->label(__('admin.navigation.plugin'))
                    ->label(fn (): string => __('admin.navigation.plugin'))
                    ->icon('icon-plugin'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.setting'))
                    ->icon('icon-settings'),
                NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation.help'))
                    ->icon('icon-help'),
            ])
            ->plugins(
                array_merge(
                    $this->getActivePlugins(),
                    [
                        FilamentShieldPlugin::make()
                            ->gridColumns([
                                'default' => 1,
                                'sm'      => 1,
                                'lg'      => 2,
                                'xl'      => 3,
                            ])
                            ->sectionColumnSpan(1)
                            ->checkboxListColumns([
                                'default' => 1,
                                'sm'      => 1,
                                'lg'      => 2,
                                'xl'      => 3,
                            ])
                            ->resourceCheckboxListColumns([
                                'default' => 1,
                                'sm'      => 2,
                            ]),
                    ]
                )
            )
            ->globalSearch(provider: GlobalSearchProvider::class)
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
                ApplyBrandSettings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->multiFactorAuthentication([
                AppAuthentication::make()
                    ->recoverable(),
            ]);
    }

    /**
     * Get active plugins from the database and instantiate them.
     *
     * @return array<object>
     */
    private function getActivePlugins(): array
    {
        $pluginMapping = [
            'accounting'    => 'Webkul\Accounting\AccountingPlugin',
            'accounts'      => 'Webkul\Account\AccountPlugin',
            'barcode'       => 'Webkul\Barcode\BarcodePlugin',
            'chatter'       => 'Webkul\Chatter\ChatterPlugin',
            'contacts'      => 'Webkul\Contact\ContactPlugin',
            'employees'     => 'Webkul\Employee\EmployeePlugin',
            'fields'        => 'Webkul\Field\FieldsPlugin',
            'full-calendar' => 'Webkul\FullCalendar\FullCalendarPlugin',
            'inventories'   => 'Webkul\Inventory\InventoryPlugin',
            'invoices'      => 'Webkul\Invoice\InvoicePlugin',
            'maintenance'   => 'Webkul\Maintenance\MaintenancePlugin',
            'manufacturing' => 'Webkul\Manufacturing\ManufacturingPlugin',
            'partners'      => 'Webkul\Partner\PartnerPlugin',
            'payments'      => 'Webkul\Payment\PaymentPlugin',
            'products'      => 'Webkul\Product\ProductPlugin',
            'projects'      => 'Webkul\Project\ProjectPlugin',
            'purchases'     => 'Webkul\Purchase\PurchasePlugin',
            'recruitments'  => 'Webkul\Recruitment\RecruitmentPlugin',
            'sales'         => 'Webkul\Sale\SalePlugin',
            'security'      => 'Webkul\Security\SecurityPlugin',
            'support'       => 'Webkul\Support\SupportPlugin',
            'time-off'      => 'Webkul\TimeOff\TimeOffPlugin',
            'timesheets'    => 'Webkul\Timesheet\TimesheetPlugin',
        ];

        $plugins = [];

        try {
            // Check if database connection and plugins table exist
            if (! Schema::hasTable('plugins')) {
                return $plugins;
            }

            DB::connection()->getPdo();

            // Get all active plugins from the database
            $activePlugins = Plugin::where('is_active', true)
                ->where('is_installed', true)
                ->get();

            foreach ($activePlugins as $plugin) {
                if (! isset($pluginMapping[$plugin->name])) {
                    continue;
                }

                $pluginClass = $pluginMapping[$plugin->name];

                if (! class_exists($pluginClass)) {
                    continue;
                }

                // Create an instance of the plugin
                $pluginInstance = app($pluginClass);

                if (method_exists($pluginInstance, 'make')) {
                    $plugins[] = $pluginInstance::make();
                } else {
                    $plugins[] = $pluginInstance;
                }
            }
        } catch (Exception) {
            // Silently fail during database unavailability
        }

        return $plugins;
    }
}
