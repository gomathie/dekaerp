<?php

namespace Webkul\Support\Filament\Forms\Components\Repeater;

use Filament\Schemas\Components\Concerns\HasName;
use Filament\Schemas\Components\Group;

/**
 * Several fields in one cell of a table repeater. The table view places each
 * item component under the column of the same name, and a plain Group has
 * no name, so it could not take a cell. Given its column's name (->name()),
 * this one stacks its fields in that cell (plan 10, piece 7: the item box
 * with the line's description under it).
 */
class TableCellGroup extends Group
{
    use HasName;
}
