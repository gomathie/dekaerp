<x-filament-panels::page>
    <div class="flex flex-col gap-8">
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
