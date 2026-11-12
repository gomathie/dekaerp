<x-filament-panels::page>
    <div style="display:flex;flex-wrap:wrap;gap:.75rem;align-items:end;margin-bottom:1rem">
        <label style="display:flex;flex-direction:column;gap:.25rem;font-size:.75rem;font-weight:600">
            {{ __('attendances::reports.range.from') }}
            <input type="date" wire:model.live="from" style="padding:.4rem .5rem;border:1px solid rgba(0,0,0,.15);border-radius:.4rem">
        </label>

        <label style="display:flex;flex-direction:column;gap:.25rem;font-size:.75rem;font-weight:600">
            {{ __('attendances::reports.range.until') }}
            <input type="date" wire:model.live="until" style="padding:.4rem .5rem;border:1px solid rgba(0,0,0,.15);border-radius:.4rem">
        </label>
    </div>

    @php($records = $this->rows())
    @php($labels = __('attendances::reports.columns'))

    @if ($records === [])
        <p style="font-size:.875rem;opacity:.75">{{ __('attendances::reports.empty') }}</p>
    @else
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:.8125rem">
                <thead>
                    <tr>
                        @foreach ($this->columns() as $column)
                            <th style="text-align:left;padding:.5rem;border-bottom:1px solid rgba(0,0,0,.1);white-space:nowrap">
                                {{ $labels[$column] ?? $column }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr wire:key="report-{{ $loop->index }}">
                            @foreach ($this->columns() as $column)
                                <td style="padding:.5rem;border-bottom:1px solid rgba(0,0,0,.05)">
                                    {{ $record[$column] ?? '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-filament-panels::page>
