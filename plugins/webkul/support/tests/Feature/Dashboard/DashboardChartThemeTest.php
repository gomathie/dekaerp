<?php

use Webkul\Support\Filament\Widgets\DashboardChartWidget;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensureERPInstalled();
});

/**
 * Chart modernisation (user, 2026-11-11): every dashboard chart carries the
 * translated empty-state text, so an empty period says "No data for this
 * period" instead of drawing a flat line on a 0..1 axis, and the axes use
 * flat labels and at most five value ticks.
 */
it('gives every chart kind an empty-state message and the modern axes', function (): void {
    app()->setLocale('fr');

    $widget = new class extends DashboardChartWidget
    {
        protected function getType(): string
        {
            return 'line';
        }

        protected function getData(): array
        {
            return [];
        }

        /** @return array<string, array<string, mixed>> */
        public function allOptions(): array
        {
            return [
                'bar'      => $this->barChartOptions(),
                'line'     => $this->lineChartOptions(),
                'doughnut' => $this->doughnutChartOptions(),
            ];
        }
    };

    foreach ($widget->allOptions() as $kind => $options) {
        expect($options['plugins']['emptyState']['text'])->toBe('Aucune donnée pour cette période', $kind);

        if ($kind !== 'doughnut') {
            expect($options['scales']['x']['ticks']['maxRotation'])->toBe(0)
                ->and($options['scales']['y']['ticks']['maxTicksLimit'])->toBe(5);
        }
    }
});
