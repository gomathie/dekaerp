{{--
    Help page, Developers tab: the API reference for the modules the current
    company has (user, 2026-11-25, approved mockup).

    Only the selected resource's endpoints are rendered - one module can hold
    over a hundred, each with four code samples - so switching resources is a
    Livewire round trip and the search filters the sidebar in the browser.

    Every value printed here is escaped. Endpoint titles and descriptions come
    from the Scribe files as plain text (ApiReference strips their HTML), and
    the copy buttons read the text back from the page rather than from a
    string built into JavaScript.
--}}
@php
    $modules = $this->apiModules();
    $module = $this->selectedApiModule();
    $resource = $this->selectedApiResource();
    $start = $this->apiGettingStarted();
    $total = $this->apiTotalCount();
    $moduleLabel = $module ? __('support::filament/pages/help-developers.modules.'.$module['key']) : null;

    // One copy routine for every button: the async clipboard where the page is
    // a secure context, a hidden textarea where it is not (plain http, older
    // browsers), so the button works wherever the panel does.
    $copy = "copied: false, copy(text) { const done = () => { this.copied = true; setTimeout(() => this.copied = false, 1500) }; const fallback = () => { const area = document.createElement('textarea'); area.value = text; area.setAttribute('readonly', ''); area.style.position = 'fixed'; area.style.opacity = '0'; document.body.appendChild(area); area.select(); try { document.execCommand('copy'); done() } finally { area.remove() } }; if (window.navigator.clipboard && window.isSecureContext) { window.navigator.clipboard.writeText(text).then(done, fallback) } else { fallback() } }";
@endphp

<div class="grid gap-6 lg:grid-cols-4">
    {{-- Sidebar --}}
    <aside
        class="flex flex-col gap-4 lg:col-span-1 lg:sticky lg:top-20 lg:max-h-[calc(100vh-6rem)] lg:self-start lg:overflow-y-auto"
        x-data="{ q: '', open: window.matchMedia('(min-width: 1024px)').matches, matches(text) { return ! this.q || text.includes(this.q.toLowerCase().trim()) } }"
    >
        @if ($modules !== [])
            <div
                class="flex flex-wrap gap-2"
                role="tablist"
                aria-label="{{ __('support::filament/pages/help-developers.modules.label') }}"
            >
                @foreach ($modules as $key => $item)
                    @php($isCurrent = $module && $module['key'] === $key)

                    <button
                        type="button"
                        role="tab"
                        aria-selected="{{ $isCurrent ? 'true' : 'false' }}"
                        wire:click="selectApiModule('{{ $key }}')"
                        @class([
                            'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm ring-1 transition',
                            'bg-primary-600 font-semibold text-white ring-primary-600 dark:bg-primary-500 dark:ring-primary-500' => $isCurrent,
                            'bg-white text-gray-700 ring-gray-200 hover:bg-gray-50 dark:bg-white/5 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-white/10' => ! $isCurrent,
                        ])
                    >
                        <span>{{ __('support::filament/pages/help-developers.modules.'.$key) }}</span>
                        <span @class(['text-xs', 'text-white/80' => $isCurrent, 'text-gray-400' => ! $isCurrent])>{{ $item['count'] }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        <button
            type="button"
            wire:click="selectApiResource(null)"
            @class([
                'flex items-center gap-2 rounded-lg px-3 py-2 text-left text-sm',
                'bg-primary-500/10 font-semibold text-primary-600 dark:text-primary-400' => ! $resource,
                'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' => (bool) $resource,
            ])
        >
            <x-filament::icon icon="heroicon-m-rocket-launch" class="size-4 shrink-0" />
            <span>{{ __('support::filament/pages/help-developers.getting_started.title') }}</span>
        </button>

        @if ($module)
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass" :valid="true">
                <x-filament::input
                    type="search"
                    x-model="q"
                    :placeholder="__('support::filament/pages/help-developers.search.placeholder', ['module' => $moduleLabel])"
                    :aria-label="__('support::filament/pages/help-developers.search.placeholder', ['module' => $moduleLabel])"
                />
            </x-filament::input.wrapper>

            {{-- On a phone the list starts folded, so the content is not pushed a hundred rows down. --}}
            <button
                type="button"
                class="flex items-center justify-between rounded-lg px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-200 lg:hidden dark:text-gray-200 dark:ring-white/10"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open ? 'true' : 'false'"
            >
                <span>{{ trans_choice('support::filament/pages/help-developers.badges.total', $module['count'], ['count' => $module['count']]) }}</span>
                <x-filament::icon icon="heroicon-m-chevron-down" class="size-4" />
            </button>

            <nav class="flex flex-col gap-4" x-show="open || q" x-cloak>
                @foreach ($module['resources'] as $group)
                    <div x-show="! q || Array.from($el.querySelectorAll('[data-search]')).some((item) => matches(item.dataset.search))">
                        <button
                            type="button"
                            wire:click="selectApiResource('{{ $group['key'] }}')"
                            @class([
                                'w-full px-2 text-left text-xs font-semibold uppercase tracking-wide',
                                'text-primary-600 dark:text-primary-400' => $resource && $resource['key'] === $group['key'],
                                'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => ! ($resource && $resource['key'] === $group['key']),
                            ])
                        >
                            {{ $group['name'] ?: __('support::filament/pages/help-developers.general') }}
                        </button>

                        <ul class="mt-1 flex flex-col">
                            @foreach ($group['endpoints'] as $item)
                                <li
                                    data-search="{{ mb_strtolower($item['title'].' '.$item['method'].' '.$item['path']) }}"
                                    x-show="matches($el.dataset.search)"
                                >
                                    <button
                                        type="button"
                                        wire:click="selectApiEndpoint('{{ $item['id'] }}')"
                                        class="flex w-full items-center gap-2 rounded-lg px-2 py-1.5 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5"
                                    >
                                        <x-filament::badge size="sm" :color="\Webkul\Support\Services\ApiReference::methodColor($item['method'])" class="w-16 shrink-0 justify-center">
                                            {{ $item['method'] }}
                                        </x-filament::badge>

                                        <span class="min-w-0 truncate">{{ $item['title'] }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <p
                    class="px-2 text-sm text-gray-500 dark:text-gray-400"
                    x-show="q && ! Array.from($el.parentElement.querySelectorAll('[data-search]')).some((item) => matches(item.dataset.search))"
                >
                    {{ __('support::filament/pages/help-developers.search.empty') }}
                </p>
            </nav>
        @endif
    </aside>

    {{-- Content --}}
    <div class="flex min-w-0 flex-col gap-6 lg:col-span-3">
        <div class="flex flex-col gap-2">
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::badge color="primary">
                    {{ __('support::filament/pages/help-developers.badges.version', ['name' => $start['product']]) }}
                </x-filament::badge>

                <x-filament::badge color="gray">
                    {{ trans_choice('support::filament/pages/help-developers.badges.total', $total, ['count' => $total]) }}@if ($module) · {{ __('support::filament/pages/help-developers.badges.in_module', ['count' => $module['count'], 'module' => $moduleLabel]) }}@endif
                </x-filament::badge>
            </div>

            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">
                @if ($resource)
                    {{ $resource['name'] ?: __('support::filament/pages/help-developers.general') }}
                    <span class="text-base font-normal text-gray-500 dark:text-gray-400">· {{ $moduleLabel }}</span>
                @else
                    {{ __('support::filament/pages/help-developers.heading') }}
                @endif
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if ($resource && filled($resource['description']))
                    {{ $resource['description'] }}
                @else
                    {{ __('support::filament/pages/help-developers.intro') }}
                @endif
            </p>
        </div>

        @if ($resource)
            @foreach ($resource['endpoints'] as $endpoint)
                @include('support::pages.partials.help-api-endpoint', ['endpoint' => $endpoint, 'copy' => $copy, 'appUrl' => $start['app_url'], 'companyId' => $start['company_id']])
            @endforeach
        @else
            @if ($modules === [])
                <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                    <x-filament::icon icon="heroicon-o-puzzle-piece" class="size-8 text-gray-400" />

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('support::filament/pages/help-developers.empty') }}
                    </p>
                </div>
            @endif

            <x-filament::section :heading="__('support::filament/pages/help-developers.getting_started.title')" icon="heroicon-o-rocket-launch">
                <dl class="grid gap-x-6 gap-y-5 text-sm md:grid-cols-2">
                    <div class="flex min-w-0 flex-col gap-1 md:col-span-2">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.base_url') }}</dt>
                        <dd
                            class="flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 ring-1 ring-gray-200 dark:bg-white/5 dark:ring-white/10"
                            x-data="{ {{ $copy }} }"
                        >
                            <code class="min-w-0 flex-1 break-all font-mono text-gray-950 dark:text-white" x-ref="text">{{ $start['base_url'] }}</code>

                            <x-filament::link tag="button" type="button" size="sm" icon="heroicon-m-clipboard-document" x-on:click="copy($refs.text.textContent)">
                                <span x-show="! copied">{{ __('support::filament/pages/help-developers.copy') }}</span>
                                <span x-show="copied" x-cloak>{{ __('support::filament/pages/help-developers.copied') }}</span>
                            </x-filament::link>
                        </dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.authentication') }}</dt>
                        <dd class="flex flex-col gap-1 text-gray-600 dark:text-gray-300">
                            <code class="break-all font-mono text-gray-950 dark:text-white">Authorization: Bearer {{ \Webkul\Support\Services\ApiCodeSamples::TOKEN }}</code>
                            <span>{{ __('support::filament/pages/help-developers.getting_started.authentication_help') }}</span>
                        </dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.token') }}</dt>
                        <dd class="flex flex-col items-start gap-2 text-gray-600 dark:text-gray-300">
                            @if ($start['tokens_url'])
                                <span>{{ __('support::filament/pages/help-developers.getting_started.token_page') }}</span>

                                <x-filament::button tag="a" :href="$start['tokens_url']" size="sm" icon="heroicon-m-key">
                                    {{ __('support::filament/pages/help-developers.getting_started.token_page_button') }}
                                </x-filament::button>
                            @endif

                            <span>{{ __('support::filament/pages/help-developers.getting_started.token_login') }}</span>
                        </dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.scopes') }}</dt>
                        <dd class="text-gray-600 dark:text-gray-300">{{ __('support::filament/pages/help-developers.getting_started.scopes_help') }}</dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.company') }}</dt>
                        <dd class="flex flex-col gap-1 text-gray-600 dark:text-gray-300">
                            <code class="break-all font-mono text-gray-950 dark:text-white">X-Company-Id: {{ $start['company_id'] ?? '<id>' }}</code>
                            <span>{{ __('support::filament/pages/help-developers.getting_started.company_help') }}</span>

                            @if ($start['company_id'])
                                <span>{{ __('support::filament/pages/help-developers.getting_started.company_current', ['id' => $start['company_id']]) }}</span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.limits') }}</dt>
                        <dd class="flex flex-col gap-1 text-gray-600 dark:text-gray-300">
                            <span>{{ trans_choice('support::filament/pages/help-developers.getting_started.rate_limit', $start['rate_limit'], ['count' => $start['rate_limit']]) }}</span>

                            <span>
                                @if ($start['expires'])
                                    {{ __('support::filament/pages/help-developers.getting_started.expiry', ['time' => $start['expires']]) }}
                                @else
                                    {{ __('support::filament/pages/help-developers.getting_started.no_expiry') }}
                                @endif
                            </span>
                        </dd>
                    </div>

                    <div class="flex min-w-0 flex-col gap-1">
                        <dt class="font-semibold text-gray-950 dark:text-white">{{ __('support::filament/pages/help-developers.getting_started.format') }}</dt>
                        <dd class="flex flex-col gap-1 text-gray-600 dark:text-gray-300">
                            <code class="font-mono text-gray-950 dark:text-white">Accept: application/json</code>
                            <span>{{ __('support::filament/pages/help-developers.getting_started.format_help') }}</span>
                        </dd>
                    </div>
                </dl>
            </x-filament::section>

            @foreach ($this->apiAuthEndpoints() as $endpoint)
                @include('support::pages.partials.help-api-endpoint', ['endpoint' => $endpoint, 'copy' => $copy, 'appUrl' => $start['app_url'], 'companyId' => $start['company_id']])
            @endforeach
        @endif

        <x-filament::section>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('support::filament/pages/help-developers.full_reference.title') }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('support::filament/pages/help-developers.full_reference.description') }}
                    </p>
                </div>

                <x-filament::button
                    tag="a"
                    :href="$start['docs_url']"
                    target="_blank"
                    rel="noopener"
                    icon="heroicon-m-arrow-top-right-on-square"
                    color="gray"
                >
                    {{ __('support::filament/pages/help-developers.full_reference.button') }}
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</div>
