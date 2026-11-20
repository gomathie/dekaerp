<x-filament-panels::page>
    <div class="flex flex-col gap-6">
        @if ($this->hasReleases())
            @foreach ($this->releases() as $index => $release)
                <details
                    @if ($index === 0) open @endif
                    class="group rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="text-base font-semibold text-gray-950 dark:text-white">
                                {{ $release['version'] }}
                            </span>

                            @if ($index === 0)
                                <span class="rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                                    {{ __('support::filament/pages/whats-new.latest') }}
                                </span>
                            @endif
                        </div>

                        <x-filament::icon
                            icon="heroicon-o-chevron-down"
                            class="size-4 shrink-0 text-gray-400 transition-transform group-open:rotate-180"
                        />
                    </summary>

                    <div class="flex flex-col gap-6 border-t border-gray-200 px-6 py-5 dark:border-gray-700">
                        @foreach ($release['sections'] as $section)
                            @if ($section['items'] !== [])
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center gap-2">
                                        <x-filament::icon
                                            :icon="$this->sectionIcon($section['label'])"
                                            class="size-4 text-gray-400"
                                        />

                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                            {{ $section['label'] }}
                                        </span>
                                    </div>

                                    <ul class="flex flex-col gap-2 pl-6 text-sm text-gray-700 marker:text-gray-400 dark:text-gray-300">
                                        @foreach ($section['items'] as $item)
                                            <li class="list-disc [&_a]:text-primary-600 [&_a]:underline [&_code]:rounded [&_code]:bg-gray-100 [&_code]:px-1 [&_code]:py-0.5 [&_code]:text-xs dark:[&_code]:bg-gray-800">
                                                {!! \Illuminate\Support\Str::inlineMarkdown($item) !!}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </details>
            @endforeach
        @else
            <div class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-700">
                <x-filament::icon icon="heroicon-o-megaphone" class="size-8 text-gray-400" />

                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('support::filament/pages/whats-new.empty') }}
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
