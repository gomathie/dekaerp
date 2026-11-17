<?php

namespace Webkul\Partner\Filament\Resources\TagResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('partners::filament/resources/tag.form.name'))
                    ->required()
                    ->maxLength(255)
                    // Per company, like the row itself: each client names its own
                    // tags, and two of them may pick the same words
                    // (docs/completed/company-admin-role-plan.md §4k).
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('company_id', current_company_id()),
                    ),
                ColorPicker::make('color')
                    ->label(__('partners::filament/resources/tag.form.color'))
                    ->hexColor(),
            ]);
    }
}
