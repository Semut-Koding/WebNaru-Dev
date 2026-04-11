<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Pengaturan Sistem')
            ->description('Konfigurasi variabel dan preferensi aplikasi.')
            ->columns([
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('group')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada pengaturan')
            ->emptyStateDescription('Sistem belum memiliki pengaturan kustom.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
