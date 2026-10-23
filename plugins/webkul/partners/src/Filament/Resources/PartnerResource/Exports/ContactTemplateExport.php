<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Webkul\Partner\Services\ContactImporter;

/**
 * The example file offered alongside the contact import.
 *
 * Headings are the importer's own column keys rather than translated labels.
 * The file is filled in and uploaded back, so the heading row is machine input
 * on the return trip - translating it would make a template downloaded in one
 * language fail to import. The human explanation lives in the import modal,
 * where it is translated.
 */
class ContactTemplateExport implements FromArray, WithColumnWidths, WithCustomCsvSettings, WithHeadings, WithStyles
{
    /**
     * Used only when this template is written as CSV; the Excel writer ignores
     * it. The byte-order mark is what makes Excel open the file as UTF-8, so
     * accented names survive the round trip instead of arriving as mojibake.
     *
     * @return array<string, mixed>
     */
    public function getCsvSettings(): array
    {
        return [
            'use_bom'   => true,
            'delimiter' => ',',
        ];
    }

    public function headings(): array
    {
        return array_keys(ContactImporter::columns());
    }

    public function array(): array
    {
        $columns = ContactImporter::columns();

        $rows = [];

        foreach ([0, 1] as $example) {
            $rows[] = array_map(
                fn (array $column): string => $column['example'][$example] ?? '',
                array_values($columns),
            );
        }

        return $rows;
    }

    /**
     * @return array<string, array<string, int>>
     */
    public function columnWidths(): array
    {
        $widths = [];

        foreach (array_keys(ContactImporter::columns()) as $index => $column) {
            $widths[$this->columnLetter($index)] = max(14, strlen($column) + 4);
        }

        return $widths;
    }

    public function styles(Worksheet $sheet): array
    {
        $required = ContactImporter::requiredColumns();

        $styles = [
            1 => ['font' => ['bold' => true]],
        ];

        // A required column is marked on the heading itself, so the cue
        // survives in the downloaded file rather than only in the modal.
        foreach (array_keys(ContactImporter::columns()) as $index => $column) {
            if (! in_array($column, $required, true)) {
                continue;
            }

            $styles[$this->columnLetter($index).'1'] = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'B91C1C']],
            ];
        }

        return $styles;
    }

    /**
     * PhpSpreadsheet's own converter, rather than arithmetic on 'A', so the
     * 27th column becomes AA instead of a punctuation mark.
     */
    protected function columnLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index + 1);
    }
}
