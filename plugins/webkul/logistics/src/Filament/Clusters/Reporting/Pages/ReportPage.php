<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Webkul\Logistics\Filament\Clusters\Reporting;
use Webkul\Logistics\Support\LogisticsAccess;

/**
 * What every Logistics report has in common (WP-11).
 *
 * Five reports with the same shape - a filtered table, a cluster, an export and
 * one access rule - so the rule lives here once. A per-page copy is how one of
 * them ends up quietly missing the company switch.
 *
 * Rows are company-scoped by CompanyScope on the underlying models, and nothing
 * in these pages removes that scope: a report that crossed the company boundary
 * would be the worst place to do it, because a report is where people take
 * numbers from and act on them.
 */
abstract class ReportPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $cluster = Reporting::class;

    protected string $view = 'logistics::filament.pages.report';

    /**
     * The Shield page permission, e.g. page_logistics_shipment_register.
     */
    abstract protected static function pagePermission(): string;

    /**
     * Reports that show money require view_financials on top of the page
     * permission. An operations user who may read a register has no business
     * reading margins.
     */
    protected static function requiresFinancials(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user?->can(static::pagePermission())) {
            return false;
        }

        if (static::requiresFinancials() && ! $user->can('view_financials_logistics_shipment')) {
            return false;
        }

        return LogisticsAccess::enabledForCurrent();
    }

    public static function getNavigationLabel(): string
    {
        return __(static::$lang.'.title');
    }

    public function getTitle(): string
    {
        return __(static::$lang.'.title');
    }

    public function getSubheading(): ?string
    {
        $key = static::$lang.'.subheading';
        $subheading = __($key);

        return $subheading === $key ? null : $subheading;
    }
}
