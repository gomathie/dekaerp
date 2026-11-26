<?php

namespace Webkul\Support\Filament\Actions;

use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Contracts\HasLabel;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Webkul\Support\Support\ReportPdf;

/**
 * "Export to PDF" for a report that is a Filament table (plan #9 R-2,
 * mockup approved by the user 2026-11-25): the PDF holds exactly what the
 * screen shows for the current filters, search, sort and grouping, every
 * page of results rather than the visible one, with the visible columns,
 * group subtotals and the totals row.
 *
 * One action for every table report (Sales, Purchases, Logistics), so the
 * rule "print what the screen shows" lives in one place. It reads the page's
 * own table:
 *
 *  - rows from getFilteredSortedTableQuery(), the query the screen pages
 *    through (filters, search, grouping order, sort). Never getTable()
 *    ->getQuery(): that is the unfiltered base query, and a PDF built from it
 *    would hand someone rows the screen never showed them;
 *  - columns from getVisibleColumns(), so a column the page hides from this
 *    user (a money column behind a permission) or that the user toggled off
 *    is not printed either;
 *  - totals from the columns' own summarizers, over the same filtered query.
 *
 * Company scoping comes with the query: the models' CompanyScope applies and
 * nothing here removes it.
 */
class ExportTablePdfAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export-pdf';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('support::report-pdf.export-pdf'))
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            // The page's own access rule, checked again when the action runs:
            // a Livewire request can name an action directly. Anything that is
            // not a page has no rule to borrow, so it is refused (fail closed).
            ->authorize(fn (HasTable $livewire): bool => $livewire instanceof Page && $livewire::canAccess())
            ->action(fn (HasTable $livewire): ?StreamedResponse => static::export($livewire));
    }

    public static function export(HasTable $livewire): ?StreamedResponse
    {
        $count = $livewire->getFilteredTableQuery()->count();

        // Refused, with the way out, rather than a request that times out
        // half-way through laying out thousands of rows (ReportPdf::$maxRows).
        if ($count > ReportPdf::$maxRows) {
            Notification::make()
                ->warning()
                ->title(__('support::report-pdf.too-many.title'))
                ->body(__('support::report-pdf.too-many.body', [
                    'count' => number_format($count),
                    'max'   => number_format(ReportPdf::$maxRows),
                ]))
                ->send();

            return null;
        }

        return ReportPdf::download(static::report($livewire));
    }

    /**
     * The report as the PDF prints it: header, filters in words, and one
     * table of what the screen shows.
     *
     * @return array<string, mixed>
     */
    public static function report(HasTable $livewire): array
    {
        $table = $livewire->getTable();
        $columns = array_values($table->getVisibleColumns());
        $group = $livewire->getTableGrouping();
        $summaryQuery = $livewire->getAllTableSummaryQuery();
        $hasSummary = $summaryQuery && collect($columns)->contains(fn (Column $column): bool => $column->getSummarizers($summaryQuery) !== []);

        $records = $livewire->getFilteredSortedTableQuery()->limit(ReportPdf::$maxRows)->get();

        $rows = [];
        $previous = null;

        foreach ($records as $record) {
            if ($group && ($previous === null || $group->getStringKey($record) !== $group->getStringKey($previous))) {
                if ($previous !== null && $hasSummary) {
                    $rows[] = static::groupTotal($columns, $group, $summaryQuery, $previous);
                }

                $rows[] = ['kind' => 'group', 'label' => static::text($group->getTitle($record)) ?: '—'];
            }

            $rows[] = ['kind' => 'row', 'cells' => array_map(fn (Column $column): string => static::cell($column, $record), $columns)];
            $previous = $record;
        }

        if ($group && $previous !== null && $hasSummary) {
            $rows[] = static::groupTotal($columns, $group, $summaryQuery, $previous);
        }

        $title = $livewire instanceof Page ? static::text($livewire->getTitle()) : '';
        $subtitle = $livewire instanceof Page ? static::text($livewire->getSubheading()) : null;

        return ReportPdf::make($title, static::filters($livewire), [[
            'columns' => array_map(fn (Column $column): array => [
                'label' => static::text($column->getLabel()),
                'end'   => static::alignsEnd($column),
            ], $columns),
            'rows'  => $rows,
            'total' => $hasSummary ? [
                'label' => __('support::report-pdf.total'),
                'cells' => static::summaryCells($columns, $summaryQuery),
            ] : null,
        ]], $subtitle);
    }

    /**
     * The applied filters and search in words: Filament's own indicators (the
     * chips the screen shows), plus a plain description of any filter that is
     * set but shows no chip, such as a from/until date pair. Without that the
     * PDF of a filtered period would read as if it covered everything.
     *
     * @return list<string>
     */
    public static function filters(HasTable $livewire): array
    {
        $table = $livewire->getTable();
        $words = [];

        if ($livewire->hasTableSearch()) {
            $words[] = static::text($livewire->getTableSearchIndicator()->getLabel());
        }

        foreach ($table->getFilters() as $filter) {
            $indicators = $filter->getIndicators();

            if ($indicators !== []) {
                foreach ($indicators as $indicator) {
                    $words[] = static::text($indicator instanceof Indicator ? $indicator->getLabel() : $indicator);
                }

                continue;
            }

            if ($described = static::describeFilter($filter)) {
                $words[] = $described;
            }
        }

        return array_values(array_filter($words, 'filled'));
    }

    protected static function describeFilter(BaseFilter $filter): ?string
    {
        $state = $filter->getState();
        $parts = [];

        foreach ($filter->getSchemaComponents() as $component) {
            if (! $component instanceof Field) {
                continue;
            }

            $value = $state[$component->getName()] ?? null;

            if (blank($value) || $value === false) {
                continue;
            }

            $parts[] = static::text($component->getLabel()).' '.match (true) {
                $component instanceof DatePicker => static::date($value),
                $value === true                  => __('support::report-pdf.yes'),
                is_array($value)                 => implode(', ', array_map('strval', $value)),
                default                          => (string) $value,
            };
        }

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * @param  list<Column>  $columns
     * @return array{kind: string, label: string, cells: list<?string>}
     */
    protected static function groupTotal(array $columns, Group $group, Builder $summaryQuery, Model $record): array
    {
        return [
            'kind'  => 'subtotal',
            'label' => __('support::report-pdf.group-total', ['group' => static::text($group->getTitle($record)) ?: '—']),
            'cells' => static::summaryCells($columns, $group->scopeQuery($summaryQuery->clone(), $record)),
        ];
    }

    /**
     * Each column's summary over the given query, formatted as the column's
     * summarizer formats it on screen; null where a column has none.
     *
     * @param  list<Column>  $columns
     * @return list<?string>
     */
    protected static function summaryCells(array $columns, Builder $query): array
    {
        return array_map(function (Column $column) use ($query): ?string {
            $summarizers = $column->getSummarizers($query);

            if ($summarizers === []) {
                return null;
            }

            $values = [];

            foreach ($summarizers as $summarizer) {
                $summarizer->query($query->clone());
                $value = static::text($summarizer->formatState($summarizer->getState()));

                // A label only where a column carries several summaries; one
                // sum under its own column needs none.
                $values[] = count($summarizers) > 1 && filled($label = static::text($summarizer->getLabel())) ? $label.': '.$value : $value;
            }

            return implode(' / ', $values);
        }, $columns);
    }

    /**
     * The cell as the column formats it on screen (dates, numbers, enum
     * labels, placeholders), reduced to plain text: the PDF view escapes it.
     */
    protected static function cell(Column $column, Model $record): string
    {
        $column->record($record);

        $state = $column->getState();
        $format = fn (mixed $value): string => static::text(method_exists($column, 'formatState') ? $column->formatState($value) : $value);

        $text = match (true) {
            $state instanceof Collection => $state->map($format)->filter('filled')->implode(', '),
            is_array($state)             => collect($state)->map($format)->filter('filled')->implode(', '),
            blank($state)                => '',
            default                      => $format($state),
        };

        return filled($text) ? $text : static::text($column->getPlaceholder());
    }

    protected static function alignsEnd(Column $column): bool
    {
        return (method_exists($column, 'isNumeric') && $column->isNumeric())
            || (method_exists($column, 'isMoney') && $column->isMoney());
    }

    protected static function date(mixed $value): string
    {
        try {
            return $value instanceof CarbonInterface ? $value->translatedFormat('j M Y') : Carbon::parse((string) $value)->translatedFormat('j M Y');
        } catch (Throwable) {
            return (string) $value;
        }
    }

    /**
     * Plain text from whatever Filament hands back: an enum label, an
     * Htmlable or a string. Tags are stripped and entities decoded so the
     * view's own escaping is the only escaping.
     */
    protected static function text(mixed $value): string
    {
        if ($value instanceof HasLabel) {
            $value = $value->getLabel();
        } elseif ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        if ($value instanceof Htmlable) {
            $value = html_entity_decode(strip_tags($value->toHtml()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return is_scalar($value) || $value instanceof \Stringable ? trim((string) $value) : '';
    }
}
