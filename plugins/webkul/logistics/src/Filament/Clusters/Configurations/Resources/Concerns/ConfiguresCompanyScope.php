<?php

namespace Webkul\Logistics\Filament\Clusters\Configurations\Resources\Concerns;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Validation\Rules\Unique;
use Webkul\Support\Services\CompanyContext;

/**
 * Shared pieces of the Logistics configuration resources.
 *
 * Configuration rows with no company are shared by all companies. Only users
 * who see every company may create or change those (enforced again in
 * ConfigurationPolicy); everyone else creates rows for one of their companies.
 */
trait ConfiguresCompanyScope
{
    protected static string $commonLang = 'logistics::filament/clusters/configurations/resources/common';

    public static function nameField(): TextInput
    {
        return TextInput::make('name')
            ->label(__(static::$commonLang.'.fields.name'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    public static function codeField(): TextInput
    {
        return TextInput::make('code')
            ->label(__(static::$commonLang.'.fields.code'))
            ->maxLength(32)
            ->alphaDash()
            ->unique(
                ignoreRecord: true,
                modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('company_id', $get('company_id')),
            );
    }

    public static function activeField(): Toggle
    {
        return Toggle::make('is_active')
            ->label(__(static::$commonLang.'.fields.is-active'))
            ->default(true);
    }

    public static function companyField(): Select
    {
        $context = app(CompanyContext::class);
        $seesAll = $context->seesAllCompanies();

        return Select::make('company_id')
            ->label(__(static::$commonLang.'.fields.company'))
            ->options(fn (): array => $context->allowedCompanies()->pluck('name', 'id')->all())
            ->placeholder(__(static::$commonLang.'.fields.all-companies'))
            ->helperText(__(static::$commonLang.'.fields.company-helper'))
            ->required(! $seesAll)
            ->default($seesAll ? null : $context->currentId())
            ->searchable();
    }

    public static function nameColumn(): TextColumn
    {
        return TextColumn::make('name')
            ->label(__(static::$commonLang.'.columns.name'))
            ->searchable()
            ->sortable();
    }

    public static function codeColumn(): TextColumn
    {
        return TextColumn::make('code')
            ->label(__(static::$commonLang.'.columns.code'))
            ->placeholder('—')
            ->searchable()
            ->sortable();
    }

    public static function companyColumn(): TextColumn
    {
        return TextColumn::make('company.name')
            ->label(__(static::$commonLang.'.columns.company'))
            ->placeholder(__(static::$commonLang.'.columns.all-companies'))
            ->visibleFrom('md')
            ->sortable();
    }

    public static function activeColumn(): IconColumn
    {
        return IconColumn::make('is_active')
            ->label(__(static::$commonLang.'.columns.is-active'))
            ->boolean()
            ->sortable();
    }

    public static function activeFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_active')
            ->label(__(static::$commonLang.'.filters.is-active'));
    }

    public static function companyFilter(): SelectFilter
    {
        return SelectFilter::make('company_id')
            ->label(__(static::$commonLang.'.filters.company'))
            ->relationship('company', 'name');
    }
}
