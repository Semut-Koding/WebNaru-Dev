<?php

namespace App\Filament\Resources\VillaUnits;

use App\Filament\Resources\VillaUnits\Pages\CreateVillaUnit;
use App\Filament\Resources\VillaUnits\Pages\EditVillaUnit;
use App\Filament\Resources\VillaUnits\Pages\ListVillaUnits;
use App\Filament\Resources\VillaUnits\Schemas\VillaUnitForm;
use App\Filament\Resources\VillaUnits\Tables\VillaUnitsTable;
use App\Models\VillaUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class VillaUnitResource extends Resource
{
    protected static ?string $model = VillaUnit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-building-office';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    public static function getPluralModelLabel(): string
    {
        return __('Unit Villa');
    }

    public static function form(Schema $schema): Schema
    {
        return VillaUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillaUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillaUnits::route('/'),
            'create' => CreateVillaUnit::route('/create'),
            'edit' => EditVillaUnit::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
