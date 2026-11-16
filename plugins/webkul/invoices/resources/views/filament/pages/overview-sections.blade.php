@if (count($sections) > 1)
    {{--
        On a phone the tabs run past the screen edge and the last one was cut
        mid-word with no sign the row scrolls (UX finding, 2026-11-12). The fade
        shows only on the side that still has hidden tabs, so it never dims a
        tab you have already scrolled to.
    --}}
    <style>
        .overview-sections { display: flex; justify-content: center; }
        .overview-sections .fi-tabs { scrollbar-width: none; }
        .overview-sections .fi-tabs::-webkit-scrollbar { display: none; }
        .overview-sections[data-fade~="end"] .fi-tabs { mask-image: linear-gradient(to right, #000 calc(100% - 2.5rem), transparent); }
        .overview-sections[data-fade~="start"] .fi-tabs { mask-image: linear-gradient(to left, #000 calc(100% - 2.5rem), transparent); }
        .overview-sections[data-fade~="start"][data-fade~="end"] .fi-tabs { mask-image: linear-gradient(to right, transparent, #000 2.5rem, #000 calc(100% - 2.5rem), transparent); }
    </style>

    <div
        class="overview-sections"
        x-data="{ fade: '' }"
        x-init="
            const row = $el.querySelector('.fi-tabs');
            const update = () => {
                const max = row.scrollWidth - row.clientWidth;
                fade = [row.scrollLeft > 4 ? 'start' : '', row.scrollLeft < max - 4 ? 'end' : ''].join(' ').trim();
            };
            row.addEventListener('scroll', update, { passive: true });
            new ResizeObserver(update).observe(row);
            row.querySelector('[aria-selected=true]')?.scrollIntoView({ block: 'nearest', inline: 'center' });
            update();
        "
        :data-fade="fade"
    >
        <x-filament::tabs :label="__('invoices::filament/pages/overview.filters.sections')">
            <x-filament::tabs.item
                :active="$active === null"
                wire:click="selectSection()"
            >
                {{ __('invoices::filament/pages/overview.filters.all-sections') }}
            </x-filament::tabs.item>

            @foreach ($sections as $key => $label)
                <x-filament::tabs.item
                    :active="$active === $key"
                    wire:click="selectSection('{{ $key }}')"
                >
                    {{ $label }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>
@endif
