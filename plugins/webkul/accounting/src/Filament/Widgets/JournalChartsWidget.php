<?php

namespace Webkul\Accounting\Filament\Widgets;

use Filament\Widgets\Widget;
use Webkul\Account\Models\Journal;
use Webkul\Accounting\Filament\Pages\Overview;

class JournalChartsWidget extends Widget
{
    /**
     * Absent, never an empty frame, when the Accounting overview is closed
     * to the user: page permission missing or the module revoked for the
     * company (dashboard plan DB-3). A widget is reachable through Livewire
     * on its own, and this one shows money owed and to pay. Shield excludes
     * it (config/filament-shield.php), so it has no permission of its own to
     * put through GuardsDashboardWidget; it takes the page's rule instead.
     */
    public static function canView(): bool
    {
        return Overview::canAccess();
    }

    protected string $view = 'accounting::filament.widgets.journal-charts-widget';

    protected int|string|array $columnSpan = 'full';

    public string $activeTab = 'all';

    public function getJournals()
    {
        return Journal::where('show_on_dashboard', true)
            ->orderBy('id', 'asc')
            ->when($this->activeTab !== 'all', function ($query) {
                $query->where('type', $this->activeTab);
            })
            ->get();
    }
}
