<x-filament-panels::page>
    <div class="flex flex-col gap-8">
        @if ($this->hasGuides())
            <div class="flex flex-col gap-4">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.guides.group') }}
                </span>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
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

                                <div class="flex-1 space-y-4">
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

                                {{--
                                    Every card now opens its own guide. The admin
                                    card used to show a checklist and link
                                    nowhere, so there was no way through to the
                                    content from here.
                                --}}
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-xs text-gray-400">
                                        {{ trans_choice('support::filament/pages/help.guides.user.category_count', $guide['count'], ['count' => $guide['count']]) }}
                                    </span>

                                    <x-filament::button
                                        tag="a"
                                        :href="$guide['url']"
                                        size="sm"
                                    >
                                        {{ $guide['button'] }}
                                    </x-filament::button>
                                </div>
                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($this->hasUserGuide())
            {{-- Was the whole guide inlined here as nested accordions: 82
                 articles rendered into every response, navigated by scrolling.
                 It lives on its own page now, which renders one article at a
                 time and can be searched and linked to. --}}
            <div class="flex flex-col gap-4">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/help.guides.user.full_heading') }}
                </span>

                <x-filament::section>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="min-w-0">
                            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                {{ __('support::filament/pages/guide.heading') }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ trans_choice('support::filament/pages/guide.subheading', $this->guidePageCount(), ['count' => $this->guidePageCount()]) }}
                            </p>
                        </div>

                        <x-filament::button
                            tag="a"
                            :href="\Webkul\Support\Filament\Pages\Guide::getUrl()"
                            icon="heroicon-m-book-open"
                        >
                            {{ __('support::filament/pages/help.guides.user.button') }}
                        </x-filament::button>
                    </div>
                </x-filament::section>
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
