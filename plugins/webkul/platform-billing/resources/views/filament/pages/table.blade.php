<x-filament-panels::page>
    @foreach ($this->summaryLines() as $line)
        <p class="text-sm leading-6">{{ $line }}</p>
    @endforeach

    {{ $this->table }}
</x-filament-panels::page>
