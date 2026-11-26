@php
    /*
     * One layout for every report PDF (plan #9 R-2, 2026-11-25). The caller
     * builds $report through Webkul\Support\Support\ReportPdf; this view only
     * lays it out. Every value is printed escaped ({{ }}): report cells carry
     * customer names, references and notes typed by users.
     *
     * Each table's <thead> repeats at the top of every page it runs onto.
     */
    $isRtl = \Webkul\Support\SupportServiceProvider::isRtl();
    $start = $isRtl ? 'right' : 'left';
    $end = $isRtl ? 'left' : 'right';
    $accent = $report['accent'] ?? '#1f2937';
    $company = $report['company'];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $report['title'] }}</title>
    <style type="text/css">
        @page { size: A4 landscape; margin: 30px 34px 48px 34px; }

        .deka-report, .deka-report table, .deka-report td, .deka-report th, .deka-report div, .deka-report span, .deka-report p {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
        }
        .deka-report { font-size: 9px; color: #1f2937; line-height: 1.4; }
        .deka-report table { border-collapse: collapse; width: 100%; }
        .deka-report td, .deka-report th { vertical-align: top; padding: 0; }
        .deka-report .muted { color: #6b7280; }
        .deka-report .end { text-align: {{ $end }}; }
        .deka-report .start { text-align: {{ $start }}; }

        .deka-report .head td { padding-bottom: 12px; }
        .deka-report .logo { max-height: 48px; max-width: 180px; margin-bottom: 4px; }
        .deka-report .company-name { font-size: 12px; font-weight: bold; color: #111827; }
        .deka-report .title { font-size: 18px; font-weight: bold; color: #111827; }
        .deka-report .subtitle { margin-top: 2px; color: #6b7280; }
        .deka-report .meta { border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; padding: 6px 0; margin-bottom: 12px; }
        .deka-report .meta div { margin: 1px 0; }

        .deka-report .heading { font-size: 11px; font-weight: bold; color: #111827; margin: 12px 0 6px 0; }
        .deka-report .rows thead { display: table-header-group; }
        .deka-report .rows th { background: {{ $accent }}; color: #ffffff; font-weight: normal; padding: 5px 6px; text-align: {{ $start }}; font-size: 8.5px; }
        .deka-report .rows th.end { text-align: {{ $end }}; }
        .deka-report .rows td { padding: 4px 6px; border-bottom: 1px solid #e5e7eb; }
        .deka-report .rows td.end { text-align: {{ $end }}; white-space: nowrap; }
        .deka-report .rows tr { page-break-inside: avoid; }
        .deka-report .rows .group td { background: #f3f4f6; font-weight: bold; color: #111827; }
        .deka-report .rows .subtotal td { font-weight: bold; color: #374151; border-bottom: 1px solid #d1d5db; }
        .deka-report .rows .total td { font-weight: bold; font-size: 10px; color: #111827; border-top: 2px solid #111827; border-bottom: 2px solid #111827; background: #f9fafb; }
        .deka-report .note { margin-top: 6px; color: #6b7280; }

        .deka-report .report-footer { position: fixed; bottom: -30px; {{ $start }}: 0; {{ $end }}: 0; border-top: 1px solid #e5e7eb; padding-top: 5px; font-size: 8px; color: #9ca3af; }
        /* Page number only: dompdf has no total-pages counter (see support::pdfs.document). */
        .deka-report .page-number:after { content: counter(page); }
    </style>
</head>

<body style="margin: 0;">
<div class="deka-report" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <table class="head">
        <tr>
            <td style="width: 50%;" class="start">
                @if ($company['logo'])
                    <img class="logo" src="{{ $company['logo'] }}" alt="{{ $company['name'] }}"><br>
                @endif

                <div class="company-name">{{ $company['name'] }}</div>

                @foreach ($company['lines'] as $line)
                    <div class="muted">{{ $line }}</div>
                @endforeach
            </td>

            <td style="width: 50%;" class="end">
                <div class="title">{{ $report['title'] }}</div>

                @if (filled($report['subtitle'] ?? null))
                    <div class="subtitle">{{ $report['subtitle'] }}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="meta">
        <div>{{ $report['filters'] }}</div>
        <div class="muted">{{ $report['printed'] }}</div>
    </div>

    @foreach ($report['tables'] as $table)
        @php
            $columnCount = max(count($table['columns']), 1);
            $total = $table['total'] ?? null;
            $isEnd = fn (int $index): bool => (bool) ($table['columns'][$index]['end'] ?? false);
        @endphp

        @if (filled($table['heading'] ?? null))
            <div class="heading">{{ $table['heading'] }}</div>
        @endif

        <table class="rows">
            <thead>
                <tr>
                    @foreach ($table['columns'] as $column)
                        <th class="{{ $column['end'] ? 'end' : '' }}">{{ $column['label'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- An empty result still prints its headings and this line, so the
                     reader sees the filters matched nothing. --}}
                @if ($table['rows'] === [])
                    <tr><td colspan="{{ $columnCount }}" class="muted">{{ $table['empty'] ?? __('support::report-pdf.empty') }}</td></tr>
                @endif

                @foreach ($table['rows'] as $row)
                    @if ($row['kind'] === 'group')
                        <tr class="group"><td colspan="{{ $columnCount }}">{{ $row['label'] }}</td></tr>
                    @else
                        <tr class="{{ $row['kind'] === 'subtotal' ? 'subtotal' : '' }}">
                            @foreach ($row['cells'] as $index => $cell)
                                <td class="{{ $isEnd($index) ? 'end' : '' }}">{{ $index === 0 && $row['kind'] === 'subtotal' && blank($cell) ? $row['label'] : $cell }}</td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach

                @if ($total)
                    <tr class="total">
                        @foreach ($total['cells'] as $index => $cell)
                            <td class="{{ $isEnd($index) ? 'end' : '' }}">{{ $index === 0 && blank($cell) ? $total['label'] : $cell }}</td>
                        @endforeach
                    </tr>
                @endif
            </tbody>
        </table>

        @if (filled($table['note'] ?? null))
            <div class="note">{{ $table['note'] }}</div>
        @endif
    @endforeach

    <div class="report-footer">
        <table>
            <tr>
                <td class="start">{{ $company['name'] }} · {{ $report['title'] }}</td>
                <td class="end">{{ __('support::report-pdf.page') }} <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
