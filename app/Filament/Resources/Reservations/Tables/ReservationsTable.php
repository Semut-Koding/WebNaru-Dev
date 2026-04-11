<?php

namespace App\Filament\Resources\Reservations\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Number;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Data Reservasi')
            ->description('Kelola pemesanan, pembayaran, dan status reservasi villa.')
            ->modifyQueryUsing(
                fn($query) => $query->with(['creator', 'villaUnit.villa'])
            )
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->description(fn($record) => $record->creator?->name ? 'oleh: ' . $record->creator->name : 'Walk-in'),

                TextColumn::make('guest_name')
                    ->label('Tamu')
                    ->searchable()
                    ->icon('heroicon-m-user')
                    ->description(fn($record) => $record->guest_phone),

                TextColumn::make('villaUnit.unit_name')
                    ->label('Unit Villa')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->villaUnit?->villa?->name),

                TextColumn::make('check_in_date')
                    ->label('Check-in → Out')
                    ->date('d M Y')
                    ->sortable()
                    ->description(function ($record) {
                        $checkOut = Carbon::parse($record->check_out_date)->translatedFormat('d M Y');
                        $nights = Carbon::parse($record->check_in_date)->diffInDays($record->check_out_date);
                        return "→ {$checkOut} ({$nights} malam)";
                    }),

                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->formatStateUsing(fn($state) => Number::rupiah($state))
                    ->sortable()
                    ->description(function ($record) {
                        $sisa = $record->total_price - ($record->paid_amount ?? 0);
                        if ($sisa <= 0)
                            return '✓ Lunas';
                        return 'Sisa: ' . Number::rupiah($sisa);
                    }),

                TextColumn::make('status')
                    ->label('Status Reservasi')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'booked' => 'Booked',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'booked',
                        'success' => fn($state) => in_array($state, ['checked_in', 'checked_out']),
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'unpaid' => 'Belum Bayar',
                        'dp_paid' => 'DP Dibayar',
                        'paid' => 'Lunas',
                        'refunded' => 'Refund',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'dp_paid',
                        'success' => 'paid',
                        'gray' => 'refunded',
                    ]),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'pending' => 'Pending',
                        'booked' => 'Booked',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Dibatalkan',
                    ]),
                SelectFilter::make('payment_status')
                    ->label('Pembayaran')
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'dp_paid' => 'DP Dibayar',
                        'paid' => 'Lunas',
                        'refunded' => 'Refund',
                    ]),
                Filter::make('check_in_range')
                    ->form([
                        DatePicker::make('check_in_from')
                            ->label('Check-in Dari'),
                        DatePicker::make('check_in_until')
                            ->label('Check-in Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['check_in_from'], fn($q, $date) => $q->whereDate('check_in_date', '>=', $date))
                            ->when($data['check_in_until'], fn($q, $date) => $q->whereDate('check_in_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['check_in_from'] ?? null) {
                            $indicators[] = 'Dari: ' . Carbon::parse($data['check_in_from'])->translatedFormat('d M Y');
                        }
                        if ($data['check_in_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . Carbon::parse($data['check_in_until'])->translatedFormat('d M Y');
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
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100, 'all'])
            ->emptyStateHeading('Tidak ada data reservasi')
            ->emptyStateDescription('Belum ada data reservasi dibuat.')
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
