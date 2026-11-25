<x-filament-panels::page>
    {{-- Balance check per account (plan #9 R-4). Scoped styles: the
         accounting stylesheet is a prebuilt Tailwind file, so new utility
         classes would not exist in it until it is rebuilt. --}}
    @php($lang = 'accounting::filament/clusters/accounting/pages/bank-reconciliation.balances')
    @php($cards = $this->balanceCards())

    @if ($cards !== [])
        <style>
            .deka-recon { display: grid; gap: 1rem; grid-template-columns: repeat(auto-fill, minmax(min(100%, 17rem), 1fr)); }
            .deka-recon__card { background: #fff; border: 1px solid var(--gray-200); border-radius: 0.75rem; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; min-width: 0; }
            .dark .deka-recon__card { background: var(--gray-900); border-color: rgb(255 255 255 / 0.1); }
            .deka-recon__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 0.5rem; }
            .deka-recon__name { font-weight: 600; font-size: 0.95rem; color: var(--gray-950); overflow-wrap: anywhere; }
            .dark .deka-recon__name { color: #fff; }
            .deka-recon__type { font-size: 0.75rem; color: var(--gray-500); }
            .deka-recon__figures { display: grid; grid-template-columns: 1fr auto; gap: 0.25rem 0.75rem; margin: 0; font-size: 0.875rem; }
            .deka-recon__figures dt { color: var(--gray-500); }
            .deka-recon__figures dd { margin: 0; text-align: end; font-variant-numeric: tabular-nums; color: var(--gray-950); }
            .dark .deka-recon__figures dd { color: #fff; }
            .deka-recon__figures .deka-recon__diff { font-weight: 600; }
            .deka-recon__foot { font-size: 0.75rem; color: var(--gray-500); margin: 0; }
        </style>

        <section class="deka-recon" aria-label="{{ __($lang.'.heading') }}">
            @foreach ($cards as $card)
                @php($color = match ($card['state']) { 'clean' => 'success', 'to-match' => 'warning', 'difference' => 'danger', default => 'gray' })
                @php($pill = match ($card['state']) {
                    'clean'      => __($lang.'.state.clean'),
                    'to-match'   => trans_choice($lang.'.state.to-match', $card['to_match'], ['count' => number_format($card['to_match'])]),
                    'difference' => __($lang.'.state.difference'),
                    default      => __($lang.'.state.none'),
                })

                <article class="deka-recon__card">
                    <div class="deka-recon__head">
                        <div>
                            <div class="deka-recon__name">{{ $card['journal']->name }}</div>
                            <div class="deka-recon__type">{{ $card['journal']->type?->getLabel() }}@if ($card['currency']) · {{ $card['currency'] }}@endif</div>
                        </div>

                        <x-filament::badge :color="$color">{{ $pill }}</x-filament::badge>
                    </div>

                    <dl class="deka-recon__figures">
                        <dt>{{ __($lang.'.statement') }}</dt>
                        <dd>{{ $card['statement'] === null ? '—' : money($card['statement'], $card['currency']) }}</dd>

                        <dt>{{ __($lang.'.book') }}</dt>
                        <dd>{{ money($card['book'], $card['currency']) }}</dd>

                        <dt>{{ __($lang.'.difference') }}</dt>
                        <dd class="deka-recon__diff">{{ $card['difference'] === null ? '—' : money($card['difference'], $card['currency']) }}</dd>
                    </dl>

                    <p class="deka-recon__foot">
                        @if ($card['date'])
                            {{ __($lang.'.up-to', ['date' => \Illuminate\Support\Carbon::parse($card['date'])->isoFormat('LL')]) }}
                        @else
                            {{ __($lang.'.no-statement') }}
                        @endif
                    </p>
                </article>
            @endforeach
        </section>

        <p class="deka-recon__foot" style="margin-top: -0.5rem">{{ __($lang.'.explain') }}</p>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
