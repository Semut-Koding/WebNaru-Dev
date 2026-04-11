<?php

namespace App\Filament\Resources\AttractionCounters\Tables;

use App\Models\Attraction;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Actions\CreateAction;

class AttractionCountersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Tiket Wahana')
            ->description('Kelola data penjualan dan penggunaan tiket wahana.')
            ->defaultSort('date', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['attraction', 'operator']))
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d F Y')
                    ->sortable(),
                TextColumn::make('attraction.name')
                    ->label('Wahana')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('count')
                    ->label('Jumlah Tiket')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 0, ',', '');
                    })
                    // ->summarize([
                    //         Sum::make()
                    //             ->label('Total Tiket')
                    //             ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    //     ])
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('operator.name')
                    ->label('Operator')
                    ->sortable()
                    ->searchable(),
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
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                SelectFilter::make('attraction_id')
                    ->label('Wahana')
                    ->native(false)
                    ->relationship('attraction', 'name')
                    ->preload(),

                Filter::make('date_range')
                    ->label('Rentang Tanggal')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['date_from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['date_until'], fn($q, $date) => $q->whereDate('date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators[] = Indicator::make('Dari: ' . Carbon::parse($data['date_from'])->translatedFormat('d F Y'))
                                ->removeField('date_from');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators[] = Indicator::make('Sampai: ' . Carbon::parse($data['date_until'])->translatedFormat('d F Y'))
                                ->removeField('date_until');
                        }

                        return $indicators;
                    }),
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
                    DeleteBulkAction::make()
                        ->label('Hapus Permanen'),
                    ExportBulkAction::make()
                        ->label('Ekspor Data')
                        ->icon('heroicon-o-document-arrow-up'),
                ]),
            ])
            ->emptyStateHeading('Tidak ada data tiket')
            ->emptyStateDescription('Belum ada data penghitungan tiket wahana tersimpan.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Pengunjung Wahana'),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
