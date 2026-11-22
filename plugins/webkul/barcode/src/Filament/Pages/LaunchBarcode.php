<?php

namespace Webkul\Barcode\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

class LaunchBarcode extends Page
{
    use ChecksPagePermission, HasPageShield;

    protected string $view = 'barcode::filament.pages.launch-barcode';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $slug = 'barcode-app';

    /**
     * The sidebar launcher showed for companies without the Barcode module
     * (TRACE365, 2026-11-22): only the barcode app's own routes checked the
     * grant. Module switch and the page permission together, never the module
     * check alone (protected decision, 2026-11-07).
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('barcode') && static::hasPagePermission();
    }

    public function mount(): void
    {
        $this->redirect(route('barcode.dashboard'), navigate: true);
    }

    public static function getNavigationLabel(): string
    {
        return __('barcode::app.filament.navigation.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Barcode;
    }

    public function getTitle(): string
    {
        return __('barcode::app.filament.navigation.label');
    }
}
