<?php

namespace App\Filament\Resources\AttractionCounters\Widgets;

use App\Models\Attraction;
use App\Models\AttractionCounter;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AttractionCounterTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Rekap Kunjungan Per Wahana Hari Ini';
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        // ============================================================
        // SOLUSI N+1: 4 query untuk semua wahana sekaligus
        // ============================================================

        // Query 1: semua wahana, hari ini
        $todayStats = AttractionCounter::query()
            ->whereDate('date', $today)
            ->selectRaw('attraction_id, SUM(count) as total')
            ->groupBy('attraction_id')
            ->get()
            ->keyBy('attraction_id');

        // Query 2: semua wahana, kemarin
        $yesterdayStats = AttractionCounter::query()
            ->whereDate('date', $yesterday)
            ->selectRaw('attraction_id, SUM(count) as total')
            ->groupBy('attraction_id')
            ->get()
            ->keyBy('attraction_id');

        // Query 3: semua wahana, minggu ini
        $weekStats = AttractionCounter::query()
            ->whereBetween('date', [$thisWeekStart, $today])
            ->selectRaw('attraction_id, SUM(count) as total')
            ->groupBy('attraction_id')
            ->get()
            ->keyBy('attraction_id');

        // Query 4: semua wahana, bulan ini
        $monthStats = AttractionCounter::query()
            ->whereBetween('date', [$thisMonthStart, $today])
            ->selectRaw('attraction_id, SUM(count) as total')
            ->groupBy('attraction_id')
            ->get()
            ->keyBy('attraction_id');

        return $table
            ->description("Pantau distribusi trafik pengunjung: rekapitulasi validasi tiket harian, okupansi wahana, dan ringkasan aktivitas operasional secara real-time.")
            ->query(Attraction::query()->where('status', 'active'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Wahana')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('today_count')
                    ->label('Hari Ini')
                    ->getStateUsing(fn($record) => number_format(
                        $todayStats->get($record->id)?->total ?? 0,
                        0,
                        ',',
                        '.'
                    ))
                    ->sortable(query: function (Builder $query, string $direction) use ($today) {
                        $query->withSum(
                            ['counters as today_sum' => fn($q) => $q->whereDate('date', $today)],
                            'count'
                        )->orderBy('today_sum', $direction);
                    }),

                TextColumn::make('yesterday_count')
                    ->label('Kemarin')
                    ->getStateUsing(fn($record) => number_format(
                        $yesterdayStats->get($record->id)?->total ?? 0,
                        0,
                        ',',
                        '.'
                    )),

                TextColumn::make('week_count')
                    ->label('Minggu Ini')
                    ->getStateUsing(fn($record) => number_format(
                        $weekStats->get($record->id)?->total ?? 0,
                        0,
                        ',',
                        '.'
                    )),

                TextColumn::make('month_count')
                    ->label('Bulan Ini')
                    ->getStateUsing(fn($record) => number_format(
                        $monthStats->get($record->id)?->total ?? 0,
                        0,
                        ',',
                        '.'
                    )),

                TextColumn::make('trend')
                    ->label('Tren')
                    ->badge()
                    ->getStateUsing(function ($record) use ($todayStats, $yesterdayStats) {
                        $todayCount = (int) ($todayStats->get($record->id)?->total ?? 0);
                        $yesterdayCount = (int) ($yesterdayStats->get($record->id)?->total ?? 0);

                        if ($yesterdayCount === 0 && $todayCount === 0)
                            return '—';
                        if ($yesterdayCount === 0)
                            return 'Baru';

                        $change = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100, 1);
                        if ($change > 0)
                            return '↑ ' . $change . '%';
                        if ($change < 0)
                            return '↓ ' . abs($change) . '%';
                        return '→ 0%';
                    })
                    ->icon(fn($state): ?string => $state === 'Baru' ? 'heroicon-m-sparkles' : null)
                    ->iconPosition(IconPosition::Before)
                    ->color(fn($state) => match (true) {
                        $state === 'Baru' => 'info',
                        $state === '—' => 'gray',
                        str_contains($state, '↑') => 'success',
                        str_contains($state, '↓') => 'danger',
                        default => 'gray',
                    }),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Kolom'),
            )
            ->defaultSort('name')
            ->paginated([10, 25, 'all'])
            ->striped();
    }
}