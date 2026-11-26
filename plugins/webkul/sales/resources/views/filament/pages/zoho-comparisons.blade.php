@php
    $lang = 'sales::filament/pages/zoho-comparisons';
    $runs = $this->runs();
    $run = $this->selected();
    $rows = $run?->comparison['rows'] ?? [];
    $unmatched = $run?->comparison['unmatched'] ?? [];
    $differences = collect($rows)->where('status', 'DIFF');
@endphp

<x-filament-panels::page>
    <div class="flex flex-col gap-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($lang.'.intro') }}</p>

        @if (! $run)
            <x-filament::section>
                <p class="text-sm">{{ __($lang.'.empty') }}</p>
            </x-filament::section>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($runs as $item)
                    <x-filament::button
                        size="sm"
                        :color="$item->id === $run->id ? 'primary' : 'gray'"
                        wire:click="selectRun({{ (int) $item->id }})"
                    >
                        {{ $item->finished_at?->toDateString() }}
                    </x-filament::button>
                @endforeach
            </div>

            <x-filament::section :heading="__($lang.'.summary', ['date' => $run->finished_at?->toDayDateTimeString()])">
                <p @class(['text-sm font-medium', 'text-success-600 dark:text-success-400' => $differences->isEmpty() && $unmatched === [], 'text-danger-600 dark:text-danger-400' => $differences->isNotEmpty() || $unmatched !== []])>
                    @if ($differences->isEmpty() && $unmatched === [])
                        {{ __($lang.'.all-ok') }}
                    @else
                        {{ __($lang.'.differences', ['rows' => $differences->count(), 'documents' => count($unmatched)]) }}
                    @endif
                </p>
            </x-filament::section>

            <x-filament::section :heading="__($lang.'.unmatched')">
                @forelse ($unmatched as $document)
                    <p class="text-sm">{{ $document['text'] }}</p>
                @empty
                    <p class="text-sm">{{ __($lang.'.none') }}</p>
                @endforelse
            </x-filament::section>

            <x-filament::section :heading="__($lang.'.totals')" collapsible>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-white/10">
                                <th class="py-2 pe-4">{{ __($lang.'.columns.what') }}</th>
                                <th class="py-2 pe-4 text-end">Zoho</th>
                                <th class="py-2 pe-4 text-end">DEKA</th>
                                <th class="py-2 pe-4 text-end">{{ __($lang.'.columns.difference') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr @class(['border-b border-gray-100 dark:border-white/5', 'text-danger-600 dark:text-danger-400 font-medium' => $row['status'] === 'DIFF'])>
                                    <td class="py-1 pe-4">{{ $row['section'] }} · {{ $row['label'] }}</td>
                                    <td class="py-1 pe-4 text-end">{{ number_format($row['zoho'], 2) }}</td>
                                    <td class="py-1 pe-4 text-end">{{ number_format($row['deka'], 2) }}</td>
                                    <td class="py-1 pe-4 text-end">{{ number_format($row['difference'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
