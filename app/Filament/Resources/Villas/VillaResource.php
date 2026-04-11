<?php

namespace App\Filament\Resources\Villas;

use App\Filament\Resources\Villas\Pages\CreateVilla;
use App\Filament\Resources\Villas\Pages\EditVilla;
use App\Filament\Resources\Villas\Pages\ListVillas;
use App\Filament\Resources\Villas\Pages\ViewVilla;
use App\Filament\Resources\Villas\Schemas\VillaForm;
use App\Filament\Resources\Villas\Schemas\VillaInfolist;
use App\Filament\Resources\Villas\Tables\VillasTable;
use App\Models\Villa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class VillaResource extends Resource
{
    protected static ?string $model = Villa::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home-modern';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-home-modern';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    public static function getPluralModelLabel(): string
    {
        return __('Villa');
    }


    public static function form(Schema $schema): Schema
    {
        return VillaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VillaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillas::route('/'),
            'create' => CreateVilla::route('/create'),
            'view' => ViewVilla::route('/{record}'),
            'edit' => EditVilla::route('/{record}/edit'),
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
