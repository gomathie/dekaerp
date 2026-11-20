<?php

namespace Webkul\Support\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\SequenceResource\Pages\ManageSequences;
use Webkul\Support\Filament\Resources\SequenceResource\Schemas\SequenceForm;
use Webkul\Support\Filament\Resources\SequenceResource\Tables\SequencesTable;
use Webkul\Support\Models\Sequence;

class SequenceResource extends Resource
{
    protected static ?string $model = Sequence::class;

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string
    {
        return __('support::filament/resources/sequence.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('support::filament/resources/sequence.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/sequence.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function form(Schema $schema): Schema
    {
        return SequenceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SequencesTable::configure($table);
    }

    public static function scopeLabel(Sequence $record): ?string
    {
        if (! $record->scope_type) {
            return null;
        }

        $label = $record->scope?->name ?? "#{$record->scope_id}";

        if ($record->variant) {
            $label .= ' — '.__("support::filament/resources/sequence.table.variants.{$record->variant}");
        }

        return $label;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSequences::route('/'),
        ];
    }
}
