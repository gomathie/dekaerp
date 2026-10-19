<x-filament-panels::page>
    <x-filament::tabs>
        @foreach ($this->getTabs() as $key => $label)
            <x-filament::tabs.item
                :active="$activeTab === $key"
                wire:click="$set('activeTab', '{{ $key }}')"
            >
                {{ $label }}
            </x-filament::tabs.item>
        @endforeach
    </x-filament::tabs>

    {{ $this->table }}
</x-filament-panels::page>
