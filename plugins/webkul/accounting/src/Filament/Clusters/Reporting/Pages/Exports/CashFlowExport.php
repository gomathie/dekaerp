<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Cash flow statement as a spreadsheet (accounting plan A6): the same rows
 * and totals as the page, one sheet.
 */
class CashFlowExport implements FromArray, WithHeadings
{
    /**
     * @param  array<string, mixed>  $data  CashFlow::cashFlowData()
     */
    public function __construct(protected array $data) {}

    public function headings(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.cash-flow';

        return [
            [__($lang.'.navigation.title').' '.$this->data['date_from']->format('d M Y').' - '.$this->data['date_to']->format('d M Y')],
            [],
            [__($lang.'.columns.account'), __($lang.'.columns.in'), __($lang.'.columns.out'), __($lang.'.columns.net')],
        ];
    }

    public function array(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.cash-flow';
        $rows = [[__($lang.'.totals.opening'), null, null, $this->data['opening']], []];

        foreach ($this->data['sections'] as $name => $section) {
            $rows[] = [__($lang.'.sections.'.$name)];

            foreach ($section['rows'] as $row) {
                $rows[] = [$row['account'], $row['in'], $row['out'], round($row['in'] - $row['out'], 2)];
            }

            $rows[] = [__($lang.'.totals.section'), $section['in'], $section['out'], $section['net']];
            $rows[] = [];
        }

        $rows[] = [__($lang.'.totals.net'), null, null, $this->data['net']];
        $rows[] = [__($lang.'.totals.closing'), null, null, $this->data['closing']];

        return $rows;
    }
}
