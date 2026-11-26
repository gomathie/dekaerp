<?php

namespace Webkul\Support\Support;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\PDFHandler;

/**
 * A report printed as it is on screen (plan #9 R-2, mockup approved by the
 * user 2026-11-25): company header with logo, the report title, the filters
 * in words, who printed it and when, then one or more tables, A4 landscape,
 * page numbers. Every report PDF goes through this one layout
 * (`support::pdfs.report`), so a header fix is made once.
 *
 * The report array this class builds and renders:
 *
 * @phpstan-type ReportTable array{heading?: ?string, columns: list<array{label: string, end: bool}>, rows: list<array{kind: string, label?: string, cells?: list<?string>}>, total?: ?array{label: string, cells: list<?string>}, empty?: ?string, note?: ?string}
 */
class ReportPdf
{
    use PDFHandler;

    /**
     * Above this many rows a PDF is refused and the user is asked to narrow
     * the filters. dompdf lays the whole document out in one PHP process, its
     * time and memory grow with every row, and a web request that runs out
     * of either fails with nothing to show for it. Excel (a queued export)
     * has no such limit, so it stays the way to take everything.
     *
     * A property rather than a constant only so a test can lower it instead
     * of creating thousands of rows.
     */
    public static int $maxRows = 2000;

    /**
     * The header and frame every report PDF shares.
     *
     * @param  list<string>  $filters  the applied filters, already in words
     * @param  list<ReportTable>  $tables
     * @return array<string, mixed>
     */
    public static function make(string $title, array $filters, array $tables, ?string $subtitle = null): array
    {
        // The company the user is working in: reports are read in that
        // company's context, and its own folder holds its logo
        // (PrintedDocument::logo reads it scope-free by company id).
        $company = ($companyId = current_company_id()) ? Company::query()->withoutGlobalScopes()->find($companyId) : null;

        $filters = array_values(array_filter(array_map(fn ($filter): string => trim((string) $filter), $filters), 'filled'));

        return [
            'title'    => $title,
            'subtitle' => filled($subtitle) ? (string) $subtitle : null,
            'filters'  => $filters === []
                ? __('support::report-pdf.no-filters')
                : __('support::report-pdf.filters', ['filters' => implode(' · ', $filters)]),
            'printed'  => __('support::report-pdf.printed', [
                'date' => now()->translatedFormat('j M Y, H:i'),
                'user' => (string) (auth()->user()?->name ?? ''),
            ]),
            'company'  => PrintedDocument::company($company),
            'accent'   => PrintedDocument::accent($company),
            'tables'   => $tables,
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public static function html(array $report): string
    {
        return view('support::pdfs.report', ['report' => $report])->render();
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public static function download(array $report): StreamedResponse
    {
        $name = Str::slug($report['title']) ?: 'report';

        return (new static)->downloadPDF(static::html($report), $name.'-'.now()->format('Y-m-d'), 'landscape');
    }
}
