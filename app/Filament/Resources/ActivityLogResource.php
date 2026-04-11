<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;
use UnitEnum;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|BackedEnum|null $activeNavigationIcon = 'heroicon-s-clipboard-document-list';
    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    public static function getPluralModelLabel(): string
    {
        return __('Aktivitas Log');
    }


    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityLogResource\Schemas\ActivityLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogResource\Tables\ActivityLogTable::configure($table);
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
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
}
