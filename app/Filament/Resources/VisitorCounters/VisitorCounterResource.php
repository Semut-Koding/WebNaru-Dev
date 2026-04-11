<?php

namespace App\Filament\Resources\VisitorCounters;

use App\Filament\Resources\VisitorCounters\Pages\CreateVisitorCounter;
use App\Filament\Resources\VisitorCounters\Pages\EditVisitorCounter;
use App\Filament\Resources\VisitorCounters\Pages\ListVisitorCounters;
use App\Filament\Resources\VisitorCounters\Schemas\VisitorCounterForm;
use App\Filament\Resources\VisitorCounters\Tables\VisitorCountersTable;
use App\Models\VisitorCounter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VisitorCounterResource extends Resource
{
    protected static ?string $model = VisitorCounter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-users';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    protected static ?string $navigationLabel = 'Penghitung Pengunjung';

    public static function getPluralModelLabel(): string
    {
        return __('Pengunjung Wisata');
    }

    public static function form(Schema $schema): Schema
    {
        return VisitorCounterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisitorCountersTable::configure($table);
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
            'index' => ListVisitorCounters::route('/'),
            'create' => CreateVisitorCounter::route('/create'),
            'edit' => EditVisitorCounter::route('/{record}/edit'),
        ];
    }
}
