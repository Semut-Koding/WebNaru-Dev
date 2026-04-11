<x-filament-widgets::widget>
    <x-filament::section heading="Rekap Kunjungan Per Kategori Usia">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-white/10">
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Kategori</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Usia</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Hari Ini</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Kemarin</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Minggu Ini</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Bulan Ini</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Bulan Lalu</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600 dark:text-gray-400">Tren</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $index => $row)
                                    @php
                                        $isTotal = $index === count($rows) - 1;
                                        $curr = $row['today'];
                                        $prev = $row['yesterday'];

                                        if ($prev === 0 && $curr === 0) {
                                            $trend = '—';
                                            $trendClass = 'text-gray-400 dark:text-gray-500';
                                            $trendBg = 'bg-gray-100 dark:bg-gray-800';
                                        } elseif ($prev === 0 && $curr > 0) {
                                            $trend = '✦ Baru';
                                            $trendClass = 'text-blue-600 dark:text-blue-400';
                                            $trendBg = 'bg-blue-50 dark:bg-blue-950';
                                        } else {
                                            $pct = round((($curr - $prev) / $prev) * 100, 1);
                                            if ($pct > 0) {
                                                $trend = '↑ ' . $pct . '%';
                                                $trendClass = 'text-green-600 dark:text-green-400';
                                                $trendBg = 'bg-green-50 dark:bg-green-950';
                                            } elseif ($pct < 0) {
                                                $trend = '↓ ' . abs($pct) . '%';
                                                $trendClass = 'text-red-600 dark:text-red-400';
                                                $trendBg = 'bg-red-50 dark:bg-red-950';
                                            } else {
                                                $trend = '→ 0%';
                                                $trendClass = 'text-gray-500 dark:text-gray-400';
                                                $trendBg = 'bg-gray-100 dark:bg-gray-800';
                                            }
                                        }
                                    @endphp
                                    <tr class="
                                            {{ $isTotal
                        ? 'border-t-2 border-gray-300 dark:border-white/20 bg-gray-50 dark:bg-white/5 font-bold'
                        : 'border-b border-gray-100 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-white/5'
                                            }} transition
                                        ">
                                        <td class="py-3 px-4 text-gray-950 dark:text-white font-medium">
                                            {{ $row['label'] }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $row['range'] }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-950 dark:text-white">
                                            {{ number_format($row['today'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-500 dark:text-gray-400">
                                            {{ number_format($row['yesterday'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-950 dark:text-white">
                                            {{ number_format($row['week'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-950 dark:text-white">
                                            {{ number_format($row['month'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-500 dark:text-gray-400">
                                            {{ number_format($row['last_month'], 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $trendClass }} {{ $trendBg }}">
                                                {{ $trend }}
                                            </span>
                                        </td>
                                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>