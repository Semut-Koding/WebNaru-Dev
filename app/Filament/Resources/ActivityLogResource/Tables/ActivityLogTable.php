<?php

namespace App\Filament\Resources\ActivityLogResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivityLogTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Aktivitas Sistem')
            ->description('Log aktivitas pengguna dan perubahan data di dalam sistem.')
            ->columns([
                TextColumn::make('log_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('Causer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada log aktivitas')
            ->emptyStateDescription('Belum ada log aktivitas yang tercatat dalam sistem.')
            ->defaultSort('created_at', 'desc');
    }
}
