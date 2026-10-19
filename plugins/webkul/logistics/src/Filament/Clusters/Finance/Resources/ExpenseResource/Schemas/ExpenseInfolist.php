<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseInfolist
{
    protected static string $lang = 'logistics::filament/clusters/finance/resources/expense';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__(static::$lang.'.infolist.sections.summary'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('state')->label(__(static::$lang.'.infolist.fields.state'))->badge(),
                        TextEntry::make('date')->label(__(static::$lang.'.infolist.fields.date'))->date(),
                        TextEntry::make('amount')->label(__(static::$lang.'.infolist.fields.amount'))->numeric(decimalPlaces: 2),
                        TextEntry::make('category.name')->label(__(static::$lang.'.infolist.fields.category')),
                        TextEntry::make('shipment.name')->label(__(static::$lang.'.infolist.fields.shipment'))->placeholder('-'),
                        TextEntry::make('trip.name')->label(__(static::$lang.'.infolist.fields.trip'))->placeholder('-'),
                        TextEntry::make('vehicle.registration_no')->label(__(static::$lang.'.infolist.fields.vehicle'))->placeholder('-'),
                        TextEntry::make('receipt_path')->label(__(static::$lang.'.infolist.fields.receipt'))->placeholder('-'),
                        TextEntry::make('description')->label(__(static::$lang.'.infolist.fields.description'))->columnSpanFull()->placeholder('-'),
                    ]),
            ]);
    }
}
