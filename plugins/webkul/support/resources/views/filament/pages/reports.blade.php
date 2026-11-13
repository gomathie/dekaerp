{{--
    Reports (reports plan R1): every report the viewer can open, grouped by
    module, with a search box. Scoped styles on Filament's variables. The
    search filters on the client; the list itself is already permission-checked.
--}}
<x-filament-panels::page>
    <style>
        .rpt-search { width: 100%; max-width: 28rem; padding: 0.5625rem 0.875rem; border: 1px solid rgb(24 24 27 / 0.12); border-radius: 0.625rem; background: #fff; font-size: 0.875rem; color: inherit; }
        .dark .rpt-search { border-color: rgb(255 255 255 / 0.12); background: rgb(24 24 27); }
        .rpt-search:focus { outline: 2px solid var(--primary-500); outline-offset: 1px; }
        .rpt-groups { display: grid; gap: 0.875rem; grid-template-columns: repeat(auto-fill, minmax(17rem, 1fr)); margin-top: 1rem; }
        .rpt-group { padding: 1rem 1.125rem; border: 1px solid rgb(24 24 27 / 0.09); border-radius: 1rem; background: #fff; }
        .dark .rpt-group { border-color: rgb(255 255 255 / 0.08); background: rgb(24 24 27); }
        .rpt-group h2 { margin: 0 0 0.5rem; font-size: 0.6875rem; font-weight: 650; letter-spacing: 0.08em; text-transform: uppercase; color: rgb(113 113 122); }
        .rpt-group ul { margin: 0; padding: 0; list-style: none; display: grid; gap: 0.125rem; }
        .rpt-group a { display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 550; color: inherit; text-decoration: none; }
        .rpt-group a svg { width: 1.125rem; height: 1.125rem; color: var(--primary-600); flex: none; }
        @media (hover: hover) { .rpt-group a:hover { background: rgb(24 24 27 / 0.04); } .dark .rpt-group a:hover { background: rgb(255 255 255 / 0.05); } }
        .rpt-group a:focus-visible { outline: 2px solid var(--primary-500); }
        .rpt-empty { margin-top: 1rem; font-size: 0.875rem; color: rgb(113 113 122); }
    </style>

    <div x-data="{ q: '' }">
        <label for="reports-search" class="sr-only">{{ __('support::filament/pages/reports.search') }}</label>
        <input id="reports-search" type="search" class="rpt-search" x-model="q" placeholder="{{ __('support::filament/pages/reports.search') }}" autocomplete="off">

        <div class="rpt-groups">
            @foreach ($groups as $group => $items)
                <section class="rpt-group" x-show="!q || {{ \Illuminate\Support\Js::from(collect($items)->pluck('label')->push($group)->implode(' ')) }}.toLowerCase().includes(q.toLowerCase())">
                    <h2>{{ $group }}</h2>
                    <ul>
                        @foreach ($items as $item)
                            <li x-show="!q || {{ \Illuminate\Support\Js::from($item['label'].' '.$group) }}.toLowerCase().includes(q.toLowerCase())">
                                <a href="{{ $item['url'] }}" wire:navigate>
                                    @if ($item['icon'])
                                        <x-filament::icon :icon="$item['icon']" />
                                    @endif
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
