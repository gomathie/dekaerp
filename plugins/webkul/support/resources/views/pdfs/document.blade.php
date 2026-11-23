@php
    /*
     * One layout for every printed business document (TRACE365, 2026-11-23,
     * plan #10 R-8). The callers build $document; this view only lays it out.
     *
     * Every rule is scoped under .deka-doc: the preview modal renders this
     * same HTML inside the application page, and the old templates' global
     * `body`, `.header` and `.summary` rules restyled the page around the
     * modal. dompdf reads the same rules.
     */
    $isRtl = \Webkul\Support\SupportServiceProvider::isRtl();
    $start = $isRtl ? 'right' : 'left';
    $end = $isRtl ? 'left' : 'right';
    $accent = $document['accent'] ?? '#1f2937';
    $labels = fn (string $key, array $replace = []) => __('support::documents.'.$key, $replace);
    $company = $document['company'];
    $party = $document['party'];
    // The rich-text editor saves an empty note as "<p></p>": that printed a
    // "Notes" heading with nothing under it.
    $hasNotes = trim(strip_tags((string) ($document['notes'] ?? ''))) !== '';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $document['title'] }} {{ $document['number'] }}</title>
    <style type="text/css">
        @page { margin: 36px 40px 56px 40px; }

        .deka-doc, .deka-doc table, .deka-doc td, .deka-doc th, .deka-doc div, .deka-doc span, .deka-doc p {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
        }
        .deka-doc { font-size: 10.5px; color: #1f2937; line-height: 1.45; }
        .deka-doc table { border-collapse: collapse; width: 100%; }
        .deka-doc td, .deka-doc th { vertical-align: top; padding: 0; }
        .deka-doc .muted { color: #6b7280; }
        .deka-doc .strong { font-weight: bold; }
        .deka-doc .end { text-align: {{ $end }}; }
        .deka-doc .start { text-align: {{ $start }}; }
        .deka-doc .nowrap { white-space: nowrap; }

        .deka-doc .head td { padding-bottom: 22px; }
        .deka-doc .logo { max-height: 64px; max-width: 210px; margin-bottom: 8px; }
        .deka-doc .company-name { font-size: 13px; font-weight: bold; color: #111827; }
        .deka-doc .title { font-size: 26px; letter-spacing: 1px; color: #111827; text-transform: uppercase; }
        .deka-doc .number { font-size: 11px; font-weight: bold; margin-top: 2px; }
        .deka-doc .headline-label { margin-top: 14px; font-size: 9.5px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
        .deka-doc .headline-value { font-size: 17px; font-weight: bold; color: #111827; }
        .deka-doc .status { display: inline-block; margin-top: 6px; padding: 2px 8px; border-radius: 9px; font-size: 9px; font-weight: bold; background: #eef2f7; color: #374151; }

        .deka-doc .parties td { padding-bottom: 18px; }
        .deka-doc .section-label { font-size: 9.5px; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .deka-doc .party-name { font-size: 11.5px; font-weight: bold; color: #111827; }
        .deka-doc .meta td { padding: 2px 0; }
        .deka-doc .meta .meta-label { color: #6b7280; padding-{{ $end }}: 14px; text-align: {{ $end }}; }
        .deka-doc .meta .meta-value { text-align: {{ $end }}; white-space: nowrap; }

        .deka-doc .lines th { background: {{ $accent }}; color: #ffffff; font-weight: normal; padding: 7px 8px; text-align: {{ $start }}; font-size: 10px; }
        .deka-doc .lines th.end { text-align: {{ $end }}; }
        .deka-doc .lines td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .deka-doc .lines td.end { text-align: {{ $end }}; white-space: nowrap; }
        .deka-doc .lines .item { font-weight: bold; color: #111827; }
        .deka-doc .lines .description { color: #6b7280; font-size: 9.5px; margin-top: 2px; }
        .deka-doc .lines tr { page-break-inside: avoid; }

        .deka-doc .totals { width: 46%; margin-top: 10px; margin-{{ $start }}: 54%; }
        .deka-doc .totals td { padding: 5px 8px; }
        .deka-doc .totals .total td { border-top: 1px solid #d1d5db; font-weight: bold; font-size: 11.5px; color: #111827; }
        .deka-doc .totals .balance td { background: #f3f4f6; font-weight: bold; font-size: 11.5px; color: #111827; padding-top: 8px; padding-bottom: 8px; }

        .deka-doc .notes { margin-top: 26px; page-break-inside: avoid; }
        .deka-doc .notes .block { margin-bottom: 14px; }
        .deka-doc .notes p { margin: 0 0 4px 0; }

        .deka-doc .doc-footer { position: fixed; bottom: -34px; {{ $start }}: 0; {{ $end }}: 0; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 8.5px; color: #9ca3af; }
        /* Page number only: dompdf has no total-pages counter, so "counter(pages)"
           printed "Page 1 / 0" (production, 2026-11-23). */
        .deka-doc .page-number:after { content: counter(page); }
    </style>
</head>

<body style="margin: 0;">
<div class="deka-doc" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <table class="head">
        <tr>
            <td style="width: 55%;" class="start">
                @if ($company['logo'])
                    <img class="logo" src="{{ $company['logo'] }}" alt="{{ $company['name'] }}"><br>
                @endif

                <div class="company-name">{{ $company['name'] }}</div>

                @foreach ($company['lines'] as $line)
                    <div>{{ $line }}</div>
                @endforeach

                @if ($company['tax_id'])
                    <div>{{ $labels('tax-id') }}: {{ $company['tax_id'] }}</div>
                @endif

                @foreach (array_filter([$company['phone'], $company['email'], $company['website']]) as $contact)
                    <div>{{ $contact }}</div>
                @endforeach
            </td>

            <td style="width: 45%;" class="end">
                <div class="title">{{ $document['title'] }}</div>
                <div class="number"># {{ $document['number'] }}</div>

                @if (! empty($document['status']))
                    <div class="status">{{ $document['status'] }}</div>
                @endif

                @if (! empty($document['headline']))
                    <div class="headline-label">{{ $document['headline']['label'] }}</div>
                    <div class="headline-value">{{ $document['headline']['value'] }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td style="width: 55%;" class="start">
                <div class="section-label">{{ $document['party_label'] }}</div>
                <div class="party-name">{{ $party['name'] }}</div>

                @foreach ($party['lines'] as $line)
                    <div>{{ $line }}</div>
                @endforeach

                @if ($party['tax_id'])
                    <div>{{ $labels('tax-id') }}: {{ $party['tax_id'] }}</div>
                @endif
            </td>

            <td style="width: 45%;">
                <table class="meta">
                    @foreach ($document['meta'] as [$label, $value])
                        <tr>
                            <td class="meta-label">{{ $label }}</td>
                            <td class="meta-value">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th>{{ $labels('columns.item') }}</th>
                <th class="end" style="width: 11%;">{{ $labels('columns.quantity') }}</th>
                <th class="end" style="width: 15%;">{{ $labels('columns.rate') }}</th>

                @if ($document['show_discount'])
                    <th class="end" style="width: 9%;">{{ $labels('columns.discount') }}</th>
                @endif

                <th class="end" style="width: 17%;">{{ $labels('columns.amount') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($document['lines'] as $index => $line)
                <tr>
                    <td class="muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="item">{{ $line['name'] }}</div>

                        @if (filled($line['description']))
                            <div class="description">{!! nl2br(e($line['description'])) !!}</div>
                        @endif
                    </td>
                    <td class="end">
                        {{ $line['quantity'] }}

                        @if (filled($line['unit']))
                            <div class="description">{{ $line['unit'] }}</div>
                        @endif
                    </td>
                    <td class="end">{{ $line['rate'] }}</td>

                    @if ($document['show_discount'])
                        <td class="end">{{ $line['discount'] }}</td>
                    @endif

                    <td class="end">{{ $line['amount'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $document['show_discount'] ? 6 : 5 }}" class="muted">{{ $labels('no-lines') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        @foreach ($document['totals'] as $row)
            <tr class="{{ $row['class'] ?? '' }}">
                <td class="end">{{ $row['label'] }}</td>
                <td class="end nowrap" style="width: 42%;">{{ $row['value'] }}</td>
            </tr>
        @endforeach
    </table>

    @if ($hasNotes || ! empty($document['payment']) || filled($document['terms'] ?? null) || ! empty($document['sections']))
        <div class="notes">
            @foreach ($document['sections'] ?? [] as $section)
                <div class="block">
                    <div class="section-label">{{ $section['label'] }}</div>

                    @foreach ($section['lines'] as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            @endforeach

            @if (! empty($document['payment']))
                <div class="block">
                    <div class="section-label">{{ $labels('payment-details') }}</div>

                    @foreach ($document['payment'] as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            @endif

            @if ($hasNotes)
                <div class="block">
                    <div class="section-label">{{ $labels('notes') }}</div>
                    {{-- Rich text from the document's own Notes field. Sanitized here,
                         at the point of output: the preview modal renders it in the page. --}}
                    <div>{!! str($document['notes'])->sanitizeHtml() !!}</div>
                </div>
            @endif

            @if (filled($document['terms'] ?? null))
                <div class="block">
                    <div class="section-label">{{ $labels('terms') }}</div>
                    <div>{!! nl2br(e($document['terms'])) !!}</div>
                </div>
            @endif
        </div>
    @endif

    <div class="doc-footer">
        <table>
            <tr>
                <td class="start">{{ $company['name'] }} · {{ $document['title'] }} {{ $document['number'] }}</td>
                <td class="end">{{ $labels('page') }} <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
