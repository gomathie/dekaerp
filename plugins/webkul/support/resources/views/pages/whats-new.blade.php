<x-filament-panels::page>
    {{-- Own small stylesheet: these classes are not in the compiled Filament theme,
         and a missing utility class fails silently (WN-3). --}}
    <style>
        .wn-toolbar { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; }
        .wn-search { flex: 1 1 16rem; max-width: 28rem; }
        .wn-search input { width: 100%; border-radius: .5rem; border: 1px solid rgb(209 213 219); background: #fff; padding: .5rem .75rem; font-size: .875rem; color: rgb(17 24 39); }
        .dark .wn-search input { border-color: rgb(55 65 81); background: rgb(17 24 39); color: #fff; }
        .wn-pills { display: flex; flex-wrap: wrap; gap: .5rem; }
        .wn-pill { border-radius: 9999px; border: 1px solid rgb(209 213 219); padding: .25rem .75rem; font-size: .75rem; font-weight: 500; color: rgb(55 65 81); background: transparent; cursor: pointer; }
        .dark .wn-pill { border-color: rgb(55 65 81); color: rgb(209 213 219); }
        .wn-pill[aria-pressed="true"] { border-color: rgb(var(--primary-600)); background: rgb(var(--primary-600)); color: #fff; }
        .wn-pill:focus-visible, .wn-search input:focus-visible { outline: 2px solid rgb(var(--primary-500)); outline-offset: 2px; }
        .wn-badge { display: inline-block; margin-right: .375rem; border-radius: .25rem; background: rgb(254 226 226); padding: 0 .375rem; font-size: .6875rem; font-weight: 600; color: rgb(153 27 27); vertical-align: middle; }
        .dark .wn-badge { background: rgb(127 29 29 / .4); color: rgb(254 202 202); }
        .wn-item pre { overflow-x: auto; border-radius: .375rem; background: rgb(243 244 246); padding: .5rem; font-size: .75rem; }
        .dark .wn-item pre { background: rgb(31 41 55); }
        [x-cloak] { display: none !important; }
    </style>

    @php
        $releases = $this->releases();
        $matrix = collect($releases)->map(fn ($release) => [
            'id' => $release['id'],
            'sections' => collect($release['sections'])->map(fn ($section) => [
                'category' => $section['category'],
                'items' => collect($section['items'])->map(fn ($item) => [
                    'text' => $item['text'],
                    'security' => $item['security'],
                ])->values(),
            ])->values(),
        ])->values();
    @endphp

    <div
        class="flex flex-col gap-6"
        x-data="{
            query: '',
            categories: [],
            matrix: @js($matrix),
            toggle(category) {
                this.categories = this.categories.includes(category)
                    ? this.categories.filter((c) => c !== category)
                    : [...this.categories, category];
            },
            filtering() { return this.query.trim() !== '' || this.categories.length > 0; },
            itemVisible(r, s, i) {
                const section = this.matrix[r].sections[s];
                const item = section.items[i];
                if (this.categories.length) {
                    const wanted = this.categories.includes(section.category)
                        || (this.categories.includes('security') && item.security);
                    if (! wanted) return false;
                }
                const q = this.query.trim().toLowerCase();
                return q === '' || item.text.includes(q);
            },
            sectionVisible(r, s) { return this.matrix[r].sections[s].items.some((_, i) => this.itemVisible(r, s, i)); },
            releaseVisible(r) { return this.matrix[r].sections.some((_, s) => this.sectionVisible(r, s)); },
            anyVisible() { return this.matrix.some((_, r) => this.releaseVisible(r)); },
        }"
    >
        @if ($this->hasReleases())
            <div class="wn-toolbar">
                <div class="wn-search">
                    <label for="whats-new-search" class="sr-only">{{ __('support::filament/pages/whats-new.search.label') }}</label>
                    <input
                        id="whats-new-search"
                        type="search"
                        x-model.debounce.150ms="query"
                        placeholder="{{ __('support::filament/pages/whats-new.search.placeholder') }}"
                    />
                </div>

                <div class="wn-pills" role="group" aria-label="{{ __('support::filament/pages/whats-new.filters.label') }}">
                    @foreach ([...\Webkul\Support\Filament\Pages\WhatsNew::CATEGORIES, 'security'] as $category)
                        @continue($category === 'upgrade')
                        <button
                            type="button"
                            class="wn-pill"
                            x-on:click="toggle('{{ $category }}')"
                            x-bind:aria-pressed="categories.includes('{{ $category }}') ? 'true' : 'false'"
                        >
                            {{ __('support::filament/pages/whats-new.categories.'.$category) }}
                        </button>
                    @endforeach
                </div>

                <x-filament::button
                    tag="a"
                    :href="route('support.whats-new.pdf')"
                    target="_blank"
                    color="gray"
                    size="sm"
                    icon="heroicon-o-arrow-down-tray"
                >
                    {{ __('support::filament/pages/whats-new.pdf.all') }}
                </x-filament::button>
            </div>

            @foreach ($releases as $r => $release)
                <details
                    @if ($r === 0) open @endif
                    x-show="releaseVisible({{ $r }})"
                    x-effect="if (filtering() && releaseVisible({{ $r }})) $el.open = true"
                    class="group rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-base font-semibold text-gray-950 dark:text-white">
                                {{ $release['version'] }}
                            </span>

                            @if ($release['date'])
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $release['date'] }}</span>
                            @endif

                            @if ($r === 0)
                                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                    {{ __('support::filament/pages/whats-new.latest') }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('support.whats-new.pdf', ['release' => $release['id']]) }}"
                                target="_blank"
                                class="text-xs font-medium text-primary-600 hover:underline dark:text-primary-400"
                                x-on:click.stop
                            >
                                {{ __('support::filament/pages/whats-new.pdf.one') }}
                            </a>

                            <x-filament::icon
                                icon="heroicon-o-chevron-down"
                                class="size-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180"
                            />
                        </div>
                    </summary>

                    <div class="flex flex-col gap-6 border-t border-gray-200 px-6 py-5 dark:border-gray-700">
                        @foreach ($release['sections'] as $s => $section)
                            @if ($section['items'] !== [])
                                <div class="flex flex-col gap-3" x-show="sectionVisible({{ $r }}, {{ $s }})">
                                    <div class="flex items-center gap-2">
                                        <x-filament::icon
                                            :icon="\Webkul\Support\Filament\Pages\WhatsNew::sectionIcon($section['category'])"
                                            class="size-4 text-gray-400"
                                        />

                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            {{ \Webkul\Support\Filament\Pages\WhatsNew::sectionLabel($section) }}
                                        </span>
                                    </div>

                                    <ul class="flex flex-col gap-2 pl-6 text-sm text-gray-700 marker:text-gray-400 dark:text-gray-300">
                                        @foreach ($section['items'] as $i => $item)
                                            <li
                                                x-show="itemVisible({{ $r }}, {{ $s }}, {{ $i }})"
                                                class="wn-item list-disc [&_a]:text-primary-600 [&_a]:underline [&_code]:rounded [&_code]:bg-gray-100 [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-xs dark:[&_code]:bg-gray-800"
                                            >
                                                @if ($item['security'])
                                                    <span class="wn-badge">{{ __('support::filament/pages/whats-new.categories.security') }}</span>
                                                @endif
                                                {{-- Sanitised in WhatsNew::makeItem(): raw HTML stripped, unsafe links refused. --}}
                                                {!! $item['html'] !!}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </details>
            @endforeach

            <p x-cloak x-show="! anyVisible()" class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('support::filament/pages/whats-new.no-match') }}
            </p>
        @else
            <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                <x-filament::icon icon="heroicon-o-megaphone" class="size-8 text-gray-400" />

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/whats-new.empty', ['app' => config('app.name')]) }}
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
