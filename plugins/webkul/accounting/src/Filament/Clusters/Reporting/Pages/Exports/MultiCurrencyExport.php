<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * The Multi-currency report as a spreadsheet (plan #10 MC-3): the same
 * figures as the page, from the same MultiCurrencyReport::build() result.
 */
class MultiCurrencyExport implements FromArray, WithHeadings
{
    /**
     * @param  array<string, mixed>  $data  MultiCurrencyReport::build()
     */
    public function __construct(protected array $data) {}

    public function headings(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.multi-currency';
        $base = ['base' => $this->data['base']];

        return [
            [__($lang.'.navigation.title').' '.$this->data['date_from']->format('d M Y').' - '.$this->data['date_to']->format('d M Y')],
            [],
            array_map(fn (string $column): string => __($lang.'.columns.'.$column, $base), ['currency', 'invoiced', 'average-rate', 'invoiced-base', 'open', 'open-base', 'open-today', 'difference']),
        ];
    }

    public function array(): array
    {
        $lang = 'accounting::filament/clusters/reporting.pages.multi-currency';
        $base = ['base' => $this->data['base']];
        $rows = [];

        foreach ($this->data['currencies'] as $row) {
            $rows[] = [$row['code'], $row['invoiced'], $row['average_rate'], $row['invoiced_base'], $row['open'], $row['open_base'], $row['open_base_today'], $row['difference']];
        }

        $totals = $this->data['totals'];
        $rows[] = [__($lang.'.totals.total', $base), null, null, $totals['invoiced_base'], null, $totals['open_base'], $totals['open_base_today'], $totals['difference']];

        $rows[] = [];
        $rows[] = [__($lang.'.sections.exchange')];
        $rows[] = [__($lang.'.exchange.gains'), $this->data['exchange']['gains']];
        $rows[] = [__($lang.'.exchange.losses'), $this->data['exchange']['losses']];
        $rows[] = [__($lang.'.exchange.net'), $this->data['exchange']['net']];

        $rows[] = [];
        $rows[] = [__($lang.'.sections.documents')];
        $rows[] = array_map(fn (string $column): string => __($lang.'.columns.'.$column, $base), ['number', 'date', 'contact', 'currency', 'amount', 'rate', 'base-amount', 'status']);

        foreach ($this->data['documents'] as $document) {
            $rows[] = [$document['name'], $document['date']?->format('Y-m-d'), $document['partner'], $document['code'], $document['amount'], $document['rate'], $document['base'], $document['state']];
        }

        return $rows;
    }
}
