<?php

namespace Webkul\Support\Filament\Concerns;

use Filament\Actions\Action;
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
}
