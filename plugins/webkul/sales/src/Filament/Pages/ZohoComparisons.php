<?php

namespace Webkul\Sale\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Webkul\Sale\Models\ZohoImportRun;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

/**
 * Zoho Books against DEKA, for the company itself (user, 2026-11-26): while
 * the client runs both systems side by side, every comparison a Super Admin
 * runs is kept here, read-only, with the totals and the documents found in
 * one system only.
 *
 * Who sees it: an active Super Admin, or a Company Admin who is a member of
 * the company (a global role is true for every company id, so membership
 * is what limits it). The company is always the current one from the
 * session context, never an id the request sends: a run is read only when
 * it belongs to that company.
 */
class ZohoComparisons extends Page
{
    protected const LANG = 'sales::filament/pages/zoho-comparisons';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?int $navigationSort = 96;

    protected static ?string $slug = 'zoho-comparisons';

    protected string $view = 'sales::filament.pages.zoho-comparisons';

    public ?int $runId = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        $companyId = current_company_id();

        if (! $user instanceof User || ! $user->is_active || ! $companyId) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isCompanyAdmin($companyId)
            && $user->allowedCompanies()->withoutGlobalScopes()->where('companies.id', $companyId)->exists();
    }

    /**
     * Listed only for a company that has comparisons to show.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess()
            && ZohoImportRun::query()
                ->where('company_id', current_company_id())
                ->where('status', ZohoImportRun::FINISHED)
                ->whereNotNull('comparison')
                ->exists();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getNavigationLabel(): string
    {
        return __(self::LANG.'.navigation');
    }

    public function getTitle(): string
    {
        return __(self::LANG.'.title');
    }

    /**
     * @return Collection<int, ZohoImportRun>
     */
    public function runs(): Collection
    {
        return ZohoImportRun::query()
            ->where('company_id', current_company_id())
            ->where('status', ZohoImportRun::FINISHED)
            ->whereNotNull('comparison')
            ->latest('id')
            ->limit(52)
            ->get(['id', 'mode', 'finished_at', 'comparison']);
    }

    public function selectRun(int $id): void
    {
        $this->runId = $id;
    }

    /**
     * The chosen run, only if it is this company's; else the latest.
     */
    public function selected(): ?ZohoImportRun
    {
        $runs = $this->runs();

        return $runs->firstWhere('id', $this->runId) ?? $runs->first();
    }
}
