<?php

namespace App\Filament\Resources\VisitorCounters\Widgets;

use App\Models\VisitorCounter;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;

class VisitorCounterTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Rekap Kunjungan Per Kategori Usia';
    protected static ?string $pollingInterval = '30s';
    protected int|string|array $columnSpan = 'full';

    // Cache stats agar tidak query ulang per kolom
    private ?object $cachedStats = null;

    private function getStats(): object
    {
        if ($this->cachedStats)
            return $this->cachedStats;

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        $this->cachedStats = VisitorCounter::query()
            ->selectRaw("
                SUM(CASE WHEN date = '$today' THEN adult_count ELSE 0 END) as adult_today,
                SUM(CASE WHEN date = '$yesterday' THEN adult_count ELSE 0 END) as adult_yesterday,
                SUM(CASE WHEN date >= '$weekStart' THEN adult_count ELSE 0 END) as adult_week,
                SUM(CASE WHEN date >= '$monthStart' THEN adult_count ELSE 0 END) as adult_month,
                SUM(CASE WHEN date BETWEEN '$lastMonthStart' AND '$lastMonthEnd' THEN adult_count ELSE 0 END) as adult_last_month,
                SUM(CASE WHEN date = '$today' THEN teenager_count ELSE 0 END) as teen_today,
                SUM(CASE WHEN date = '$yesterday' THEN teenager_count ELSE 0 END) as teen_yesterday,
                SUM(CASE WHEN date >= '$weekStart' THEN teenager_count ELSE 0 END) as teen_week,
                SUM(CASE WHEN date >= '$monthStart' THEN teenager_count ELSE 0 END) as teen_month,
                SUM(CASE WHEN date BETWEEN '$lastMonthStart' AND '$lastMonthEnd' THEN teenager_count ELSE 0 END) as teen_last_month,
                SUM(CASE WHEN date = '$today' THEN child_count ELSE 0 END) as child_today,
                SUM(CASE WHEN date = '$yesterday' THEN child_count ELSE 0 END) as child_yesterday,
                SUM(CASE WHEN date >= '$weekStart' THEN child_count ELSE 0 END) as child_week,
                SUM(CASE WHEN date >= '$monthStart' THEN child_count ELSE 0 END) as child_month,
                SUM(CASE WHEN date BETWEEN '$lastMonthStart' AND '$lastMonthEnd' THEN child_count ELSE 0 END) as child_last_month
            ")
            ->first();

        return $this->cachedStats;
    }

    public function table(Table $table): Table
    {
        $stats = $this->getStats();

        $rows = [
            1 => [
                'type' => 'adult',
                'label' => 'Dewasa',
                'range' => '18-59 Tahun',
                'today' => (int) ($stats->adult_today ?? 0),
                'yesterday' => (int) ($stats->adult_yesterday ?? 0),
                'week' => (int) ($stats->adult_week ?? 0),
                'month' => (int) ($stats->adult_month ?? 0),
                'last_month' => (int) ($stats->adult_last_month ?? 0),
            ],
            2 => [
                'type' => 'teen',
                'label' => 'Remaja',
                'range' => '13-17 Tahun',
                'today' => (int) ($stats->teen_today ?? 0),
                'yesterday' => (int) ($stats->teen_yesterday ?? 0),
                'week' => (int) ($stats->teen_week ?? 0),
                'month' => (int) ($stats->teen_month ?? 0),
                'last_month' => (int) ($stats->teen_last_month ?? 0),
            ],
            3 => [
                'type' => 'child',
                'label' => 'Anak-anak',
                'range' => '< 13 Tahun',
                'today' => (int) ($stats->child_today ?? 0),
                'yesterday' => (int) ($stats->child_yesterday ?? 0),
                'week' => (int) ($stats->child_week ?? 0),
                'month' => (int) ($stats->child_month ?? 0),
                'last_month' => (int) ($stats->child_last_month ?? 0),
            ],
            4 => [
                'type' => 'total',
                'label' => 'Total',
                'range' => 'Semua Kategori',
                'today' => (int) ($stats->adult_today ?? 0) + (int) ($stats->teen_today ?? 0) + (int) ($stats->child_today ?? 0),
                'yesterday' => (int) ($stats->adult_yesterday ?? 0) + (int) ($stats->teen_yesterday ?? 0) + (int) ($stats->child_yesterday ?? 0),
                'week' => (int) ($stats->adult_week ?? 0) + (int) ($stats->teen_week ?? 0) + (int) ($stats->child_week ?? 0),
                'month' => (int) ($stats->adult_month ?? 0) + (int) ($stats->teen_month ?? 0) + (int) ($stats->child_month ?? 0),
                'last_month' => (int) ($stats->adult_last_month ?? 0) + (int) ($stats->teen_last_month ?? 0) + (int) ($stats->child_last_month ?? 0),
            ],
        ];

        return $table
            // Query ke VisitorCounter dengan LIMIT 4 sebagai "anchor" rows
            // lalu data asli di-override via getStateUsing
            ->query(
                fn(): Builder => VisitorCounter::query()
                    ->selectRaw('ROW_NUMBER() OVER (ORDER BY id) as id, 1 as _anchor')
                    ->groupBy('id')
                    ->limit(4)
            )
            ->columns([
                TextColumn::make('label')
                    ->label('Kategori')
                    ->weight('bold')
                    ->getStateUsing(fn($record) => $rows[$record->id]['label'] ?? '—'),

                TextColumn::make('range')
                    ->label('Rentang Usia')
                    ->color('gray')
                    ->getStateUsing(fn($record) => $rows[$record->id]['range'] ?? '—'),

                TextColumn::make('today')
                    ->label('Hari Ini')
                    ->alignCenter()
                    ->weight('bold')
                    ->getStateUsing(fn($record) => number_format($rows[$record->id]['today'] ?? 0, 0, ',', '.')),

                TextColumn::make('yesterday')
                    ->label('Kemarin')
                    ->alignCenter()
                    ->color('gray')
                    ->getStateUsing(fn($record) => number_format($rows[$record->id]['yesterday'] ?? 0, 0, ',', '.')),

                TextColumn::make('week')
                    ->label('Minggu Ini')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => number_format($rows[$record->id]['week'] ?? 0, 0, ',', '.')),

                TextColumn::make('month')
                    ->label('Bulan Ini')
                    ->alignCenter()
                    ->getStateUsing(fn($record) => number_format($rows[$record->id]['month'] ?? 0, 0, ',', '.')),

                TextColumn::make('last_month')
                    ->label('Bulan Lalu')
                    ->alignCenter()
                    ->color('gray')
                    ->getStateUsing(fn($record) => number_format($rows[$record->id]['last_month'] ?? 0, 0, ',', '.')),

                TextColumn::make('trend')
                    ->label('Tren')
                    ->badge()
                    ->alignCenter()
                    ->getStateUsing(function ($record) use ($rows) {
                        $row = $rows[$record->id] ?? null;
                        if (!$row)
                            return '—';

                        $curr = $row['today'];
                        $prev = $row['yesterday'];

                        if ($prev === 0 && $curr === 0)
                            return '—';
                        if ($prev === 0 && $curr > 0)
                            return 'Baru';

                        $change = round((($curr - $prev) / $prev) * 100, 1);
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
            ->paginated(false)
            ->striped();
    }
}