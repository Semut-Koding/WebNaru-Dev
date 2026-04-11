<?php

namespace App\Filament\Resources\Attractions;

use App\Filament\Resources\Attractions\Pages\CreateAttraction;
use App\Filament\Resources\Attractions\Pages\EditAttraction;
use App\Filament\Resources\Attractions\Pages\ListAttractions;
use App\Filament\Resources\Attractions\Schemas\AttractionForm;
use App\Filament\Resources\Attractions\Tables\AttractionsTable;
use App\Models\Attraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AttractionResource extends Resource
{
    protected static ?string $model = Attraction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-ticket';

    protected static string|UnitEnum|null $navigationGroup = 'Data Induk';

    public static function getPluralModelLabel(): string
    {
        return __('Wahana');
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    public static function form(Schema $schema): Schema
    {
        return AttractionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttractionsTable::configure($table);
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
            'index' => ListAttractions::route('/'),
            'create' => CreateAttraction::route('/create'),
            'edit' => EditAttraction::route('/{record}/edit'),
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
