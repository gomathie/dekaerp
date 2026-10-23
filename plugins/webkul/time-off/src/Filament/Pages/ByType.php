<?php

namespace Webkul\TimeOff\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Widgets\LeaveTypeWidget;

class ByType extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'reporting/by-type';

    /*
     * getRouteName() is deliberately NOT overridden - same latent bug as the
     * MyTime dashboard. This page is in the Reporting cluster, so the hardcoded
     * 'filament.admin.pages.time-off-by-type' was never a registered route
     * name. It had not surfaced yet only because nobody had opened the page.
     */

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Reporting::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_time_off_by_type';
    }

    public function getTitle(): string
    {
        return __('time-off::filament/pages/by-type.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/pages/by-type.navigation.title');
    }

    public function getWidgets(): array
    {
        return [
            LeaveTypeWidget::class,
        ];
    }
}
