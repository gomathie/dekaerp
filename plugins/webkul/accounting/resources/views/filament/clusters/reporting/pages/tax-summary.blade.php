{{-- Tax Summary (accounting plan A7). Same table conventions as the other report pages. --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @include('accounting::filament.clusters.reporting.pages.partials.currency-notice')

        @php
            $data = $this->taxSummaryData;
            $lang = 'accounting::filament/clusters/reporting.pages.tax-summary';
            $sections = [
                ['label' => __($lang.'.sections.sales'), 'rows' => $data['sales'], 'base' => $data['totals']['sales_base'], 'tax' => $data['totals']['collected'], 'total' => __($lang.'.totals.collected')],
                ['label' => __($lang.'.sections.purchases'), 'rows' => $data['purchases'], 'base' => $data['totals']['purchases_base'], 'tax' => $data['totals']['paid'], 'total' => __($lang.'.totals.paid')],
            ];
        @endphp

        <x-filament::section>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.tax') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.rate') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.base') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.amount') }}</th>
                        </tr>
                    </thead>

                    @foreach ($sections as $section)
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <td colspan="4" class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $section['label'] }}</td>
                            </tr>

                            @forelse ($section['rows'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-500 dark:text-gray-400">{{ $row['rate'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($row['base'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($row['tax'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ __($lang.'.empty') }}</td>
                                </tr>
                            @endforelse

                            <tr>
                                <td colspan="2" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $section['total'] }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($section['base'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($section['tax'], 2) }}</td>
                            </tr>
                        </tbody>
                    @endforeach

                    <tfoot class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $data['totals']['net'] >= 0 ? __($lang.'.totals.net-payable') : __($lang.'.totals.net-reclaimable') }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format(abs($data['totals']['net']), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.note') }}</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
