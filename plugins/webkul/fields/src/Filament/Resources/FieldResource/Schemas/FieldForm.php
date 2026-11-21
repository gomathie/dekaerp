<?php

namespace Webkul\Field\Filament\Resources\FieldResource\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Facades\Schema;
use Webkul\Field\Filament\Resources\FieldResource;

class FieldForm
{
    public static function configure(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('fields::filament/resources/field.form.sections.general.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('code')
                                    ->required()
                                    ->label(__('fields::filament/resources/field.form.sections.general.fields.code'))
                                    ->maxLength(255)
                                    ->disabledOn('edit')
                                    ->helperText(__('fields::filament/resources/field.form.sections.general.fields.code-helper-text'))
                                    ->unique(ignoreRecord: true)
                                    ->notIn(function (Get $get) {
                                        if ($get('id') || ! $get('customizable_type')) {
                                            return [];
                                        }

                                        $table = app($get('customizable_type'))->getTable();

                                        return Schema::getColumnListing($table);
                                    })
                                    ->rules([
                                        'regex:/^[a-zA-Z_][a-zA-Z0-9_]*$/',
                                    ]),
                            ])
                            ->columns(2),

                        Section::make(__('fields::filament/resources/field.form.sections.options.title'))
                            ->visible(fn (Get $get): bool => in_array($get('type'), [
                                'select',
                                'checkbox_list',
                                'radio',
                            ]))
                            ->schema([
                                Repeater::make('options')
                                    ->hiddenLabel()
                                    ->simple(
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    )
                                    ->addActionLabel(__('fields::filament/resources/field.form.sections.options.fields.add-option')),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.form-settings.title'))
                            ->schema([
                                Group::make()
                                    ->schema(FieldResource::getFormSettingsSchema())
                                    ->statePath('form_settings'),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.table-settings.title'))
                            ->schema(FieldResource::getTableSettingsSchema()),

                        Section::make(__('fields::filament/resources/field.form.sections.infolist-settings.title'))
                            ->schema(FieldResource::getInfolistSettingsSchema()),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('fields::filament/resources/field.form.sections.settings.title'))
                            ->schema([
                                Select::make('type')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.type'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->searchable()
                                    ->native(false)
                                    ->live()
                                    ->options([
                                        'text'          => __('fields::filament/resources/field.form.sections.settings.fields.type-options.text'),
                                        'textarea'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.textarea'),
                                        'select'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.select'),
                                        'checkbox'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.checkbox'),
                                        'radio'         => __('fields::filament/resources/field.form.sections.settings.fields.type-options.radio'),
                                        'toggle'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.toggle'),
                                        'checkbox_list' => __('fields::filament/resources/field.form.sections.settings.fields.type-options.checkbox-list'),
                                        'datetime'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.datetime'),
                                        'editor'        => __('fields::filament/resources/field.form.sections.settings.fields.type-options.editor'),
                                        'markdown'      => __('fields::filament/resources/field.form.sections.settings.fields.type-options.markdown'),
                                        'color'         => __('fields::filament/resources/field.form.sections.settings.fields.type-options.color'),
                                    ]),
                                Select::make('input_type')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.input-type'))
                                    ->required()
                                    ->disabledOn('edit')
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => $get('type') == 'text')
                                    ->options([
                                        'text'     => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.text'),
                                        'email'    => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.email'),
                                        'numeric'  => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.numeric'),
                                        'integer'  => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.integer'),
                                        'password' => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.password'),
                                        'tel'      => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.tel'),
                                        'url'      => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.url'),
                                        'color'    => __('fields::filament/resources/field.form.sections.settings.fields.input-type-options.color'),
                                    ]),
                                Toggle::make('is_multiselect')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.is-multiselect'))
                                    ->required()
                                    ->visible(fn (Get $get): bool => $get('type') == 'select')
                                    ->live(),
                                TextInput::make('sort')
                                    ->label(__('fields::filament/resources/field.form.sections.settings.fields.sort-order'))
                                    ->required()
                                    ->integer()
                                    ->maxLength(255),
                            ]),

                        Section::make(__('fields::filament/resources/field.form.sections.resource.title'))
                            ->schema([
                                Select::make('plugin')
                                    ->label(__('fields::filament/resources/field.form.sections.resource.fields.plugin'))
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->searchable()
                                    ->native(false)
                                    ->live()
                                    ->dehydrated(false)
                                    ->disabledOn('edit')
                                    ->options(fn () => FieldResource::getPluginOptions())
                                    ->afterStateHydrated(fn (Set $set, $record) => $record?->customizable_type
                                        ? $set('plugin', FieldResource::pluginForModel($record->customizable_type))
                                        : null)
                                    ->afterStateUpdated(fn (Set $set) => $set('customizable_type', null)),
                                Select::make('customizable_type')
                                    ->label(__('fields::filament/resources/field.form.sections.resource.fields.resource'))
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->disabledOn('edit')
                                    ->visible(fn (Get $get): bool => filled($get('plugin')))
                                    ->options(fn (Get $get): array => FieldResource::getCustomizableResourceOptions($get('plugin'))),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
