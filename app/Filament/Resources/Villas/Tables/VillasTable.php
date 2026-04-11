<?php

namespace App\Filament\Resources\Villas\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Number;

class VillasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Villa')
            ->description('Kelola unit dan fasilitas penginapan villa.')
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Villa')
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
                TextColumn::make('bedroom_count')
                    ->label('Kamar')
                    ->formatStateUsing(fn($state) => $state . ' Kamar Tidur')
                    ->description(fn($record) => $record->bathroom_count . ' Kamar Mandi')
                    ->sortable(),
                TextColumn::make('amenities')
                    ->label('Fasilitas')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('benefits')
                    ->label('Keuntungan')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('units_count')
                    ->counts('units')
                    ->label('Jumlah Unit')
                    ->sortable()
                    ->summarize([
                        Sum::make()->label('Total Unit'),
                        Average::make()
                            ->label('Rata-rata Unit')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    ]),
                TextColumn::make('base_price_weekend')
                    ->label('Harga')
                    ->formatStateUsing(fn($state) => 'Weekend: ' . Number::rupiah($state))
                    ->description(fn($record) => 'Weekday: ' . Number::rupiah($record->base_price_weekday))
                    ->sortable()
                    ->summarize([
                        // Summarizer::make()
                        //     ->label('Weekend Termurah')
                        //     ->using(fn(Builder $query) => $query->min('base_price_weekend'))
                        //     ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        Summarizer::make()
                            ->label('Weekend Rata-rata')
                            ->using(fn(Builder $query) => $query->avg('base_price_weekend'))
                            ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        // Summarizer::make()
                        //     ->label('Weekend Termahal')
                        //     ->using(fn(Builder $query) => $query->max('base_price_weekend'))
                        //     ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        // Summarizer::make()
                        //     ->label('Weekday Termurah')
                        //     ->using(fn(Builder $query) => $query->min('base_price_weekday'))
                        //     ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        Summarizer::make()
                            ->label('Weekday Rata-rata')
                            ->using(fn(Builder $query) => $query->avg('base_price_weekday'))
                            ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                        // Summarizer::make()
                        //     ->label('Weekday Termahal')
                        //     ->using(fn(Builder $query) => $query->max('base_price_weekday'))
                        //     ->formatStateUsing(fn($state) => Number::rupiah((int) $state)),
                    ]),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'coming_soon' => 'Segera Hadir',
                        'closed' => 'Ditutup',
                    ])
                    ->sortable()
                    ->summarize([
                        Count::make()
                            ->label('Tersedia')
                            ->query(fn($query) => $query->where('status', 'available')),
                        Count::make()
                            ->label('Segera Hadir')
                            ->query(fn($query) => $query->where('status', 'coming_soon')),
                        Count::make()
                            ->label('Ditutup')
                            ->query(fn($query) => $query->where('status', 'closed')),
                    ]),
                TextColumn::make('coordinate')
                    ->label('Koordinat')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
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

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->reorderRecordsTriggerAction(
                fn(Action $action, bool $isReordering) => $action
                    ->button()
                    ->label($isReordering ? 'Nonaktifkan Urutan' : 'Aktifkan Urutan'),
            )
            ->emptyStateHeading('Tidak ada data villa')
            ->emptyStateDescription('Belum ada data villa yang terdaftar.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Data Villa'),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
