<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Tax Summary as a spreadsheet (accounting plan A7): the same rows and
 * totals as the page, one sheet.
 */
class TaxSummaryExport implements FromArray, WithHeadings
{
    /**
     * @param  array<string, mixed>  $data  TaxSummary::taxSummaryData()
     */
    public function __construct(protected array $data) {}

    public function headings(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.tax-summary';

        return [
            [__($lang.'.navigation.title').' '.$this->data['date_from']->format('d M Y').' - '.$this->data['date_to']->format('d M Y')],
            [],
            [__($lang.'.columns.tax'), __($lang.'.columns.rate'), __($lang.'.columns.base'), __($lang.'.columns.amount')],
        ];
    }

    public function array(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.tax-summary';
        $totals = $this->data['totals'];
        $rows = [[__($lang.'.sections.sales')]];

        foreach ($this->data['sales'] as $row) {
            $rows[] = [$row['name'], $row['rate'], $row['base'], $row['tax']];
        }

        $rows[] = [__($lang.'.totals.collected'), null, $totals['sales_base'], $totals['collected']];
        $rows[] = [];
        $rows[] = [__($lang.'.sections.purchases')];

        foreach ($this->data['purchases'] as $row) {
            $rows[] = [$row['name'], $row['rate'], $row['base'], $row['tax']];
        }

        $rows[] = [__($lang.'.totals.paid'), null, $totals['purchases_base'], $totals['paid']];
        $rows[] = [];
        $rows[] = [__($lang.'.totals.net'), null, null, $totals['net']];

        return $rows;
    }
}
