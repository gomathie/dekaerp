@php
    $lang = 'sales::filament/pages/zoho-import';
    $run = $this->currentRun();
    $mapping = $run?->mapping ?? [];
@endphp

<x-filament-panels::page>
    <div @if ($run?->isActive()) wire:poll.5s @endif class="flex flex-col gap-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($lang.'.intro') }}</p>

        @if (! $run)
            <x-filament::section>
                <p class="text-sm">{{ __($lang.'.empty') }}</p>
            </x-filament::section>
        @else
            <x-filament::section :heading="__($lang.'.run.heading', ['company' => $run->company?->name])">
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __($lang.'.run.status') }}</dt>
                        <dd class="font-medium">{{ __($lang.'.modes.'.($run->mode ?? 'import')) }} · {{ __($lang.'.statuses.'.$run->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __($lang.'.run.started-by') }}</dt>
                        <dd class="font-medium">{{ $run->user?->name }} · {{ $run->created_at?->toDateTimeString() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">{{ __($lang.'.run.source') }}</dt>
                        <dd class="font-medium">
                            @foreach ($mapping['source']['counts'] ?? [] as $kind => $count)
                                {{ $kind }} {{ $count }}@if (! $loop->last), @endif
                            @endforeach
                        </dd>
                    </div>
                </dl>

                @if ($run->message)
                    <p class="mt-4 text-sm text-danger-600 dark:text-danger-400">{{ $run->message }}</p>
                @endif

                @if ($run->isActive())
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ __($lang.'.run.working') }}</p>
                @endif
            </x-filament::section>

            @if ($run->status === \Webkul\Sale\Models\ZohoImportRun::PROPOSED)
                <x-filament::section :heading="__($lang.'.sections.accounts')" collapsible>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.zoho') }}</th>
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.uses') }}</th>
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.action') }}</th>
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.deka') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (collect($mapping['accounts'] ?? [])->where('action', '!=', 'unused') as $account)
                                    <tr class="border-b border-gray-100 dark:border-white/5">
                                        <td class="py-1 pe-4">{{ $account['zoho_name'] }}</td>
                                        <td class="py-1 pe-4">{{ $account['used'] }}</td>
                                        <td class="py-1 pe-4">{{ __($lang.'.actions.'.$account['action']) }}</td>
                                        <td class="py-1 pe-4">{{ $account['code'] }} {{ $account['name'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">{{ __($lang.'.unused', ['count' => collect($mapping['accounts'] ?? [])->where('action', 'unused')->count()]) }}</p>
                </x-filament::section>

                <x-filament::section :heading="__($lang.'.sections.taxes')" collapsible>
                    <ul class="list-disc ps-5 text-sm">
                        @foreach ($mapping['taxes'] ?? [] as $tax)
                            <li>{{ $tax['zoho_name'] }} → {{ __($lang.'.actions.'.$tax['action']) }}: {{ $tax['name'] }} ({{ $tax['used'] }})</li>
                        @endforeach
                    </ul>
                </x-filament::section>

                <x-filament::section :heading="__($lang.'.sections.banks')" collapsible>
                    <ul class="list-disc ps-5 text-sm">
                        @foreach ($mapping['bank_accounts'] ?? [] as $bank)
                            <li>{{ $bank['zoho_name'] }} ({{ number_format((float) $bank['balance'], 2) }}) → {{ $bank['journal_code'] }} {{ $bank['journal_name'] }}</li>
                        @endforeach
                    </ul>
                </x-filament::section>

                <x-filament::section :heading="__($lang.'.sections.records')" collapsible>
                    <ul class="list-disc ps-5 text-sm">
                        <li>{{ __($lang.'.records.items', ['create' => $this->actionCounts('items')['create'] ?? 0, 'map' => $this->actionCounts('items')['map'] ?? 0]) }}</li>
                        <li>{{ __($lang.'.records.contacts', ['create' => $this->actionCounts('contacts')['create'] ?? 0, 'map' => $this->actionCounts('contacts')['map'] ?? 0]) }}</li>
                        @foreach ($mapping['sequences'] ?? [] as $kind => $sequence)
                            @if ($sequence && ($sequence['action'] ?? 'keep') === 'apply')
                                <li>{{ __($lang.'.records.numbering', ['kind' => $kind, 'next' => $sequence['example']]) }}</li>
                            @elseif ($sequence)
                                <li>{{ __($lang.'.records.numbering-deka', ['kind' => $kind]) }}</li>
                            @endif
                        @endforeach
                    </ul>
                </x-filament::section>
            @endif

            @if ($run->comparison)
                <x-filament::section :heading="__($lang.'.sections.unmatched')">
                    @forelse ($run->comparison['unmatched'] ?? [] as $document)
                        <p @class(['text-sm', 'text-danger-600 dark:text-danger-400' => $document['side'] === 'zoho', 'text-warning-600 dark:text-warning-400' => $document['side'] === 'deka'])>{{ $document['text'] }}</p>
                    @empty
                        <p class="text-sm">{{ __($lang.'.unmatched.none') }}</p>
                    @endforelse
                </x-filament::section>
            @endif

            @if ($run->counts)
                <x-filament::section :heading="__($lang.'.sections.result')">
                    <ul class="list-disc ps-5 text-sm">
                        @foreach ($run->counts as $what => $count)
                            <li>{{ $what }}: {{ $count }}</li>
                        @endforeach
                    </ul>
                </x-filament::section>
            @endif

            @if ($run->comparison)
                @php($differences = collect($run->comparison['rows'] ?? [])->where('status', 'DIFF')->count())

                <x-filament::section :heading="__($lang.'.sections.comparison')" :description="$differences ? __($lang.'.comparison.differences', ['count' => $differences]) : __($lang.'.comparison.all-ok')">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-white/10">
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.section') }}</th>
                                    <th class="py-2 pe-4">{{ __($lang.'.columns.row') }}</th>
                                    <th class="py-2 pe-4 text-end">Zoho</th>
                                    <th class="py-2 pe-4 text-end">DEKA</th>
                                    <th class="py-2 pe-4 text-end">{{ __($lang.'.columns.difference') }}</th>
                                    <th class="py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($run->comparison['rows'] ?? [] as $row)
                                    <tr @class(['border-b border-gray-100 dark:border-white/5', 'text-danger-600 dark:text-danger-400 font-medium' => $row['status'] === 'DIFF'])>
                                        <td class="py-1 pe-4">{{ $row['section'] }}</td>
                                        <td class="py-1 pe-4">{{ $row['label'] }}</td>
                                        <td class="py-1 pe-4 text-end">{{ number_format($row['zoho'], 2) }}</td>
                                        <td class="py-1 pe-4 text-end">{{ number_format($row['deka'], 2) }}</td>
                                        <td class="py-1 pe-4 text-end">{{ number_format($row['difference'], 2) }}</td>
                                        <td class="py-1">{{ $row['status'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>
            @endif
        @endif
    </div>
</x-filament-panels::page>
