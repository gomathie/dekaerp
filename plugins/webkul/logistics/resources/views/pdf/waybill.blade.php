<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        * { font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; }
        body { margin: 0; color: #2f3640; font-size: 11px; line-height: 1.45; }
        h1 { margin: 0; color: #1a4587; font-size: 24px; }
        h2 { margin: 20px 0 8px; padding-bottom: 5px; border-bottom: 2px solid #1a4587; color: #1a4587; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px; text-align: {{ $isRtl ? 'right' : 'left' }}; vertical-align: top; }
        th { background: #1a4587; color: #ffffff; font-weight: bold; }
        .letterhead { margin-bottom: 22px; }
        .company { width: 58%; }
        .document { width: 42%; text-align: {{ $isRtl ? 'left' : 'right' }}; }
        .company-name { color: #1a4587; font-size: 22px; font-weight: bold; }
        .meta td, .parties td, .assignment td { border: 1px solid #d9dee5; }
        .label { color: #667085; font-size: 9px; text-transform: uppercase; }
        .value { margin-top: 2px; font-weight: bold; }
        .cargo td { border-bottom: 1px solid #e4e7ec; }
        .muted { color: #667085; }
        .notes { min-height: 34px; padding: 8px; border: 1px solid #d9dee5; }
        .signature td { width: 33.33%; height: 70px; border: 1px solid #98a2b3; vertical-align: bottom; }
    </style>
</head>
<body>
    @php
        $company = $record->company;
        $companyAddress = $company->partner;
        $trip = $record->trips->first();
        $empty = __('logistics::documents/waybill.empty');
    @endphp

    <table class="letterhead">
        <tr>
            <td class="company">
                <div class="company-name">{{ $company->name }}</div>
                @if ($companyAddress)
                    <div>{{ collect([$companyAddress->street1, $companyAddress->street2])->filter()->join(', ') }}</div>
                    <div>{{ collect([$companyAddress->city, $companyAddress->state?->name, $companyAddress->zip])->filter()->join(', ') }}</div>
                    <div>{{ $companyAddress->country?->name }}</div>
                @endif
                @if ($company->email)<div>{{ $company->email }}</div>@endif
                @if ($company->phone)<div>{{ $company->phone }}</div>@endif
            </td>
            <td class="document">
                <h1>{{ __('logistics::documents/waybill.title') }}</h1>
                <div><strong>{{ $record->waybill_no }}</strong></div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.shipment-number') }}</div><div class="value">{{ $record->name }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.customer') }}</div><div class="value">{{ $record->customer?->name ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.customer-reference') }}</div><div class="value">{{ $record->customer_reference ?: $empty }}</div></td>
        </tr>
    </table>

    <h2>{{ __('logistics::documents/waybill.labels.route') }}</h2>
    <table class="parties">
        <tr>
            <td width="50%">
                <div class="label">{{ __('logistics::documents/waybill.labels.sender') }}</div>
                <div class="value">{{ $record->pickupAddress?->name ?? $record->origin_label ?? $empty }}</div>
                @if ($record->pickupAddress)
                    <div>{{ collect([$record->pickupAddress->street1, $record->pickupAddress->street2])->filter()->join(', ') }}</div>
                    <div>{{ collect([$record->pickupAddress->city, $record->pickupAddress->state?->name, $record->pickupAddress->zip])->filter()->join(', ') }}</div>
                    <div>{{ $record->pickupAddress->country?->name }}</div>
                    <div>{{ $record->pickupAddress->phone }}</div>
                @endif
            </td>
            <td width="50%">
                <div class="label">{{ __('logistics::documents/waybill.labels.recipient') }}</div>
                <div class="value">{{ $record->deliveryAddress?->name ?? $record->destination_label ?? $empty }}</div>
                @if ($record->deliveryAddress)
                    <div>{{ collect([$record->deliveryAddress->street1, $record->deliveryAddress->street2])->filter()->join(', ') }}</div>
                    <div>{{ collect([$record->deliveryAddress->city, $record->deliveryAddress->state?->name, $record->deliveryAddress->zip])->filter()->join(', ') }}</div>
                    <div>{{ $record->deliveryAddress->country?->name }}</div>
                    <div>{{ $record->deliveryAddress->phone }}</div>
                @endif
            </td>
        </tr>
        <tr>
            <td><span class="label">{{ __('logistics::documents/waybill.labels.origin') }}:</span> {{ $record->origin_label ?: $empty }}</td>
            <td><span class="label">{{ __('logistics::documents/waybill.labels.destination') }}:</span> {{ $record->destination_label ?: $empty }}</td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.planned-pickup') }}</div><div>{{ $record->planned_pickup_at?->format('Y-m-d H:i') ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.actual-pickup') }}</div><div>{{ $record->actual_pickup_at?->format('Y-m-d H:i') ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.expected-delivery') }}</div><div>{{ $record->expected_delivery_at?->format('Y-m-d H:i') ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.actual-delivery') }}</div><div>{{ $record->actual_delivery_at?->format('Y-m-d H:i') ?? $empty }}</div></td>
        </tr>
    </table>

    <h2>{{ __('logistics::documents/waybill.labels.cargo') }}</h2>
    <table class="cargo">
        <thead>
            <tr>
                <th>{{ __('logistics::documents/waybill.labels.description') }}</th>
                <th>{{ __('logistics::documents/waybill.labels.package-type') }}</th>
                <th>{{ __('logistics::documents/waybill.labels.quantity') }}</th>
                <th>{{ __('logistics::documents/waybill.labels.weight') }}</th>
                <th>{{ __('logistics::documents/waybill.labels.volume') }}</th>
                <th>{{ __('logistics::documents/waybill.labels.handling') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($record->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->packageType?->name ?? $empty }}</td>
                    <td>{{ number_format((float) $line->quantity, 3) }}</td>
                    <td>{{ number_format((float) $line->weight_kg, 3) }}</td>
                    <td>{{ number_format((float) $line->volume_m3, 3) }}</td>
                    <td>{{ $line->handling_instructions ?: $empty }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">{{ $empty }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>{{ __('logistics::documents/waybill.labels.driver') }} / {{ __('logistics::documents/waybill.labels.vehicle') }}</h2>
    <table class="assignment">
        <tr>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.driver') }}</div><div class="value">{{ $trip?->driver?->name ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.vehicle') }}</div><div class="value">{{ $trip?->vehicle?->registration_no ?? $empty }}</div></td>
            <td><div class="label">{{ __('logistics::documents/waybill.labels.carrier') }}</div><div class="value">{{ $record->carrier?->name ?? $empty }}</div></td>
        </tr>
    </table>

    <h2>{{ __('logistics::documents/waybill.labels.special-instructions') }}</h2>
    <div class="notes">{{ $record->instructions ?: $empty }}</div>

    <h2>{{ __('logistics::documents/waybill.labels.signature') }}</h2>
    <table class="signature">
        <tr>
            <td>{{ __('logistics::documents/waybill.labels.received-by') }}</td>
            <td>{{ __('logistics::documents/waybill.labels.signature') }}</td>
            <td>{{ __('logistics::documents/waybill.labels.date') }}</td>
        </tr>
    </table>
</body>
</html>
