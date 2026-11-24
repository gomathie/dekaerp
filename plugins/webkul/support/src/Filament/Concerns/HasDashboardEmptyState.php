<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Table;

/**
 * One empty state for dashboard tables (dashboard plan DB-3, U13,
 * 2026-11-22): a plain sentence and the one next step, never a bare empty
 * frame. The action shows only when the user may take it; the caller passes
 * the create URL only if the resource's own canCreate() allows it.
 */
trait HasDashboardEmptyState
{
    /**
     * @param  string  $action  key under support::filament/dashboard.empty-actions
     */
    protected function withDashboardEmptyState(Table $table, string $action, ?string $createUrl): Table
    {
        return $table
            ->emptyStateHeading(__('support::filament/dashboard.empty-table'))
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateActions($createUrl === null ? [] : [
                Action::make('dashboardEmptyCreate')
                    ->label(__('support::filament/dashboard.empty-actions.'.$action))
                    ->icon('heroicon-o-plus')
                    ->url($createUrl),
            ]);
    }

    /**
     * "View all" in the list's header (DB-9, 2026-11-24, from TRACE365's
     * reference screens): the dashboard shows the top few, the button opens
     * the whole list. The caller passes the URL only when the resource's own
     * canViewAny() allows it, so nobody is offered a page they cannot open.
     */
    /**
     * @return list<Action>
     */
    protected function viewAllActions(?string $url): array
    {
        return $url === null ? [] : [
            Action::make('dashboardViewAll')
                ->label(__('support::filament/dashboard.view-all'))
                ->icon('heroicon-m-arrow-right')
                ->iconPosition(IconPosition::After)
                ->link()
                ->url($url),
        ];
    }
}
