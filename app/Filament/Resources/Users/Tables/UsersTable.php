<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Actions\CreateAction;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Manajemen Pegawai')
            ->description('Kelola akun, peran, dan akses pegawai sistem.')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama & Email')
                    ->description(fn($record) => $record->email)
                    ->searchable(['name', 'email'])
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->deleted_at) {
                            return $state . ' <p style="color: #ef4444; font-size: 0.8rem; font-weight: 600;"> Data Terhapus Sementara</p>';
                        }
                        return $state;
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->placeholder('Belum Diverifikasi'),
                TextColumn::make('phone')
                    ->label('No. Telepon')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->summarize(Count::make()->icons()),
                IconColumn::make('is_online')
                    ->label('Online')
                    ->boolean()
                    ->summarize(Count::make()->icons()),
                TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d F Y H:i')
                    ->sortable()
                    ->placeholder('Belum Pernah Login'),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d F Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime('d F Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Dihapus Pada')
                    ->dateTime('d F Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->placeholder('Tanpa Data Terhapus')
                    ->trueLabel('Dengan Data Terhapus')->default()
                    ->falseLabel('Hanya Data Terhapus')
                    ->native(false),

                SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),

                TernaryFilter::make('is_online')
                    ->label('Status Online')
                    ->placeholder('Semua')
                    ->trueLabel('Sedang Online')
                    ->falseLabel('Tidak Online')
                    ->native(false),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button(),
            )
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Tidak ada data pegawai')
            ->emptyStateDescription('Belum ada Pegawai sistem yang terdaftar.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Pegawai'),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
