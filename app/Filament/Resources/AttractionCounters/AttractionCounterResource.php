<?php

namespace App\Filament\Resources\AttractionCounters;

use App\Filament\Resources\AttractionCounters\Pages\CreateAttractionCounter;
use App\Filament\Resources\AttractionCounters\Pages\EditAttractionCounter;
use App\Filament\Resources\AttractionCounters\Pages\ListAttractionCounters;
use App\Filament\Resources\AttractionCounters\Schemas\AttractionCounterForm;
use App\Filament\Resources\AttractionCounters\Tables\AttractionCountersTable;
use App\Models\AttractionCounter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AttractionCounterResource extends Resource
{
    protected static ?string $model = AttractionCounter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-calculator';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?string $navigationLabel = 'Penghitung Wahana';

    public static function getPluralModelLabel(): string
    {
        return __('Pengunjung Wahana');
    }

    public static function form(Schema $schema): Schema
    {
        return AttractionCounterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttractionCountersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttractionCounters::route('/'),
            'create' => CreateAttractionCounter::route('/create'),
            'edit' => EditAttractionCounter::route('/{record}/edit'),
        ];
    }
}
