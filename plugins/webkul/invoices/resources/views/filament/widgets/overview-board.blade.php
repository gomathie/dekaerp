{{--
    The Overview's All tab as one board (approved mockup, 2026-11-13).
    Scoped styles on Filament's colour variables (plugin views are not all in
    the compiled Tailwind theme). Small visuals are server-drawn SVG/HTML, no
    extra JavaScript. Every value is escaped text. Refreshes every 5 minutes.
--}}
@php
    $main = array_filter([$panels['sales'] ?? null, $showCashFlow ? 'cash' : null, $panels['purchases'] ?? null]);
    $side = array_filter([$attention !== [] || isset($panels['invoices']) ? 'attention' : null, $panels['invoices'] ?? null]);

    $sparkline = function (array $values): ?string {
        $values = array_values(array_map('floatval', $values));

        if (count($values) < 2) {
            return null;
        }

        $max = max($values) ?: 1.0;
        $min = min(0.0, min($values));
        $span = ($max - $min) ?: 1.0;
        $step = 120 / (count($values) - 1);

        return collect($values)
            ->map(fn (float $value, int $index): string => round($index * $step, 2).','.round(32 - (($value - $min) / $span) * 28, 2))
            ->implode(' ');
    };
@endphp

<x-filament-widgets::widget wire:poll.300s>
    <style>
        .ovb { --ovb-line: rgb(24 24 27 / 0.09); --ovb-soft: rgb(24 24 27 / 0.04); --ovb-muted: rgb(113 113 122); --ovb-surface: #fff; }
        .dark .ovb { --ovb-line: rgb(255 255 255 / 0.08); --ovb-soft: rgb(255 255 255 / 0.05); --ovb-muted: rgb(161 161 170); --ovb-surface: rgb(24 24 27); }
        .ovb__meta { display: flex; justify-content: flex-end; margin: -0.25rem 0 0.625rem; font-size: 0.75rem; color: var(--ovb-muted); }
        .ovb__grid { display: grid; gap: 0.875rem; grid-template-columns: minmax(0, 7fr) minmax(0, 5fr); align-items: start; }
        .ovb__grid--single { grid-template-columns: minmax(0, 1fr); }
        @media (max-width: 1080px) { .ovb__grid { grid-template-columns: minmax(0, 1fr); } }
        .ovb__col { display: grid; gap: 0.875rem; min-width: 0; }
        .ovb-panel { min-width: 0; padding: 1.125rem 1.25rem; border: 1px solid var(--ovb-line); border-radius: 1rem; background: var(--ovb-surface); }
        .ovb-panel__head { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 0.875rem; }
        .ovb-panel__title { margin: 0; font-size: 0.9375rem; font-weight: 650; letter-spacing: -0.01em; }
        .ovb-panel__link { font-size: 0.8125rem; font-weight: 550; color: var(--ovb-muted); text-decoration: none; white-space: nowrap; }
        .ovb-panel__link:hover, .ovb-panel__link:focus-visible { color: var(--primary-600); }
        .ovb-lbl { font-size: 0.65625rem; font-weight: 650; letter-spacing: 0.07em; text-transform: uppercase; color: var(--ovb-muted); }
        .ovb-val { margin-top: 0.25rem; font-size: 1.5rem; font-weight: 650; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ovb-trend { display: inline-flex; align-items: center; margin-left: 0.375rem; padding: 0.0625rem 0.4375rem; border-radius: 9999px; font-size: 0.71875rem; font-weight: 650; vertical-align: 0.25rem; }
        .ovb-trend--up { color: var(--success-700); background: color-mix(in oklab, var(--success-500) 14%, transparent); }
        .ovb-trend--down { color: var(--danger-700); background: color-mix(in oklab, var(--danger-500) 14%, transparent); }
        .ovb-trend--flat { color: var(--ovb-muted); background: var(--ovb-soft); }
        .dark .ovb-trend--up { color: var(--success-400); } .dark .ovb-trend--down { color: var(--danger-400); }
        .ovb-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(9rem, 1fr)); }
        .ovb-metric { min-width: 0; padding: 0.125rem 1rem; border-left: 1px solid var(--ovb-line); }
        .ovb-metric:first-child { padding-left: 0; border-left: 0; }
        @media (max-width: 560px) { .ovb-metrics { grid-template-columns: 1fr; gap: 0.875rem; } .ovb-metric { padding: 0; border-left: 0; } }
        .ovb-viz { margin-top: 0.625rem; height: 2.125rem; }
        .ovb-viz svg { display: block; width: 100%; height: 100%; overflow: visible; }
        .ovb-bars { display: flex; align-items: flex-end; gap: 2px; height: 1.625rem; margin-top: 0.75rem; }
        .ovb-bars span { flex: 1; min-height: 2px; border-radius: 2px; background: var(--success-500); }
        .ovb-gauge { position: relative; height: 0.375rem; margin-top: 1.125rem; border-radius: 9999px; background: linear-gradient(90deg, var(--danger-500), var(--warning-500) 45%, var(--success-500)); }
        .ovb-gauge b { position: absolute; top: -0.375rem; width: 2px; height: 1.125rem; border-radius: 2px; background: currentColor; }
        .ovb-split { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.5rem; }
        .ovb-split .ovb-val { font-size: 1.3125rem; }
        .ovb-pct { margin-top: 0.125rem; font-size: 0.75rem; color: var(--ovb-muted); font-variant-numeric: tabular-nums; }
        .ovb-stack { display: flex; gap: 4px; height: 0.5rem; margin: 0.75rem 0 1rem; }
        .ovb-stack span { border-radius: 9999px; min-width: 4px; }
        .ovb-tabs { display: inline-flex; gap: 2px; padding: 2px; border: 1px solid var(--ovb-line); border-radius: 0.5625rem; background: var(--ovb-soft); }
        .ovb-tabs button { padding: 0.3125rem 0.625rem; border: 0; border-radius: 0.4375rem; background: none; font-size: 0.78125rem; font-weight: 550; color: var(--ovb-muted); cursor: pointer; }
        .ovb-tabs button[aria-pressed="true"] { background: var(--ovb-surface); color: inherit; box-shadow: 0 1px 2px rgb(0 0 0 / 0.08); }
        .ovb-tabs button:focus-visible { outline: 2px solid var(--primary-500); outline-offset: 1px; }
        .ovb-tabs .ovb-n { margin-left: 0.3125rem; padding: 0 0.375rem; border-radius: 9999px; font-size: 0.6875rem; background: var(--ovb-soft); }
        .ovb-table { width: 100%; margin-top: 0.625rem; border-collapse: collapse; font-size: 0.8125rem; font-variant-numeric: tabular-nums; }
        .ovb-table th { padding: 0.5rem 0.25rem; border-bottom: 1px solid var(--ovb-line); text-align: left; font-size: 0.65625rem; font-weight: 650; letter-spacing: 0.07em; text-transform: uppercase; color: var(--ovb-muted); }
        .ovb-table td { padding: 0.625rem 0.25rem; border-bottom: 1px solid var(--ovb-line); }
        .ovb-table tr:last-child td { border-bottom: 0; }
        .ovb-table .r { text-align: right; white-space: nowrap; }
        .ovb-table a { color: inherit; font-weight: 550; text-decoration: none; }
        .ovb-table a:hover, .ovb-table a:focus-visible { color: var(--primary-600); }
        .ovb-late { color: var(--danger-600); font-weight: 600; } .dark .ovb-late { color: var(--danger-400); }
        .ovb-empty { padding: 0.75rem 0; font-size: 0.8125rem; color: var(--ovb-muted); }
        .ovb-att { display: grid; gap: 0.25rem; }
        .ovb-att a, .ovb-att span.ovb-att__row { display: flex; align-items: center; gap: 0.75rem; padding: 0.5625rem 0.5rem; border-radius: 0.625rem; color: inherit; text-decoration: none; }
        @media (hover: hover) { .ovb-att a:hover { background: var(--ovb-soft); } }
        .ovb-att a:focus-visible { outline: 2px solid var(--primary-500); }
        .ovb-cnt { display: grid; place-items: center; min-width: 1.875rem; height: 1.875rem; border-radius: 0.5625rem; font-size: 0.8125rem; font-weight: 700; font-variant-numeric: tabular-nums; color: var(--ovb-tone-700); background: color-mix(in oklab, var(--ovb-tone-500) 14%, transparent); }
        .dark .ovb-cnt { color: var(--ovb-tone-400); }
        .ovb-att__label { flex: 1; min-width: 0; font-size: 0.875rem; font-weight: 550; }
        .ovb-break { display: grid; gap: 0.5rem; margin-top: 0.875rem; }
        .ovb-break div { display: grid; grid-template-columns: 0.625rem minmax(0, 1fr) auto 2.75rem; gap: 0.5rem; align-items: center; font-size: 0.8125rem; font-variant-numeric: tabular-nums; }
        .ovb-break i { width: 0.5rem; height: 0.5rem; border-radius: 9999px; }
        .ovb-break span:first-of-type { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .ovb-break span:last-child { text-align: right; color: var(--ovb-muted); }
    </style>

    <div class="ovb">
        <p class="ovb__meta">{{ __('invoices::filament/pages/overview.board.updated', ['time' => $updatedAt->format('H:i')]) }}</p>

        <div @class(['ovb__grid', 'ovb__grid--single' => $main === [] || $side === []])>
            @if ($main !== [])
                <div class="ovb__col">
                    @foreach (['sales', 'cash', 'purchases'] as $slot)
                        @if ($slot === 'cash' && $showCashFlow)
                            @livewire(\Webkul\Invoice\Filament\Widgets\OverviewCashFlowChart::class, ['pageFilters' => $filters], key('ovb-cash'))
                        @elseif (isset($panels[$slot]))
                            @php($panel = $panels[$slot])
                            <section class="ovb-panel" aria-label="{{ $panel['label'] }}">
                                <div class="ovb-panel__head">
                                    <h3 class="ovb-panel__title">{{ $panel['label'] }}</h3>
                                    @if ($panel['url'])
                                        <a class="ovb-panel__link" href="{{ $panel['url'] }}" wire:navigate>{{ __('invoices::filament/pages/overview.modules.open') }} ›</a>
                                    @endif
                                </div>

                                <div class="ovb-metrics">
                                    @foreach ($panel['metrics'] as $metric)
                                        <div class="ovb-metric">
                                            <div class="ovb-lbl">{{ $metric['label'] }}</div>
                                            <div class="ovb-val">
                                                {{ $metric['value'] }}
                                                @if ($metric['trend'] ?? null)
                                                    <span class="ovb-trend ovb-trend--{{ $metric['trend']['direction'] }}">{{ ['up' => '↗', 'down' => '↘', 'flat' => '→'][$metric['trend']['direction']] }} {{ $metric['trend']['text'] }}</span>
                                                @endif
                                            </div>

                                            @php($viz = $metric['viz'] ?? null)
                                            @if (($viz['type'] ?? null) === 'line' && ($points = $sparkline($viz['values'])))
                                                <div class="ovb-viz" aria-hidden="true">
                                                    <svg viewBox="0 0 120 34" preserveAspectRatio="none">
                                                        <polyline points="0,34 {{ $points }} 120,34" fill="color-mix(in oklab, var(--success-500) 14%, transparent)" stroke="none" />
                                                        <polyline points="{{ $points }}" fill="none" stroke="var(--success-500)" stroke-width="2" vector-effect="non-scaling-stroke" />
                                                    </svg>
                                                </div>
                                            @elseif (($viz['type'] ?? null) === 'bars' && count($viz['values']) > 1)
                                                @php($top = max($viz['values']) ?: 1)
                                                <div class="ovb-bars" aria-hidden="true">
                                                    @foreach (array_slice($viz['values'], -20) as $count)
                                                        <span style="height: {{ max(8, round($count / $top * 100)) }}%"></span>
                                                    @endforeach
                                                </div>
                                            @elseif (($viz['type'] ?? null) === 'gauge')
                                                <div class="ovb-gauge" aria-hidden="true"><b style="left: {{ max(0, min(100, (int) $viz['percent'])) }}%"></b></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if (($panel['breakdown']['items'] ?? []) !== [])
                                    @php($palette = ['var(--primary-500)', 'var(--success-500)', 'var(--info-500)', 'rgb(161 161 170)'])
                                    <div class="ovb-lbl" style="margin-top: 1.125rem">{{ $panel['breakdown']['label'] }}</div>
                                    <div class="ovb-stack" aria-hidden="true">
                                        @foreach ($panel['breakdown']['items'] as $i => $item)
                                            <span style="flex: {{ max(1, $item['percent']) }}; background: {{ $palette[$i] ?? $palette[3] }}"></span>
                                        @endforeach
                                    </div>
                                    <div class="ovb-break" style="margin-top: 0">
                                        @foreach ($panel['breakdown']['items'] as $i => $item)
                                            <div><i style="background: {{ $palette[$i] ?? $palette[3] }}"></i><span>{{ $item['label'] }}</span><span>{{ $item['value'] }}</span><span>{{ $item['percent'] }}%</span></div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        @endif
                    @endforeach
                </div>
            @endif

            @if ($side !== [])
                <div class="ovb__col">
                    <section class="ovb-panel" aria-label="{{ __('invoices::filament/pages/overview.attention.heading') }}">
                        <div class="ovb-panel__head"><h3 class="ovb-panel__title">{{ __('invoices::filament/pages/overview.attention.heading') }}</h3></div>
                        @if ($attention === [])
                            <p class="ovb-empty">{{ __('invoices::filament/pages/overview.attention.empty') }}</p>
                        @else
                            <div class="ovb-att">
                                @foreach ($attention as $item)
                                    @php($tone = in_array($item['tone'], ['danger', 'warning', 'info', 'success'], true) ? $item['tone'] : 'gray')
                                    @php($vars = "--ovb-tone-400: var(--{$tone}-400); --ovb-tone-500: var(--{$tone}-500); --ovb-tone-700: var(--{$tone}-700);")
                                    @if ($item['url'])
                                        <a href="{{ $item['url'] }}" wire:navigate style="{{ $vars }}"><span class="ovb-cnt">{{ $item['count'] }}</span><span class="ovb-att__label">{{ $item['label'] }}</span><span aria-hidden="true">›</span></a>
                                    @else
                                        <span class="ovb-att__row" style="{{ $vars }}"><span class="ovb-cnt">{{ $item['count'] }}</span><span class="ovb-att__label">{{ $item['label'] }}</span></span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if ($invoices = $panels['invoices'] ?? null)
                        <section class="ovb-panel" aria-label="{{ $invoices['label'] }}" x-data="{ tab: '{{ ($invoices['list']['tabs']['overdue']['count'] ?? 0) > 0 ? 'overdue' : 'open' }}' }">
                            <div class="ovb-panel__head">
                                <h3 class="ovb-panel__title">{{ $invoices['label'] }}</h3>
                                @if ($invoices['url'])
                                    <a class="ovb-panel__link" href="{{ $invoices['url'] }}" wire:navigate>{{ __('invoices::filament/pages/overview.modules.open') }} ›</a>
                                @endif
                            </div>

                            <div class="ovb-split">
                                @foreach ($invoices['split'] as $part)
                                    <div>
                                        <div class="ovb-lbl">{{ $part['label'] }}</div>
                                        <div class="ovb-val" @if ($part['tone'] === 'danger' && $part['percent'] > 0) style="color: var(--danger-600)" @endif>{{ $part['value'] }}</div>
                                        <div class="ovb-pct">{{ $part['percent'] }}%</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="ovb-stack" aria-hidden="true">
                                @foreach ($invoices['split'] as $part)
                                    @if ($part['percent'] > 0)
                                        <span style="flex: {{ $part['percent'] }}; background: var(--{{ $part['tone'] }}-500)"></span>
                                    @endif
                                @endforeach
                            </div>

                            <div class="ovb-tabs" role="group" aria-label="{{ __('invoices::filament/pages/overview.board.show') }}">
                                @foreach ($invoices['list']['tabs'] as $key => $tab)
                                    <button type="button" x-on:click="tab = '{{ $key }}'" x-bind:aria-pressed="tab === '{{ $key }}'">{{ $tab['label'] }}<span class="ovb-n">{{ $tab['count'] }}</span></button>
                                @endforeach
                            </div>

                            @foreach ($invoices['list']['rows'] as $key => $rows)
                                <div x-show="tab === '{{ $key }}'" @if ($key !== 'overdue') x-cloak @endif style="overflow-x: auto">
                                    @if ($rows === [])
                                        <p class="ovb-empty">{{ __('invoices::filament/pages/overview.board.nothing') }}</p>
                                    @else
                                        <table class="ovb-table">
                                            <thead><tr><th>{{ __('invoices::filament/pages/overview.board.customer') }}</th><th>{{ __('invoices::filament/pages/overview.board.due') }}</th><th class="r">{{ $invoices['list']['currency'] }}</th></tr></thead>
                                            <tbody>
                                                @foreach ($rows as $row)
                                                    <tr>
                                                        <td>@if ($row['url'])<a href="{{ $row['url'] }}" wire:navigate>{{ $row['name'] }}</a>@else{{ $row['name'] }}@endif</td>
                                                        <td @class(['ovb-late' => $row['late']])>{{ $row['detail'] }}</td>
                                                        <td class="r">{{ $row['amount'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            @endforeach
                        </section>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
