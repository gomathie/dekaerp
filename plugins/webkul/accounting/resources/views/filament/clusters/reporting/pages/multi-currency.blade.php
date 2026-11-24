{{-- Multi-currency report (plan #11 MC-3, 2026-11-24). Figures from MultiCurrencyReport; every amount names its currency. --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @php
            $data = $this->reportData;
            $lang = 'accounting::filament/clusters/reporting.pages.multi-currency';
            $base = $data['base'];
            $money = fn (?float $value): string => $value === null ? '—' : number_format($value, 2);
            // A difference always carries its sign, so it never rests on colour alone.
            $signed = fn (?float $value): string => $value === null ? '—' : ($value > 0 ? '+' : ($value < 0 ? '−' : '')).number_format(abs($value), 2);
            $tone = fn (?float $value): string => match (true) {
                $value === null, $value == 0.0 => 'text-gray-500 dark:text-gray-400',
                $value > 0 => 'text-success-600 dark:text-success-400',
                default => 'text-danger-600 dark:text-danger-400',
            };
        @endphp

        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __($lang.'.note', ['base' => $base ?? '—']) }}</p>

        @if ($data['currencies'] === [])
            <x-filament::section>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __($lang.'.empty') }}</p>
            </x-filament::section>
        @else
            {{-- One card per currency; one column on a phone. --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($data['currencies'] as $row)
                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                        <div class="flex items-center justify-between gap-2">
                            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-semibold tracking-wide text-gray-700 dark:bg-white/10 dark:text-gray-200">{{ $row['code'] }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ trans_choice($lang.'.documents-count', $row['count'], ['count' => $row['count']]) }}
                                @if ($row['is_base'])
                                    · {{ __($lang.'.company-currency') }}
                                @endif
                            </span>
                        </div>

                        <dl class="mt-3 grid grid-cols-3 gap-2">
                            @foreach ([['invoiced', 'invoiced_base'], ['received', 'received_base'], ['open', 'open_base']] as [$key, $baseKey])
                                <div class="min-w-0">
                                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.figures.'.$key) }}</dt>
                                    <dd class="truncate text-base font-semibold tabular-nums text-gray-950 dark:text-white">{{ $money($row[$key]) }}</dd>
                                    @unless ($row['is_base'])
                                        <dd class="truncate text-xs tabular-nums text-gray-500 dark:text-gray-400">≈ {{ $base }} {{ $money($row[$baseKey]) }}</dd>
                                    @endunless
                                </div>
                            @endforeach
                        </dl>

                        @if ($row['average_rate'])
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.average-rate', ['rate' => $row['average_rate']]) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- The same figures side by side, in the company currency. --}}
            <x-filament::section :heading="__($lang.'.sections.by-currency')">
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50/50 dark:bg-white/5">
                            <tr>
                                @foreach (['currency', 'invoiced', 'average-rate', 'invoiced-base', 'open', 'open-base', 'open-today', 'difference'] as $column)
                                    <th class="whitespace-nowrap px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 {{ $column === 'currency' ? 'text-left' : 'text-right' }}">
                                        {{ __($lang.'.columns.'.$column, ['base' => $base]) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($data['currencies'] as $row)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['code'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($row['invoiced']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-500 dark:text-gray-400">{{ $row['average_rate'] ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($row['invoiced_base']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($row['open']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($row['open_base']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($row['open_base_today']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium tabular-nums {{ $tone($row['difference']) }}">{{ $signed($row['difference']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">{{ __($lang.'.totals.total', ['base' => $base]) }}</td>
                                <td></td>
                                <td></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ $money($data['totals']['invoiced_base']) }}</td>
                                <td></td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ $money($data['totals']['open_base']) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ $money($data['totals']['open_base_today']) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-bold tabular-nums {{ $tone($data['totals']['difference']) }}">{{ $signed($data['totals']['difference']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.today-note') }}</p>
            </x-filament::section>
        @endif

        {{-- Gains and losses the ledger booked, so the report agrees with it. --}}
        <x-filament::section :heading="__($lang.'.sections.exchange')">
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                @foreach (['gains' => $data['exchange']['gains'], 'losses' => -$data['exchange']['losses'], 'net' => $data['exchange']['net']] as $key => $value)
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.exchange.'.$key) }}</dt>
                        <dd class="text-lg font-semibold tabular-nums {{ $tone($value) }}">{{ $base }} {{ $signed($value) }}</dd>
                    </div>
                @endforeach
            </dl>
        </x-filament::section>

        <x-filament::section :heading="__($lang.'.sections.documents')">
            @if ($data['documents'] === [])
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __($lang.'.empty') }}</p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                        <thead class="bg-gray-50/50 dark:bg-white/5">
                            <tr>
                                @foreach (['number', 'date', 'contact', 'amount', 'rate', 'base-amount', 'status'] as $column)
                                    <th class="whitespace-nowrap px-4 py-3 text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 {{ in_array($column, ['amount', 'base-amount'], true) ? 'text-right' : 'text-left' }}">
                                        {{ __($lang.'.columns.'.$column, ['base' => $base]) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            @foreach ($data['documents'] as $document)
                                @php $url = $this->documentUrl($document); @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium">
                                        @if ($url)
                                            <a href="{{ $url }}" class="text-primary-600 hover:underline dark:text-primary-400">{{ $document['name'] }}</a>
                                        @else
                                            <span class="text-gray-900 dark:text-gray-100">{{ $document['name'] }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $document['date']?->translatedFormat('j M Y') ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $document['partner'] ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $document['code'] }} {{ $money($document['amount']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $document['rate'] ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ $money($document['base']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $this->paymentStateLabel($document['state']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (count($data['documents']) >= \Webkul\Accounting\Support\MultiCurrencyReport::DOCUMENT_LIMIT)
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.documents-limit', ['count' => \Webkul\Accounting\Support\MultiCurrencyReport::DOCUMENT_LIMIT]) }}</p>
                @endif
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
