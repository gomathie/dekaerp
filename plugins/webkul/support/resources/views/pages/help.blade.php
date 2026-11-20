<x-filament-panels::page>
    <div class="flex flex-col gap-8">
        @if ($this->hasGuides())
            <div class="flex flex-col gap-4">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.guides.group') }}
                </span>

                <div class="grid gap-4 lg:grid-cols-2">
                    @foreach ($this->guides() as $guide)
                        <x-filament::section class="h-full">
                            <div class="flex h-full flex-col gap-5">
                                <div class="flex items-start gap-3">
                                    <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400">
                                        <x-filament::icon :icon="$guide['icon']" class="size-6" />
                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                            {{ $guide['title'] }}
                                        </h3>

                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $guide['description'] }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    @foreach ($guide['sections'] as $section)
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-950 dark:text-white">
                                                {{ $section['title'] }}
                                            </h4>

                                            <ul class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                                                @foreach ($section['items'] as $item)
                                                    <li class="flex gap-2">
                                                        <x-filament::icon icon="heroicon-m-check-circle" class="mt-0.5 size-4 shrink-0 text-primary-600 dark:text-primary-400" />
                                                        <span>{{ $item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($this->hasUserGuide())
            <div class="flex flex-col gap-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ __('support::filament/pages/help.guides.user.full_heading') }}
                    </span>

                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ trans_choice('support::filament/pages/help.guides.user.count', $this->guidePageCount(), ['count' => $this->guidePageCount()]) }}
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($this->guideCategories() as $category)
                        <details @class(['deka-guide-category', 'deka-guide-category--open' => $loop->first]) @if ($loop->first) open @endif>
                            <summary class="deka-guide-category__summary">
                                <span>{{ $category['name'] }}</span>
                                <span>{{ trans_choice('support::filament/pages/help.guides.user.category_count', count($category['items'] ?? []), ['count' => count($category['items'] ?? [])]) }}</span>
                            </summary>

                            <div class="deka-guide-category__body">
                                @foreach (($category['items'] ?? []) as $item)
                                    <details class="deka-guide-page">
                                        <summary class="deka-guide-page__summary">
                                            <span>
                                                <span class="deka-guide-page__title">{{ $item['title'] }}</span>

                                                @if (filled($item['summary'] ?? null))
                                                    <span class="deka-guide-page__summary-text">{{ $item['summary'] }}</span>
                                                @endif
                                            </span>

                                            @if (filled($item['readTime'] ?? null))
                                                <span class="deka-guide-page__meta">{{ $item['readTime'] }}</span>
                                            @endif
                                        </summary>

                                        <div class="deka-guide-page__body">
                                            @if (! empty($item['workflow']))
                                                <div class="deka-guide-workflow">
                                                    @foreach ($item['workflow'] as $workflowStep)
                                                        <span>{{ $workflowStep }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if (! empty($item['htmlContent']))
                                                <div class="deka-guide-content">
                                                    {!! $item['htmlContent'] !!}
                                                </div>
                                            @endif

                                            @if (! empty($item['steps']))
                                                <div class="space-y-5">
                                                    @foreach ($item['steps'] as $step)
                                                        <section class="deka-guide-step">
                                                            <h4>{{ $step['title'] }}</h4>
                                                            <p>{{ $step['description'] }}</p>

                                                            @if (! empty($step['instructions']))
                                                                <ol>
                                                                    @foreach ($step['instructions'] as $instruction)
                                                                        <li>{!! $instruction !!}</li>
                                                                    @endforeach
                                                                </ol>
                                                            @endif

                                                            @foreach (['tip', 'note', 'important'] as $callout)
                                                                @if (filled($step[$callout] ?? null))
                                                                    <div class="deka-guide-callout deka-guide-callout--{{ $callout }}">
                                                                        <span>{{ __('support::filament/pages/help.guides.user.callouts.'.$callout) }}</span>
                                                                        <p>{{ $step[$callout] }}</p>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </section>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        </details>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Sections render only when a card in them has a configured URL. See config/deka.php. --}}
        @if ($this->hasServices())
            <div class="flex flex-col gap-4">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.services.group') }}
                </span>

                {{ $this->servicesInfolist }}
            </div>
        @endif

        @if ($this->hasResources())
            <div class="flex flex-col gap-4">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.resources.group') }}
                </span>

                {{ $this->resourcesInfolist }}
            </div>
        @endif

        @if ($this->contactUrl())
            <div class="from-primary-600 to-primary-500 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-gradient-to-r p-6">
                <div class="flex items-center gap-4">
                    <div class="size-12 flex shrink-0 items-center justify-center rounded-full bg-white/20">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="size-6 text-white" />
                    </div>

                    <div>
                        <div class="text-base font-semibold text-white">
                            {{ __('support::filament/pages/help.contact.title') }}
                        </div>
                        <div class="text-sm text-white/80">
                            {{ __('support::filament/pages/help.contact.description') }}
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 flex-wrap items-center gap-3">
                    <a
                        href="{{ $this->contactUrl() }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-primary-600 inline-flex shrink-0 items-center gap-2 whitespace-nowrap rounded-lg bg-white px-5 py-2.5 text-sm font-semibold shadow-sm transition hover:bg-gray-100"
                    >
                        <span>{{ __('support::filament/pages/help.contact.button') }}</span>
                        <x-filament::icon icon="heroicon-m-arrow-right" class="size-4" />
                    </a>
                </div>
            </div>
        @endif

        @unless ($this->hasAnyContent())
            <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                <x-filament::icon icon="heroicon-o-lifebuoy" class="size-8 text-gray-400" />

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.empty', ['app' => config('app.name')]) }}
                </div>
            </div>
        @endunless
    </div>
</x-filament-panels::page>
