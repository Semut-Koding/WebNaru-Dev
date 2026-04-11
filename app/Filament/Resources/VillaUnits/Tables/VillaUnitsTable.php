<?php

namespace App\Filament\Resources\VillaUnits\Tables;

use App\Filament\Resources\Reservations\ReservationResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class VillaUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Unit Villa')
            ->description('Kelola unit dan fasilitas penginapan unit villa.')
            ->defaultSort('updated_at', 'desc')
            ->modifyQueryUsing(
                fn($query) => $query->with(['villa', 'activeReservation', 'nextReservation'])
            )
            ->groups([
                Group::make('villa.name')
                    ->collapsible(),
                Group::make('status')
                    ->collapsible(),
            ])
            ->defaultGroup('status')
            ->columns([
                TextColumn::make('unit_name')
                    ->label('Nama Unit')
                    ->description(fn($record) => $record->villa->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'occupied' => 'Dihuni',
                        'cleaning' => 'Dibersihkan',
                        'maintenance' => 'Perbaikan',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied' => 'danger',
                        'cleaning' => 'warning',
                        'maintenance' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'available' => 'heroicon-o-check-circle',
                        'occupied' => 'heroicon-o-home',
                        'cleaning' => 'heroicon-o-sparkles',
                        'maintenance' => 'heroicon-o-wrench-screwdriver',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                // Kolom: Penghuni saat ini (link ke reservasi)
                TextColumn::make('activeReservation.guest_name')
                    ->label('Penghuni')
                    ->icon('heroicon-o-user')
                    ->description(fn($record) => $record->activeReservation?->booking_code)
                    ->placeholder('-')
                    ->url(
                        fn($record) => $record->activeReservation
                        ? ReservationResource::getUrl('edit', ['record' => $record->activeReservation->id])
                        : null
                    )
                    ->color('primary'),

                // Kolom: Reservasi mendatang terdekat
                TextColumn::make('nextReservation.check_in_date')
                    ->label('Reservasi Mendatang')
                    ->date('d M Y')
                    ->icon('heroicon-o-calendar')
                    ->description(function ($record) {
                        if (!$record->nextReservation) {
                            return null;
                        }
                        $daysLeft = (int) now()->startOfDay()->diffInDays(
                            Carbon::parse($record->nextReservation->check_in_date)->startOfDay(),
                            false
                        );
                        if ($daysLeft <= 0) {
                            return 'Hari ini';
                        }

                        return "dalam {$daysLeft} hari";
                    })
                    ->placeholder('-')
                    ->url(
                        fn($record) => $record->nextReservation
                        ? ReservationResource::getUrl('edit', ['record' => $record->nextReservation->id])
                        : null
                    )
                    ->color('warning'),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d F Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d F Y H:i:s')
                    ->sortable(),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Tersedia',
                        'occupied' => 'Dihuni',
                        'cleaning' => 'Dibersihkan',
                        'maintenance' => 'Perbaikan',
                    ])
                    ->native(false),
                SelectFilter::make('villa_id')
                    ->label('Villa')
                    ->relationship('villa', 'name')
                    ->native(false),

                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->placeholder('Tanpa Data Terhapus')
                    ->trueLabel('Dengan Data Terhapus')
                    ->falseLabel('Hanya Data Terhapus')
                    ->native(false),

            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button(),
            )
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->recordActions([
                EditAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all']);
    }
}
