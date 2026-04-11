<?php

namespace App\Filament\Resources\VisitorCounters\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Actions\CreateAction;

class VisitorCountersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Data Kunjungan')
            ->description('Kelola dan pantau data statistik kunjungan harian.')
            ->defaultSort('date', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['cashier']))
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d F Y')
                    ->sortable(),
                TextColumn::make('adult_count')
                    ->label('Dewasa')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ''))
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('Total Dewasa')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                        // Average::make()
                        //     ->label('Rata-rata')
                        //     ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    ]),

                TextColumn::make('teenager_count')
                    ->label('Remaja')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ''))
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('Total Remaja')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                        // Average::make()
                        //     ->label('Rata-rata')
                        //     ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    ]),

                TextColumn::make('child_count')
                    ->label('Anak')
                    ->formatStateUsing(fn($state) => number_format($state, 0, ',', ''))
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->label('Total Anak')
                            ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                        // Average::make()
                        //     ->label('Rata-rata')
                        //     ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
                    ]),

                IconColumn::make('is_group')
                    ->label('Rombongan')
                    ->boolean()
                    ->summarize(Count::make()->icons()),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah Pada')
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
                TernaryFilter::make('is_group')
                    ->label('Rombongan')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak')
                    ->native(false),

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
            ->filtersFormColumns(2)
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
                ])->label('Aksi Massal'),
            ])
            ->emptyStateHeading('Tidak ada data kunjungan')
            ->emptyStateDescription('Belum ada data kunjungan terdaftar.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Tambah Data Pengunjung'),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
