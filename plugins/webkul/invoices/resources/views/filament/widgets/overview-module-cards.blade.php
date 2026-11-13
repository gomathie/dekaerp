{{--
    "Across the company" cards (approved mockup, 2026-11-13). Scoped styles on
    Filament's colour variables rather than Tailwind utilities: plugin views
    are not all in the compiled theme. Values are escaped text.
--}}
{{-- Refreshes itself every 5 minutes (user, 2026-11-13). --}}
<x-filament-widgets::widget wire:poll.300s>
    <style>
        .ov-mods-h { margin: 0.25rem 0 0.75rem; font-size: 0.6875rem; font-weight: 650; letter-spacing: 0.08em; text-transform: uppercase; color: rgb(113 113 122); }
        .dark .ov-mods-h { color: rgb(161 161 170); }
        .ov-mods { display: grid; gap: 0.875rem; grid-template-columns: repeat(auto-fit, minmax(13rem, 1fr)); }
        .ov-mod { min-width: 0; padding: 1.125rem 1.25rem; border: 1px solid rgb(24 24 27 / 0.09); border-radius: 1rem; background: #fff; }
        .dark .ov-mod { border-color: rgb(255 255 255 / 0.08); background: rgb(24 24 27); }
        .ov-mod__head { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.875rem; }
        .ov-mod__title { margin: 0; font-size: 0.9375rem; font-weight: 650; letter-spacing: -0.01em; }
        .ov-mod__link { font-size: 0.8125rem; font-weight: 550; color: rgb(113 113 122); text-decoration: none; white-space: nowrap; }
        .ov-mod__link:hover, .ov-mod__link:focus-visible { color: var(--primary-600); }
        .ov-mod dl { margin: 0; display: grid; gap: 0.5rem; }
        .ov-mod dl div { display: flex; justify-content: space-between; align-items: baseline; gap: 0.75rem; }
        .ov-mod dt { font-size: 0.8125rem; color: rgb(113 113 122); min-width: 0; }
        .dark .ov-mod dt { color: rgb(161 161 170); }
        .ov-mod dd { margin: 0; font-size: 0.9375rem; font-weight: 650; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .ov-mod dd[data-tone="danger"] { color: var(--danger-600); }
        .ov-mod dd[data-tone="warning"] { color: var(--warning-600); }
        .ov-mod dd[data-tone="success"] { color: var(--success-600); }
        .dark .ov-mod dd[data-tone="danger"] { color: var(--danger-400); }
        .dark .ov-mod dd[data-tone="warning"] { color: var(--warning-400); }
        .dark .ov-mod dd[data-tone="success"] { color: var(--success-400); }
        .ov-mod__bar { height: 0.375rem; margin-top: 0.875rem; border-radius: 9999px; overflow: hidden; background: rgb(24 24 27 / 0.06); }
        .dark .ov-mod__bar { background: rgb(255 255 255 / 0.08); }
        .ov-mod__bar span { display: block; height: 100%; border-radius: 9999px; background: var(--ov-bar); }
        .ov-mod__cap { margin: 0.375rem 0 0; font-size: 0.75rem; color: rgb(113 113 122); }
        .dark .ov-mod__cap { color: rgb(161 161 170); }
    </style>

    <h3 class="ov-mods-h">{{ __('invoices::filament/pages/overview.modules.heading') }}</h3>

    <div class="ov-mods">
        @foreach ($cards as $card)
            <section class="ov-mod" aria-label="{{ $card['label'] }}">
                <div class="ov-mod__head">
                    <h4 class="ov-mod__title">{{ $card['label'] }}</h4>
                    @if ($card['url'] ?? null)
                        <a class="ov-mod__link" href="{{ $card['url'] }}" wire:navigate>{{ __('invoices::filament/pages/overview.modules.open') }} ›</a>
                    @endif
                </div>

                <dl>
                    @foreach ($card['items'] as $item)
                        <div>
                            <dt>{{ $item['label'] }}</dt>
                            <dd @if ($item['tone'] ?? null) data-tone="{{ $item['tone'] }}" @endif>{{ $item['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>

                @if ($card['bar'] ?? null)
                    @php($tone = in_array($card['bar']['tone'], ['success', 'warning', 'danger', 'info', 'primary'], true) ? $card['bar']['tone'] : 'primary')
                    <div class="ov-mod__bar" role="img" aria-label="{{ $card['bar']['caption'] }}">
                        <span style="width: {{ max(0, min(100, (float) $card['bar']['percent'])) }}%; --ov-bar: var(--{{ $tone }}-500)"></span>
                    </div>
                    <p class="ov-mod__cap">{{ $card['bar']['caption'] }}</p>
                @endif
            </section>
        @endforeach
    </div>
</x-filament-widgets::widget>
