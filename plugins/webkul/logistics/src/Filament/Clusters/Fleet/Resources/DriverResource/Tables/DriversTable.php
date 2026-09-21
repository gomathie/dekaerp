<?php

namespace Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Webkul\Logistics\Models\Driver;

class DriversTable
{
    protected static string $lang = 'logistics::filament/clusters/fleet/resources/driver';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__(static::$lang.'.table.columns.phone'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('employee.name')
                    ->label(__(static::$lang.'.table.columns.employee'))
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__(static::$lang.'.table.columns.partner'))
                    ->placeholder('—')
                    ->visibleFrom('md')
                    ->sortable(),
                TextColumn::make('license_number')
                    ->label(__(static::$lang.'.table.columns.license-number'))
                    ->placeholder('—')
                    ->visible(fn (): bool => auth()->user()?->can('update_logistics_driver') ?? false)
                    ->searchable(),
                TextColumn::make('license_expires_at')
                    ->label(__(static::$lang.'.table.columns.license-expires-at'))
                    ->date()
                    ->badge()
                    ->color(fn (Driver $record): string => static::licenseColor($record))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__(static::$lang.'.table.columns.company'))
                    ->visibleFrom('lg'),
                IconColumn::make('is_active')
                    ->label(__(static::$lang.'.table.columns.is-active'))
                    ->boolean(),
            ])
            ->filters([
                Filter::make('license_attention')
                    ->label(__(static::$lang.'.table.filters.license-attention'))
                    ->query(fn ($query) => $query->whereNotNull('license_expires_at')->where('license_expires_at', '<=', now()->addDays(30)->toDateString())),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    protected static function licenseColor(Driver $record): string
    {
        if ($record->isLicenseExpired()) {
            return 'danger';
        }

        if ($record->licenseExpiresWithin(30)) {
            return 'warning';
        }

        return 'success';
    }
}
