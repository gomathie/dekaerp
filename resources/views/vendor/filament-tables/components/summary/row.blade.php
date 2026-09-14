{{--
    DEKA override of filament/tables resources/views/components/summary/row.blade.php
    (Filament v5.7.3). Re-diff against the vendor file when upgrading Filament.

    Why: the vendor row renders a cell for every column and folds leading
    non-summarised columns into the heading's colspan, but never applies a
    column's responsive visibility (`visibleFrom()` / `hiddenFrom()`). Header
    and body cells do get those classes, so on a narrow screen the body loses
    columns while the summary row keeps them, and totals land under the wrong
    columns. Changes from vendor, marked "DEKA" below:
      1. every summary cell carries its column's responsive classes;
      2. the heading colspan stops at the first responsive column, so that
         column and everything after it render as individual, hideable cells.
    On wide screens, where every column is visible, the output lines up exactly
    as before.
--}}
@props([
    'actions' => false,
    'actionsPosition' => null,
    'columns',
    'extraHeadingColumn' => false,
    'groupColumn' => null,
    'groupsOnly' => false,
    'heading',
    'placeholderColumns' => true,
    'query',
    'selectionEnabled' => false,
    'selectedState',
    'recordCheckboxPosition' => null,
])

@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Enums\RecordActionsPosition;
    use Filament\Tables\Enums\RecordCheckboxPosition;

    if ($groupsOnly && $groupColumn) {
        $columns = collect($columns)
            ->reject(fn (Column $column): bool => $column->getName() === $groupColumn)
            ->all();
    }

    // `$query` is constant for this render, so each column's resolved summarizers are
    // too. Resolve them once here instead of re-running `getSummarizers($query)` (and
    // `hasSummary($query)`, which wraps it) in every loop guard below. Keyed by the
    // `$columns` array key so the heading-span loop and the cell loop share the lookup.
    $columnsWithSummary = [];

    foreach ($columns as $summaryColumnKey => $summaryColumn) {
        $summaryColumnSummarizers = $summaryColumn->getSummarizers($query);

        $columnsWithSummary[$summaryColumnKey] = [
            'summarizers' => $summaryColumnSummarizers,
            'hasSummary' => (bool) count($summaryColumnSummarizers),
        ];
    }

    // DEKA: the same class names Column::getCellAttributeHtml() puts on body cells.
    $columnResponsiveClasses = fn (Column $column): array => [
        filled($hiddenFrom = $column->getHiddenFrom()) ? "{$hiddenFrom}:fi-hidden" : '',
        filled($visibleFrom = $column->getVisibleFrom()) ? "{$visibleFrom}:fi-visible" : '',
    ];
@endphp

<tr {{ $attributes->class(['fi-ta-row fi-ta-summary-row']) }}>
    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::BeforeCells)
        <td></td>
    @endif

    @if ($extraHeadingColumn || $groupsOnly)
        <th
            scope="row"
            class="fi-ta-cell fi-ta-summary-row-heading-cell fi-align-start"
        >
            {{ $heading }}
        </th>
    @else
        @php
            $headingColumnSpan = 1;

            foreach ($columns as $index => $column) {
                if ($index === array_key_first($columns)) {
                    continue;
                }

                if ($columnsWithSummary[$index]['hasSummary']) {
                    break;
                }

                // DEKA: a responsive column can't sit inside a colspan - it has to be its own cell to hide.
                if (filled($column->getHiddenFrom()) || filled($column->getVisibleFrom())) {
                    break;
                }

                $headingColumnSpan++;
            }
        @endphp
    @endif

    @foreach ($columns as $columnKey => $column)
        @if (($loop->first || $extraHeadingColumn || $groupsOnly || ($loop->iteration > $headingColumnSpan)) && ($placeholderColumns || $columnsWithSummary[$columnKey]['hasSummary']))
            @php
                $alignment = $column->getAlignment() ?? Alignment::Start;

                if (! $alignment instanceof Alignment) {
                    $alignment = filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
                }

                // The leading cell labels the whole summary row, so render it as a row header; the aggregate
                // value cells stay `<td>` and gain a row association from this `<th scope="row">`.
                $isSummaryRowHeadingCell = $loop->first && (! $extraHeadingColumn) && (! $groupsOnly);
                $summaryCellTag = $isSummaryRowHeadingCell ? 'th' : 'td';

                // DEKA: a heading cell spanning several columns stands for all of them, so it never hides.
                $summaryCellResponsiveClasses = ($isSummaryRowHeadingCell && ($headingColumnSpan > 1))
                    ? []
                    : $columnResponsiveClasses($column);
            @endphp

            <{{ $summaryCellTag }}
                @if ($isSummaryRowHeadingCell) scope="row" @endif
                @if ($isSummaryRowHeadingCell && ($headingColumnSpan > 1)) colspan="{{ $headingColumnSpan }}" @endif
                @class([
                    'fi-ta-cell',
                    ($alignment instanceof Alignment) ? "fi-align-{$alignment->value}" : (is_string($alignment) ? $alignment : ''),
                    'fi-ta-summary-row-heading-cell' => $isSummaryRowHeadingCell,
                    ...$summaryCellResponsiveClasses,
                ])
            >
                @if ($isSummaryRowHeadingCell)
                    {{ $heading }}
                @elseif ((! $placeholderColumns) || $columnsWithSummary[$columnKey]['hasSummary'])
                    @foreach ($columnsWithSummary[$columnKey]['summarizers'] as $summarizer)
                        {{ $summarizer->query($query)->selectedState($selectedState) }}
                    @endforeach
                @endif
            </{{ $summaryCellTag }}>
        @endif
    @endforeach

    @if ($placeholderColumns && $actions && in_array($actionsPosition, [RecordActionsPosition::AfterColumns, RecordActionsPosition::AfterCells]))
        <td></td>
    @endif

    @if ($placeholderColumns && $selectionEnabled && $recordCheckboxPosition === RecordCheckboxPosition::AfterCells)
        <td></td>
    @endif
</tr>
