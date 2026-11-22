{{--
    Till receipt for a paid invoice (user, 2026-11-22, "New direct sale").
    Narrow on purpose: it prints on a receipt printer or one column of A4.
    Every value is escaped with {{ }}; names come from users.
--}}
@php
    $lang = 'accounts::filament/resources/invoice/pages/create-invoice.receipt';
    $currency = $record->currency?->name;
    $paid = (float) $record->amount_total - (float) $record->amount_residual;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body, table, td, th, div, span {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif !important;
        }

        body {
            margin: 0;
            color: #222222;
            font-size: 12px;
            line-height: 1.45;
        }

        .receipt {
            width: 300px;
            margin: 0 auto;
            padding: 12px 8px;
        }

        .center {
            text-align: center;
        }

        .end {
            text-align: {{ $isRtl ? 'left' : 'right' }};
        }

        .company {
            font-size: 16px;
            font-weight: bold;
        }

        .muted {
            color: #666666;
        }

        .rule {
            border-top: 1px dashed #999999;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 11px;
            color: #666666;
            font-weight: normal;
            text-align: {{ $isRtl ? 'right' : 'left' }};
            padding-bottom: 4px;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .total td {
            font-size: 14px;
            font-weight: bold;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <div class="company">{{ $record->company?->name }}</div>
            @if ($record->company?->phone)
                <div class="muted">{{ $record->company->phone }}</div>
            @endif
            @if ($record->company?->email)
                <div class="muted">{{ $record->company->email }}</div>
            @endif
            <div style="margin-top: 6px; font-weight: bold;">{{ __($lang.'.title') }}</div>
        </div>

        <div class="rule"></div>

        <table>
            <tr>
                <td>{{ __($lang.'.number') }}</td>
                <td class="end">{{ $record->name }}</td>
            </tr>
            <tr>
                <td>{{ __($lang.'.date') }}</td>
                <td class="end">{{ $record->invoice_date ? \Illuminate\Support\Carbon::parse($record->invoice_date)->format('Y-m-d') : '' }}</td>
            </tr>
            <tr>
                <td>{{ __($lang.'.customer') }}</td>
                <td class="end">{{ $record->partner?->name }}</td>
            </tr>
            @if ($record->invoiceUser)
                <tr>
                    <td>{{ __($lang.'.served-by') }}</td>
                    <td class="end">{{ $record->invoiceUser->name }}</td>
                </tr>
            @endif
        </table>

        <div class="rule"></div>

        <table>
            <thead>
                <tr>
                    <th>{{ __($lang.'.item') }}</th>
                    <th class="end">{{ __($lang.'.qty') }}</th>
                    <th class="end">{{ __($lang.'.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($record->invoiceLines as $line)
                    <tr>
                        <td>
                            {{ $line->product?->name ?? $line->name }}
                            <div class="muted">{{ money($line->price_unit, $currency) }}</div>
                        </td>
                        <td class="end">{{ rtrim(rtrim(number_format((float) $line->quantity, 2, '.', ''), '0'), '.') }}</td>
                        <td class="end">{{ money($line->price_subtotal, $currency) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="rule"></div>

        <table>
            <tr>
                <td>{{ __($lang.'.subtotal') }}</td>
                <td class="end">{{ money($record->amount_untaxed, $currency) }}</td>
            </tr>
            @if ((float) $record->amount_tax !== 0.0)
                <tr>
                    <td>{{ __($lang.'.tax') }}</td>
                    <td class="end">{{ money($record->amount_tax, $currency) }}</td>
                </tr>
            @endif
            <tr class="total">
                <td>{{ __($lang.'.total') }}</td>
                <td class="end">{{ money($record->amount_total, $currency) }}</td>
            </tr>
            <tr>
                <td>{{ __($lang.'.paid') }}</td>
                <td class="end">{{ money($paid, $currency) }}</td>
            </tr>
            @if ((float) $record->amount_residual > 0)
                <tr>
                    <td>{{ __($lang.'.balance') }}</td>
                    <td class="end">{{ money($record->amount_residual, $currency) }}</td>
                </tr>
            @endif
        </table>

        <div class="rule"></div>

        <div class="center muted">{{ __($lang.'.thank-you') }}</div>
    </div>
</body>
</html>
