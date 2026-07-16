<div class="flex items-center">
    @if ($companies->count() > 1)
        <x-filament::dropdown placement="bottom-end" teleport>
            <x-slot name="trigger">
                <button
                    type="button"
                    class="flex items-center gap-x-1.5 rounded-lg px-2 py-1.5 text-sm font-medium text-gray-700 outline-hidden transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:text-gray-200 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
                >
                    <x-filament::icon
                        icon="heroicon-m-building-office-2"
                        class="h-5 w-5 text-gray-400 dark:text-gray-500"
                    />

                    <span class="hidden sm:inline">
                        @if ($selection === \Webkul\Support\Services\CurrentCompany::ALL)
                            {{ __('support::support.company-switcher.all-companies') }}
                        @else
                            {{ $activeCompany?->name ?? __('support::support.company-switcher.select-company') }}
                        @endif
                    </span>

                    <x-filament::icon
                        icon="heroicon-m-chevron-down"
                        class="h-4 w-4 text-gray-400 dark:text-gray-500"
                    />
                </button>
            </x-slot>

            <x-filament::dropdown.list>
                @foreach ($companies as $company)
                    <x-filament::dropdown.list.item
                        :icon="$selection === $company->id ? 'heroicon-m-check-circle' : 'heroicon-o-building-office'"
                        :color="$selection === $company->id ? 'primary' : 'gray'"
                        wire:click="switchCompany({{ $company->id }})"
                    >
                        {{ $company->name }}
                    </x-filament::dropdown.list.item>
                @endforeach

                <x-filament::dropdown.list.item
                    :icon="$selection === \Webkul\Support\Services\CurrentCompany::ALL ? 'heroicon-m-check-circle' : 'heroicon-o-squares-2x2'"
                    :color="$selection === \Webkul\Support\Services\CurrentCompany::ALL ? 'primary' : 'gray'"
                    wire:click="switchCompany('{{ \Webkul\Support\Services\CurrentCompany::ALL }}')"
                >
                    {{ __('support::support.company-switcher.all-companies') }}
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    @endif
</div>
