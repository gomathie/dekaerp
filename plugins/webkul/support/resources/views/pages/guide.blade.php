{{--
    The user guide, one article at a time.

    Every class here is already in the compiled support.css or Filament's own
    build: the deka-guide-* classes for article bodies, and the same Tailwind
    utilities help.blade.php uses. Adding a new class would mean rebuilding
    support.css from the project root and copying it to public/css/support,
    which is not worth it for layout this plain.
--}}
<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-4">
        {{-- Navigation --}}
        <div class="flex flex-col gap-4 lg:col-span-1">
            {{--
                Audience switch. Hidden for a viewer with only one guide, which
                is every ordinary user: a single-choice control is noise.
            --}}
            @php($audiences = $this->audiences())

            @if (count($audiences) > 1)
                <div
                    class="flex flex-col gap-1 rounded-xl bg-gray-50 p-1 dark:bg-white/5"
                    role="tablist"
                    aria-label="{{ __('support::filament/pages/guide.audience.label') }}"
                >
                    @foreach ($audiences as $audience)
                        @php($isCurrent = $audience === $this->currentAudience())

                        <button
                            type="button"
                            role="tab"
                            aria-selected="{{ $isCurrent ? 'true' : 'false' }}"
                            wire:click="selectAudience('{{ $audience->value }}')"
                            @class([
                                'flex items-center gap-2 rounded-lg px-3 py-2 text-left text-sm',
                                'bg-white font-semibold text-primary-600 shadow-sm dark:bg-gray-900 dark:text-primary-400' => $isCurrent,
                                'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => ! $isCurrent,
                            ])
                        >
                            <x-filament::icon :icon="$audience->getIcon()" class="size-4 shrink-0" />

                            <span class="truncate">{{ $audience->getLabel() }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            <x-filament::input.wrapper
                prefix-icon="heroicon-m-magnifying-glass"
                :valid="true"
            >
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    :placeholder="__('support::filament/pages/guide.search.placeholder')"
                />
            </x-filament::input.wrapper>

            <div class="flex flex-col gap-1">
                @foreach ($this->categories() as $category)
                    @php($isActive = ! $this->isSearching() && $category['id'] === $this->categoryId)

                    <button
                        type="button"
                        wire:click="selectCategory('{{ $category['id'] }}')"
                        @class([
                            'flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-left text-sm',
                            'bg-primary-500/10 font-semibold text-primary-600 dark:text-primary-400' => $isActive,
                            'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' => ! $isActive,
                        ])
                    >
                        <span class="flex min-w-0 items-center gap-2">
                            @if (filled($category['icon'] ?? null))
                                <x-filament::icon :icon="$category['icon']" class="size-4 shrink-0" />
                            @endif

                            <span class="truncate">{{ $category['name'] }}</span>
                        </span>

                        <span class="shrink-0 text-xs text-gray-400">{{ $category['count'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Content: search results, or an article, or the category's articles --}}
        <div class="lg:col-span-3">
            @if ($this->isSearching())
                @php($results = $this->searchResults())

                <x-filament::section>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ trans_choice('support::filament/pages/guide.search.results', count($results), ['count' => count($results)]) }}
                            </span>

                            <x-filament::link tag="button" wire:click="clearSearch" size="sm">
                                {{ __('support::filament/pages/guide.search.clear') }}
                            </x-filament::link>
                        </div>

                        @if ($results === [])
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('support::filament/pages/guide.search.empty', ['term' => $this->search]) }}
                            </p>
                        @else
                            <div class="flex flex-col gap-1">
                                @foreach ($results as $result)
                                    <button
                                        type="button"
                                        wire:click="selectArticle('{{ $result['id'] }}')"
                                        class="flex flex-col gap-1 rounded-lg px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-white/5"
                                    >
                                        <span class="flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $result['title'] }}</span>
                                            <span class="text-xs text-gray-400">{{ $result['category'] }}</span>
                                        </span>

                                        @if (filled($result['summary'] ?? null))
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $result['summary'] }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </x-filament::section>
            @elseif ($article = $this->article())
                <x-filament::section>
                    <div class="flex flex-col gap-5">
                        <div class="flex flex-col gap-2">
                            <x-filament::link tag="button" wire:click="clearArticle" size="sm" icon="heroicon-m-arrow-left">
                                {{ $this->categoryName() }}
                            </x-filament::link>

                            <h2 class="text-xl font-semibold text-gray-950 dark:text-white">
                                {{ $article['title'] }}
                            </h2>

                            @if (filled($article['summary'] ?? null))
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $article['summary'] }}</p>
                            @endif

                            @if (filled($article['readTime'] ?? null))
                                <span class="text-xs text-gray-400">{{ $article['readTime'] }}</span>
                            @endif
                        </div>

                        @if (! empty($article['workflow']))
                            <div class="deka-guide-workflow">
                                @foreach ($article['workflow'] as $workflowStep)
                                    <span>{{ $workflowStep }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Unescaped: guide.json is a repo asset, not user input. --}}
                        @if (! empty($article['htmlContent']))
                            <div class="deka-guide-content">
                                {!! $article['htmlContent'] !!}
                            </div>
                        @endif

                        @if (! empty($article['steps']))
                            <div class="space-y-5">
                                @foreach ($article['steps'] as $step)
                                    <section class="deka-guide-step">
                                        <h4>{{ $step['title'] }}</h4>

                                        @if (filled($step['description'] ?? null))
                                            <p>{{ $step['description'] }}</p>
                                        @endif

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
                </x-filament::section>
            @else
                <x-filament::section>
                    <div class="flex flex-col gap-4">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            {{ $this->categoryName() }}
                        </span>

                        <div class="flex flex-col gap-1">
                            @foreach ($this->articleList() as $item)
                                <button
                                    type="button"
                                    wire:click="selectArticle('{{ $item['id'] }}')"
                                    class="flex flex-col gap-1 rounded-lg px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-white/5"
                                >
                                    <span class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $item['title'] }}</span>

                                        @if (filled($item['readTime'] ?? null))
                                            <span class="text-xs text-gray-400">{{ $item['readTime'] }}</span>
                                        @endif
                                    </span>

                                    @if (filled($item['summary'] ?? null))
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $item['summary'] }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>
