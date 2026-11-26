<?php

use Livewire\Features\SupportTesting\Testable;
use Webkul\Support\Filament\Actions\ExportTablePdfAction;
use Webkul\Support\Support\ReportPdf;

/**
 * Report PDFs (plan #9 R-2) in tests: the HTML the PDF is drawn from, so a
 * test can read which rows, filters and totals it carries without parsing
 * the PDF itself.
 */
class ReportPdfHelper
{
    /**
     * The HTML "Export to PDF" would print for a table report, in the state
     * the test left it (filters, search, sort).
     */
    public static function tableHtml(Testable $component): string
    {
        return ReportPdf::html(ExportTablePdfAction::report($component->instance()));
    }

    /**
     * Whether the last call on the component handed the browser a file.
     */
    public static function downloaded(Testable $component): bool
    {
        return data_get($component->effects, 'download') !== null;
    }
}
