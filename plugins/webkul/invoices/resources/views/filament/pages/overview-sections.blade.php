@if (count($sections) > 1)
    <div class="flex justify-center">
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
