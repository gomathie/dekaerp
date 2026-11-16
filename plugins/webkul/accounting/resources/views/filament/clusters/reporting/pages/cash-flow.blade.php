{{-- Cash flow statement (accounting plan A6). Same table conventions as the other report pages. --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->form }}

        @include('accounting::filament.clusters.reporting.pages.partials.currency-notice')

        @php
            $data = $this->cashFlowData;
            $lang = 'accounting::filament/clusters/reporting.pages.cash-flow';
        @endphp

        <x-filament::section>
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/5">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-white/5">
                    <thead class="bg-gray-50/50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.account') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.in') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.out') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __($lang.'.columns.net') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __($lang.'.totals.opening') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['opening'], 2) }}</td>
                        </tr>
                    </tbody>

                    @foreach ($data['sections'] as $name => $section)
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <td colspan="4" class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white">{{ __($lang.'.sections.'.$name) }}</td>
                            </tr>

                            @forelse ($section['rows'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row['account'] }}</td>
                                    <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($row['in'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($row['out'], 2) }}</td>
                                    <td class="px-4 py-3 text-right text-sm tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($row['in'] - $row['out'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ __($lang.'.empty') }}</td>
                                </tr>
                            @endforelse

                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __($lang.'.totals.section') }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($section['in'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($section['out'], 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($section['net'], 2) }}</td>
                            </tr>
                        </tbody>
                    @endforeach

                    <tfoot class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ __($lang.'.totals.net') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['net'], 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-white">{{ __($lang.'.totals.closing') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-bold tabular-nums text-gray-900 dark:text-white">{{ number_format($data['closing'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ __($lang.'.note') }}</p>
        </x-filament::section>
    </div>
</x-filament-panels::page>
