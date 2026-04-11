<?php

namespace App\Filament\Resources\Attractions\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Query\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Filament\Actions\CreateAction;

class AttractionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Wahana')
            ->description('Kelola fasilitas wahana yang tersedia beserta harganya.')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Wahana')
                    ->searchable()
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->deleted_at) {
                            return $state . ' <p style="color: #ef4444; font-size: 0.8rem; font-weight: 600;"> Data Terhapus Sementara</p>';
                        }
                        return $state;
                    }),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                TextColumn::make('base_price')
                    ->label('Harga')
                    ->formatStateUsing(fn($state) => Number::rupiah($state))
                    ->sortable()
                    ->summarize([
                        Summarizer::make()
                            ->label('Termurah')
                            ->using(fn(Builder $query) => $query->min('base_price'))
                            ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        Average::make()
                            ->label('Rata-rata')
                            ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        Summarizer::make()
                            ->label('Termahal')
                            ->using(fn(Builder $query) => $query->max('base_price'))
                            ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                    ]),
                IconColumn::make('is_free')
                    ->label('Gratis')
                    ->boolean()
                    ->summarize(Count::make()->icons()),

                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'coming_soon' => 'Segera Hadir',
                        'closed' => 'Ditutup',
                    ])
                    ->native(false)
                    ->sortable()
                    ->summarize([
                        Count::make()
                            ->label('Aktif')
                            ->query(fn($query) => $query->where('status', 'active')),
                        Count::make()
                            ->label('Segera Hadir')
                            ->query(fn($query) => $query->where('status', 'coming_soon')),
                        Count::make()
                            ->label('Ditutup')
                            ->query(fn($query) => $query->where('status', 'closed')),
                    ]),
                TextColumn::make('coordinate')
                    ->label('Koordinat Maps')
                    ->toggleable(isToggledHiddenByDefault: true),
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

                TernaryFilter::make('is_free')
                    ->label('Harga')
                    ->placeholder('Semua')
                    ->trueLabel('Gratis')
                    ->falseLabel('Berbayar')
                    ->native(false),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'coming_soon' => 'Segera Hadir',
                        'closed' => 'Ditutup',
                    ])
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
                    // Bulk Action Status
                    BulkAction::make('set_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Ubah Status Wahana')
                        ->modalDescription('Pilih status baru untuk wahana yang dipilih.')
                        ->modalSubmitActionLabel('Ya, Ubah Status')
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Aktif',
                                    'coming_soon' => 'Segera Hadir',
                                    'closed' => 'Ditutup',
                                ])
                                ->required()
                                ->native(false),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $newStatus = $data['status'];
                            $updated = $records->filter(fn($r) => $r->status !== $newStatus)->count();
                            $records->each(fn($r) => $r->update(['status' => $newStatus]));

                            $label = match ($newStatus) {
                                'active' => 'Aktif',
                                'coming_soon' => 'Segera Hadir',
                                'closed' => 'Ditutup',
                                default => $newStatus,
                            };

                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body("$updated wahana berhasil diubah menjadi \"$label\".")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Action is_free
                    BulkAction::make('set_free')
                        ->label('Jadikan Gratis')
                        ->icon('heroicon-o-gift')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Jadikan Gratis')
                        ->modalDescription('Wahana yang dipilih akan dijadikan gratis.')
                        ->modalSubmitActionLabel('Ya, Jadikan Gratis')
                        ->action(function (Collection $records) {
                            $updated = $records->filter(fn($r) => !$r->is_free)->count();
                            $records->each(fn($r) => $r->update(['is_free' => true]));

                            Notification::make()
                                ->title('Wahana Dijadikan Gratis')
                                ->body("$updated wahana berhasil dijadikan gratis.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('set_paid')
                        ->label('Jadikan Berbayar')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Jadikan Berbayar')
                        ->modalDescription('Wahana yang dipilih akan dijadikan berbayar.')
                        ->modalSubmitActionLabel('Ya, Jadikan Berbayar')
                        ->action(function (Collection $records) {
                            $updated = $records->filter(fn($r) => $r->is_free)->count();
                            $records->each(fn($r) => $r->update(['is_free' => false]));

                            Notification::make()
                                ->title('Wahana Dijadikan Berbayar')
                                ->body("$updated wahana berhasil dijadikan berbayar.")
                                ->warning()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->label('Hapus Sementara'),
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen'),
                    RestoreBulkAction::make()
                        ->label('Kembalikan Data'),
                ]),
            ])
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->color($isReordering ? 'danger' : 'gray'),
            )
            ->searchDebounce(500)
            ->emptyStateHeading('Tidak ada data wahana')
            ->emptyStateDescription('Belum ada data wahana yang terdaftar.')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
        ;
    }
}
