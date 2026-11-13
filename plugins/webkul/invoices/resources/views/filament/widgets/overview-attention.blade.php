{{--
    Needs attention (Overview, All tab). Scoped styles on Filament's colour
    variables (as the dashboard banner does) rather than Tailwind utilities:
    plugin views are not all in the compiled theme, and a missing class
    renders silently unstyled. Tones follow the panel colours in both themes.
--}}
<x-filament-widgets::widget>
    <style>
        .ov-attn { list-style: none; margin: -0.25rem -0.5rem; padding: 0; }
        .ov-attn li + li { border-top: 1px solid rgb(15 23 42 / 0.06); }
        .dark .ov-attn li + li { border-top-color: rgb(255 255 255 / 0.06); }
        .ov-attn a, .ov-attn span.ov-attn__row {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 0.5rem; border-radius: 0.5rem;
            font-size: 0.875rem; color: inherit; text-decoration: none;
        }
        @media (hover: hover) { .ov-attn a:hover { background: rgb(15 23 42 / 0.03); } .dark .ov-attn a:hover { background: rgb(255 255 255 / 0.04); } }
        .ov-attn a:focus-visible { outline: 2px solid var(--primary-500); outline-offset: 1px; }
        .ov-attn__count {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 1.75rem; height: 1.75rem; padding: 0 0.5rem; border-radius: 9999px;
            font-size: 0.75rem; font-weight: 650; font-variant-numeric: tabular-nums;
            color: var(--ov-tone-600); background: color-mix(in oklab, var(--ov-tone-500) 12%, transparent);
        }
        .dark .ov-attn__count { color: var(--ov-tone-400); background: color-mix(in oklab, var(--ov-tone-400) 14%, transparent); }
        .ov-attn__label { flex: 1; min-width: 0; }
        .ov-attn__chevron { width: 1rem; height: 1rem; flex: none; opacity: 0.45; }
        .ov-attn-empty { display: flex; align-items: center; gap: 0.75rem; padding: 0.25rem 0; font-size: 0.875rem; opacity: 0.75; }
        .ov-attn-empty svg { width: 1.5rem; height: 1.5rem; color: var(--success-500); }
    </style>

    <x-filament::section :heading="__('invoices::filament/pages/overview.attention.heading')" icon="heroicon-o-bell-alert">
        @if ($items === [])
            <div class="ov-attn-empty">
                <x-filament::icon icon="heroicon-o-check-circle" />
                <span>{{ __('invoices::filament/pages/overview.attention.empty') }}</span>
            </div>
        @else
            <ul class="ov-attn">
                @foreach ($items as $item)
                    @php
                        $tone = in_array($item['tone'], ['danger', 'warning', 'info', 'success'], true) ? $item['tone'] : 'gray';
                        $toneVars = "--ov-tone-400: var(--{$tone}-400); --ov-tone-500: var(--{$tone}-500); --ov-tone-600: var(--{$tone}-600);";
                    @endphp
                    <li style="{{ $toneVars }}">
                        @if ($item['url'])
                            <a href="{{ $item['url'] }}" wire:navigate>
                                <span class="ov-attn__count">{{ $item['count'] }}</span>
                                <span class="ov-attn__label">{{ $item['label'] }}</span>
                                <x-filament::icon icon="heroicon-m-chevron-right" class="ov-attn__chevron" />
                            </a>
                        @else
                            <span class="ov-attn__row">
                                <span class="ov-attn__count">{{ $item['count'] }}</span>
                                <span class="ov-attn__label">{{ $item['label'] }}</span>
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
