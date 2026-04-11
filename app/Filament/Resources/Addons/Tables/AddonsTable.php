<?php

namespace App\Filament\Resources\Addons\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Number;

class AddonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Data Addon Villa')
            ->description('Kelola layanan tambahan villa: makanan, aktivitas, perlengkapan.')
            ->defaultSort('name', 'asc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Add-on')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'food' => 'Makanan',
                        'activity' => 'Aktivitas',
                        'item' => 'Barang',
                        default => $state,
                    })
                    ->color(fn($state) => match ($state) {
                        'food' => 'warning',
                        'activity' => 'info',
                        'item' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'food' => 'heroicon-o-cake',
                        'activity' => 'heroicon-o-play-circle',
                        'item' => 'heroicon-o-archive-box',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                TextColumn::make('pricing_unit')
                    ->label('Satuan Harga')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'flat' => 'Per Order',
                        'per_night' => 'Per Malam',
                        'per_person' => 'Per Orang',
                        'per_person_per_night' => 'Per Orang/Malam',
                        default => $state,
                    }),

                TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(fn($state) => Number::rupiah($state))
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'food' => 'Makanan',
                        'activity' => 'Aktivitas',
                        'item' => 'Barang',
                    ]),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Non-Aktif',
                    ]),
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
                    BulkAction::make('set_type')
                        ->label('Ubah Tipe')
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Ubah Tipe Add-on')
                        ->modalDescription('Pilih tipe baru untuk add-on yang dipilih.')
                        ->modalSubmitActionLabel('Ya, Ubah Tipe')
                        ->form([
                            Select::make('type')
                                ->label('Tipe')
                                ->options([
                                    'food' => 'Makanan',
                                    'activity' => 'Aktivitas',
                                    'item' => 'Barang',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $newType = $data['type'];
                            $updated = $records->filter(fn($r) => $r->type !== $newType)->count();
                            $records->each(fn($r) => $r->update(['type' => $newType]));

                            $label = match ($newType) {
                                'food' => 'Makanan',
                                'activity' => 'Aktivitas',
                                'item' => 'Barang',
                                default => $newType,
                            };

                            Notification::make()
                                ->title('Tipe Diperbarui')
                                ->body("$updated add-on berhasil diubah menjadi \"$label\".")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Add-on Terpilih')
                        ->modalDescription('Add-on yang dipilih akan diaktifkan.')
                        ->modalSubmitActionLabel('Ya, Aktifkan')
                        ->action(function (Collection $records) {
                            $updated = $records->filter(fn($r) => !$r->is_active)->count();
                            $records->each(fn($r) => $r->update(['is_active' => true]));

                            Notification::make()
                                ->title('Add-on Diaktifkan')
                                ->body("$updated add-on berhasil diaktifkan.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Add-on Terpilih')
                        ->modalDescription('Add-on yang dipilih akan dinonaktifkan.')
                        ->modalSubmitActionLabel('Ya, Nonaktifkan')
                        ->action(function (Collection $records) {
                            $updated = $records->filter(fn($r) => $r->is_active)->count();
                            $records->each(fn($r) => $r->update(['is_active' => false]));

                            Notification::make()
                                ->title('Add-on Dinonaktifkan')
                                ->body("$updated add-on berhasil dinonaktifkan.")
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ])->label('Aksi Massal'),
            ])
            ->searchDebounce(500)
            ->emptyStateHeading('Tidak ada data addon')
            ->emptyStateDescription('Belum ada data addon yang terdaftar.')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
