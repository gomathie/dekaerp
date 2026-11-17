<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Support\Models\Company;

class JournalEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record?->state !== MoveState::CANCEL) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.reference'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        DatePicker::make('date')
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.accounting-date'))
                                            ->default(now())
                                            ->native(false)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                        Select::make('journal_id')
                                            ->relationship(
                                                'journal',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->where('type', JournalType::GENERAL)
                                                    ->where('company_id', $get('company_id') ?? current_company_id()),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.section.general.fields.journal'))
                                            ->createOptionForm(fn ($form) => JournalResource::form($form))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ]),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->schema([
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                JournalEntryResource::getLineRepeater(),
                            ]),

                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Select::make('company_id')
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        $company = Company::find($get('company_id'));

                                        if ($company?->currency_id) {
                                            $set('currency_id', $company->currency_id);
                                        }

                                        clear_foreign_company_values($set, $get, [
                                            'journal_id'         => Journal::class,
                                            'fiscal_position_id' => FiscalPosition::class,
                                        ], $get('company_id'));
                                    }),
                                Toggle::make('checked')
                                    ->inline(false)
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.checked')),
                                Select::make('fiscal_position_id')
                                    ->relationship(
                                        'fiscalPosition',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->preload()
                                    ->searchable()
                                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position'))
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.other-information.fields.fiscal-position-tooltip'))
                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                            ])
                            ->columns(2),
                        Tab::make(__('accounting::filament/clusters/accounting/resources/journal-entry.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make()
                    ->schema(JournalEntryResource::getCustomFormFields())
                    ->columns(2),
            ])
            ->columns(1);
    }
}
