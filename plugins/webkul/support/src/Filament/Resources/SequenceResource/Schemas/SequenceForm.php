<?php

namespace Webkul\Support\Filament\Resources\SequenceResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Unique;
use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Filament\Resources\SequenceResource;
use Webkul\Support\Models\Sequence;

class SequenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('support::filament/resources/sequence.form.sections.general.title'))
                    ->description(fn (?Sequence $record): ?string => $record === null
                        ? __('support::filament/resources/sequence.form.sections.general.description')
                        : null)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.name'))
                            ->maxLength(255),
                        TextEntry::make('applies_to')
                            ->label(__('support::filament/resources/sequence.table.columns.applies-to'))
                            ->state(fn (Sequence $record): string => (string) SequenceResource::scopeLabel($record))
                            ->visible(fn (?Sequence $record): bool => $record !== null && filled($record->scope_type)),
                        TextInput::make('code')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.code'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.general.fields.code-help'))
                            ->required(fn (?Sequence $record): bool => $record === null)
                            ->maxLength(255)
                            ->visible(fn (?Sequence $record): bool => $record === null || filled($record->code))
                            ->disabled(fn (?Sequence $record): bool => $record !== null)
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, Get $get): Unique {
                                    $companyId = $get('company_id');

                                    return $companyId
                                        ? $rule->where('company_id', $companyId)
                                        : $rule->whereNull('company_id');
                                },
                            ),
                        Select::make('company_id')
                            ->label(__('support::filament/resources/sequence.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            // UF-029: only installation staff may choose (or
                            // leave empty for a shared sequence); a Company
                            // Admin's is their current company. ManageSequences
                            // enforces the same through SequencePolicy::createIn.
                            ->default(fn (): ?int => current_company_id())
                            ->disabled(fn (?Sequence $record): bool => $record !== null
                                || ! Gate::allows('createIn', [Sequence::class, null])),
                    ])
                    ->columns(2),
                Section::make(__('support::filament/resources/sequence.form.sections.format.title'))
                    ->schema([
                        TextInput::make('prefix')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.prefix'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.format.fields.prefix-help'))
                            ->maxLength(255),
                        TextInput::make('suffix')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.suffix'))
                            ->maxLength(255),
                        TextInput::make('padding')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.padding'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(12)
                            ->default(5)
                            ->required(),
                        TextInput::make('next_number')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.next-number'))
                            ->helperText(__('support::filament/resources/sequence.form.sections.format.fields.next-number-help'))
                            ->integer()
                            ->minValue(fn (?Sequence $record): int => max(1, $record?->next_number ?? 1))
                            ->default(1)
                            ->required(),
                        TextInput::make('step')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.step'))
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                        Select::make('reset_frequency')
                            ->label(__('support::filament/resources/sequence.form.sections.format.fields.reset-frequency'))
                            ->options(SequenceResetFrequency::options())
                            ->default(SequenceResetFrequency::NEVER->value)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
