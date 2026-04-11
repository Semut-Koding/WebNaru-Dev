<?php

namespace App\Filament\Resources\Addons\Widgets;

use App\Models\Addon;
use App\Models\VillaAddonOrder;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Number;

class AddonTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = Carbon::today();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $thisYearStart = Carbon::now()->startOfYear();

        // ============================================================
        // SOLUSI N+1: Satu query per periode, hasilnya di-cache
        // Total: 5 query untuk semua addon sekaligus, bukan N*6 query
        // ============================================================

        // Query 1: Agregat hari ini
        $todayStats = VillaAddonOrder::query()
            ->whereDate('created_at', $today)
            ->selectRaw('addon_id, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->groupBy('addon_id')
            ->get()
            ->keyBy('addon_id'); // index by addon_id untuk O(1) lookup

        // Query 2: Agregat bulan ini
        $monthStats = VillaAddonOrder::query()
            ->whereBetween('created_at', [$thisMonthStart, Carbon::now()])
            ->selectRaw('addon_id, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->groupBy('addon_id')
            ->get()
            ->keyBy('addon_id');

        // Query 3: Agregat bulan lalu
        $lastMonthStats = VillaAddonOrder::query()
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->selectRaw('addon_id, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->groupBy('addon_id')
            ->get()
            ->keyBy('addon_id');

        // Query 4: Agregat tahun ini
        $yearStats = VillaAddonOrder::query()
            ->whereBetween('created_at', [$thisYearStart, Carbon::now()])
            ->selectRaw('addon_id, SUM(quantity) as qty, SUM(subtotal) as revenue')
            ->groupBy('addon_id')
            ->get()
            ->keyBy('addon_id');

        return $table
            ->heading('Analitik Pendapatan Per Addon')
            ->description('Evaluasi performa finansial: tren penjualan harian, akumulasi periode, dan kontribusi laba layanan tambahan.')
            ->query(Addon::query()->withCount('addonOrders'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Add-on')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn($record) => $record->price ? Number::rupiah($record->price) : ''),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
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
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Lookup dari collection — O(1), bukan query baru
                TextColumn::make('today_qty')
                    ->label('Qty Hari Ini')
                    ->getStateUsing(fn($record) => $todayStats->get($record->id)?->qty ?: '—'),

                TextColumn::make('today_revenue')
                    ->label('Pendapatan Hari Ini')
                    ->getStateUsing(function ($record) use ($todayStats) {
                        $total = $todayStats->get($record->id)?->revenue ?? 0;
                        return $total > 0 ? Number::rupiah($total) : '—';
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Hari Ini')
                            ->formatStateUsing(fn() => Number::rupiah(
                                $todayStats->sum('revenue')
                            ))
                    ),

                TextColumn::make('month_qty')
                    ->label('Qty Bulan Ini')
                    ->getStateUsing(fn($record) => $monthStats->get($record->id)?->qty ?: '—')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('month_revenue')
                    ->label('Pendapatan Bulan Ini')
                    ->getStateUsing(function ($record) use ($monthStats) {
                        $total = $monthStats->get($record->id)?->revenue ?? 0;
                        return $total > 0 ? Number::rupiah($total) : '—';
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Bulan Ini')
                            ->formatStateUsing(fn() => Number::rupiah(
                                $monthStats->sum('revenue')
                            ))
                    ),

                TextColumn::make('last_month_revenue')
                    ->label('Bulan Lalu')
                    ->getStateUsing(function ($record) use ($lastMonthStats) {
                        $total = $lastMonthStats->get($record->id)?->revenue ?? 0;
                        return $total > 0 ? Number::rupiah($total) : '—';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('year_revenue')
                    ->label('Total Tahun Ini')
                    ->getStateUsing(function ($record) use ($yearStats) {
                        $total = $yearStats->get($record->id)?->revenue ?? 0;
                        return $total > 0 ? Number::rupiah($total) : '—';
                    })
                    ->weight('bold')
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Tahun Ini')
                            ->formatStateUsing(fn() => Number::rupiah(
                                $yearStats->sum('revenue')
                            ))
                    ),

                TextColumn::make('trend')
                    ->label('Tren Bulan Ini')
                    ->getStateUsing(function ($record) use ($monthStats, $lastMonthStats) {
                        $thisMonth = (int) ($monthStats->get($record->id)?->revenue ?? 0);
                        $lastMonth = (int) ($lastMonthStats->get($record->id)?->revenue ?? 0);

                        if ($lastMonth === 0 && $thisMonth === 0)
                            return '—';
                        if ($lastMonth === 0)
                            return 'Baru';

                        $change = round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
                        if ($change > 0)
                            return '↑ ' . $change . '%';
                        if ($change < 0)
                            return '↓ ' . abs($change) . '%';
                        return '→ 0%';
                    })
                    ->badge()
                    ->icon(fn($state): ?string => $state === 'Baru' ? 'heroicon-o-sparkles' : null)
                    ->iconPosition(IconPosition::Before)
                    ->color(function ($record) use ($monthStats, $lastMonthStats) {
                        $thisMonth = (int) ($monthStats->get($record->id)?->revenue ?? 0);
                        $lastMonth = (int) ($lastMonthStats->get($record->id)?->revenue ?? 0);

                        if ($lastMonth === 0)
                            return 'info';
                        $change = $thisMonth - $lastMonth;
                        if ($change > 0)
                            return 'success';
                        if ($change < 0)
                            return 'danger';
                        return 'gray';
                    }),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->filters([
                // Filter 1: Periode Pendapatan (DEFAULT aktif - hanya tampil yang punya pendapatan)
                Filter::make('has_revenue')
                    ->label('Hanya yang Berpendapatan')
                    ->default()
                    ->query(fn(Builder $query) => $query->whereHas('addonOrders'))
                    ->toggle(),

                // Filter 2: Tipe Addon
                SelectFilter::make('type')
                    ->label('Tipe Add-on')
                    ->options([
                        'food' => 'Makanan',
                        'activity' => 'Aktivitas',
                        'item' => 'Barang',
                    ])
                    ->native(false),

                // Filter 3: Status Aktif
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),

                // Filter 4: Minimum Pendapatan Bulan Ini
                Filter::make('min_revenue_month')
                    ->label('Min. Pendapatan Bulan Ini')
                    ->form([
                        TextInput::make('min_month')
                            ->label('Minimal (Rp)')
                            ->numeric()
                            ->placeholder('Contoh: 500000')
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['min_month']))
                            return $query;

                        $min = (int) $data['min_month'];
                        $start = Carbon::now()->startOfMonth();
                        $end = Carbon::now();

                        $addonIds = VillaAddonOrder::query()
                            ->selectRaw('addon_id, SUM(subtotal) as total')
                            ->whereBetween('created_at', [$start, $end])
                            ->groupBy('addon_id')
                            ->havingRaw('SUM(subtotal) >= ?', [$min])
                            ->pluck('addon_id');

                        return $query->whereIn('id', $addonIds);
                    })
                    ->indicateUsing(function (array $data) {
                        if (blank($data['min_month']))
                            return null;
                        return 'Min. Bulan Ini: Rp ' . number_format((int) $data['min_month'], 0, ',', '.');
                    }),

                // Filter 5: Minimum Pendapatan Tahun Ini
                Filter::make('min_revenue_year')
                    ->label('Min. Pendapatan Tahun Ini')
                    ->form([
                        TextInput::make('min_year')
                            ->label('Minimal (Rp)')
                            ->numeric()
                            ->placeholder('Contoh: 5000000')
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['min_year']))
                            return $query;

                        $min = (int) $data['min_year'];
                        $start = Carbon::now()->startOfYear();
                        $end = Carbon::now();

                        $addonIds = VillaAddonOrder::query()
                            ->selectRaw('addon_id, SUM(subtotal) as total')
                            ->whereBetween('created_at', [$start, $end])
                            ->groupBy('addon_id')
                            ->havingRaw('SUM(subtotal) >= ?', [$min])
                            ->pluck('addon_id');

                        return $query->whereIn('id', $addonIds);
                    })
                    ->indicateUsing(function (array $data) {
                        if (blank($data['min_year']))
                            return null;
                        return 'Min. Tahun Ini: Rp ' . number_format((int) $data['min_year'], 0, ',', '.');
                    }),
                // Filter 6: Rentang Tanggal Order
                Filter::make('order_date_range')
                    ->label('Rentang Tanggal Order')
                    ->form([
                        DatePicker::make('order_from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('order_until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['order_from'] ?? null, fn($q) => $q->whereHas(
                                'addonOrders',
                                fn($q2) => $q2->whereDate('created_at', '>=', $data['order_from'])
                            ))
                            ->when($data['order_until'] ?? null, fn($q) => $q->whereHas(
                                'addonOrders',
                                fn($q2) => $q2->whereDate('created_at', '<=', $data['order_until'])
                            ));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['order_from'] ?? null) {
                            $indicators[] = Indicator::make('Dari: ' . Carbon::parse($data['order_from'])->translatedFormat('d F Y'))
                                ->removeField('order_from');
                        }
                        if ($data['order_until'] ?? null) {
                            $indicators[] = Indicator::make('Sampai: ' . Carbon::parse($data['order_until'])->translatedFormat('d F Y'))
                                ->removeField('order_until');
                        }
                        return $indicators;
                    }),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button(),
            )
            ->defaultSort('name')
            ->paginated([10, 25, 'all'])
            ->striped();
    }
}