<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Addon;
use App\Models\Reservation;
use App\Models\Villa;
use App\Models\VillaAddonOrder;
use App\Models\VillaUnit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Number;

class ReservationEditForm
{
    /**
     * Resolve Villa from form state — tries villa_id first, then villa_unit_id as fallback.
     */
    private static function resolveVilla(Get $get): ?Villa
    {
        $villaId = $get('villa_id');
        if ($villaId) {
            $villa = Villa::find($villaId);
            if ($villa) {
                return $villa;
            }
        }

        // Fallback: resolve via villa_unit_id
        $villaUnitId = $get('villa_unit_id');
        if ($villaUnitId) {
            $villaUnit = VillaUnit::with('villa')->find($villaUnitId);
            if ($villaUnit?->villa) {
                return $villaUnit->villa;
            }
        }

        return null;
    }

    /**
     * Calculate date breakdown: weekdays, weekends, total nights.
     */
    private static function getDateBreakdown(Get $get): array
    {
        $checkIn = $get('check_in_date');
        $checkOut = $get('check_out_date');

        if (!$checkIn || !$checkOut) {
            return ['nights' => 0, 'weekdays' => 0, 'weekends' => 0];
        }

        try {
            $startDate = Carbon::parse($checkIn);
            $endDate = Carbon::parse($checkOut);
            $nights = $startDate->diffInDays($endDate);

            $weekdays = 0;
            $weekends = 0;

            for ($i = 0; $i < $nights; $i++) {
                $current = $startDate->copy()->addDays($i);
                $current->isWeekend() ? $weekends++ : $weekdays++;
            }

            return ['nights' => $nights, 'weekdays' => $weekdays, 'weekends' => $weekends];
        } catch (\Exception $e) {
            return ['nights' => 0, 'weekdays' => 0, 'weekends' => 0];
        }
    }

    /**
     * Calculate villa price based on nights (weekday/weekend).
     */
    public static function calculateVillaPrice(Get $get): float
    {
        $villa = self::resolveVilla($get);
        if (!$villa) {
            return 0;
        }

        $dates = self::getDateBreakdown($get);
        if ($dates['nights'] <= 0) {
            return 0;
        }

        return ($dates['weekdays'] * $villa->base_price_weekday) + ($dates['weekends'] * $villa->base_price_weekend);
    }

    /**
     * Calculate total add-ons from repeater state.
     */
    public static function calculateAddonsTotal(Get $get): float
    {
        $addonOrders = $get('addonOrders') ?? [];
        $total = 0;

        foreach ($addonOrders as $order) {
            $addonId = $order['addon_id'] ?? null;
            if (!$addonId) {
                continue;
            }

            $addon = Addon::find($addonId);
            if (!$addon) {
                continue;
            }

            $qty = (int) ($order['quantity'] ?? 1);
            $nights = (int) ($order['nights'] ?? 1);
            $persons = (int) ($order['persons'] ?? 1);

            $total += VillaAddonOrder::calculateSubtotal($addon, $qty, $nights, $persons);
        }

        return $total;
    }

    /**
     * Get detailed add-ons breakdown for display.
     */
    private static function getAddonsBreakdown(Get $get): array
    {
        $addonOrders = $get('addonOrders') ?? [];
        $items = [];

        foreach ($addonOrders as $order) {
            $addonId = $order['addon_id'] ?? null;
            if (!$addonId) {
                continue;
            }

            $addon = Addon::find($addonId);
            if (!$addon) {
                continue;
            }

            $qty = (int) ($order['quantity'] ?? 1);
            $nights = (int) ($order['nights'] ?? 1);
            $persons = (int) ($order['persons'] ?? 1);
            $subtotal = VillaAddonOrder::calculateSubtotal($addon, $qty, $nights, $persons);

            $detail = Number::rupiah($addon->price);
            $multipliers = [];
            if ($qty > 1) {
                $multipliers[] = "{$qty} unit";
            }
            if (in_array($addon->pricing_unit, ['per_night', 'per_person_per_night']) && $nights > 1) {
                $multipliers[] = "{$nights} mlm";
            }
            if (in_array($addon->pricing_unit, ['per_person', 'per_person_per_night']) && $persons > 1) {
                $multipliers[] = "{$persons} org";
            }
            if (!empty($multipliers)) {
                $detail .= ' × ' . implode(' × ', $multipliers);
            }

            $items[] = [
                'name' => $addon->name,
                'detail' => $detail,
                'subtotal' => $subtotal,
            ];
        }

        return $items;
    }

    /**
     * Recalculate total price = villa + addons.
     */
    public static function recalculateTotalPrice(Set $set, Get $get): void
    {
        $villaPrice = self::calculateVillaPrice($get);
        $addonsTotal = self::calculateAddonsTotal($get);
        $set('total_price', $villaPrice + $addonsTotal);
    }

    /**
     * Parse money formatted string (e.g. "1.500.000") to integer.
     */
    private static function parseMoney(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        // Always strip thousand separators (.) and decimal separators (,) first
        // PHP's is_numeric("900.000") returns true and reads as 900.0, which is wrong
        $cleaned = str_replace(['.', ','], '', (string) $value);
        return (int) $cleaned;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // === Section 1: Tanggal Menginap ===
                Section::make('Tanggal Menginap')
                    ->description('Periode check-in dan check-out')
                    ->icon('heroicon-o-calendar-days')
                    ->collapsible()
                    ->collapsed()
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Reservasi')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null)
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Reservasi')
                            ->modalDescription('Apakah Anda yakin ingin menghapus reservasi ini? Data dapat dikembalikan nanti.')
                            ->modalSubmitActionLabel('Ya, Hapus')
                            ->action(function (Reservation $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Reservasi Berhasil Dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(ReservationResource::getUrl('index'));
                            }),
                    ])
                    ->schema([
                        DatePicker::make('check_in_date')
                            ->label('Tanggal Check-in')
                            ->required()
                            ->native(false)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                self::recalculateTotalPrice($set, $get);
                            }),
                        DatePicker::make('check_out_date')
                            ->label('Tanggal Check-out')
                            ->required()
                            ->native(false)
                            ->minDate(function (Get $get) {
                                $checkIn = $get('check_in_date');
                                if ($checkIn) {
                                    return Carbon::parse($checkIn)->addDay();
                                }
                                return now()->addDay();
                            })
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotalPrice($set, $get)),
                        TextEntry::make('duration_info')
                            ->label('Durasi Menginap')
                            ->columnSpanFull()
                            ->state(function (Get $get) {
                                $checkIn = $get('check_in_date');
                                $checkOut = $get('check_out_date');
                                if (!$checkIn || !$checkOut) {
                                    return '-';
                                }
                                $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
                                return "{$nights} malam";
                            }),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // === Section 2: Villa & Unit (Readonly) ===
                Section::make('Villa & Unit')
                    ->description('Informasi villa yang dipesan (tidak dapat diubah)')
                    ->icon('heroicon-o-home-modern')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('villa_name_display')
                            ->label('Tipe Villa')
                            ->state(fn(Reservation $record): string => $record->villaUnit->villa->name ?? '-'),
                        TextEntry::make('unit_name_display')
                            ->label('Unit Villa')
                            ->state(fn(Reservation $record): string => $record->villaUnit?->unit_name ?? '-'),
                        // Hidden fields to keep villa_id dan villa_unit_id saat save
                        Hidden::make('villa_id')
                            ->dehydrated(),
                        Hidden::make('villa_unit_id'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // === Section 3: Data Tamu ===
                Section::make('Data Tamu')
                    ->description('Informasi tamu / penyewa')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextInput::make('guest_name')
                            ->label('Nama Tamu')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('guest_phone')
                            ->label('No. Telepon / WhatsApp')
                            ->required()
                            ->tel()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
                            ]),
                        TextInput::make('guest_email')
                            ->label('Email (Opsional)')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('total_guests')
                            ->label('Jumlah Tamu')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->rule(function (Get $get) {
                                $villa = Villa::find($get('villa_id'));
                                $maxCapacity = $villa ? $villa->capacity : 999;
                                return 'max:' . $maxCapacity;
                            })
                            ->validationMessages([
                                'max' => 'Kapasitas maksimal Villa ini adalah :max tamu.',
                            ]),
                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                // === Section 4: Add-ons ===
                Section::make('Tambahan (Add-ons)')
                    ->description('Kelola layanan tambahan untuk reservasi ini')
                    ->icon('heroicon-o-puzzle-piece')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Repeater::make('addonOrders')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Select::make('addon_id')
                                    ->label('Add-on')
                                    ->options(Addon::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $addon = Addon::find($get('addon_id'));
                                        if ($addon) {
                                            $set('unit_price', $addon->price);
                                        }
                                    })
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(),

                                TextInput::make('nights')
                                    ->label('Malam')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->visible(function (Get $get) {
                                        $addon = Addon::find($get('addon_id'));
                                        return $addon && in_array($addon->pricing_unit, ['per_night', 'per_person_per_night']);
                                    }),

                                TextInput::make('persons')
                                    ->label('Orang')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live()
                                    ->visible(function (Get $get) {
                                        $addon = Addon::find($get('addon_id'));
                                        return $addon && in_array($addon->pricing_unit, ['per_person', 'per_person_per_night']);
                                    }),

                                Hidden::make('unit_price')
                                    ->default(0),

                                Hidden::make('subtotal')
                                    ->default(0),

                                TextEntry::make('addon_subtotal_display')
                                    ->label('Subtotal')
                                    ->state(function (Get $get) {
                                        $addonId = $get('addon_id');
                                        if (!$addonId)
                                            return 'Rp 0';

                                        $addon = Addon::find($addonId);
                                        if (!$addon)
                                            return 'Rp 0';

                                        $qty = (int) ($get('quantity') ?? 1);
                                        $nights = (int) ($get('nights') ?? 1);
                                        $persons = (int) ($get('persons') ?? 1);

                                        $subtotal = VillaAddonOrder::calculateSubtotal($addon, $qty, $nights, $persons);
                                        return Number::rupiah($subtotal);
                                    }),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'delivered' => 'Dikirim',
                                        'paid' => 'Dibayar',
                                        'cancelled' => 'Batal',
                                    ])
                                    ->default('pending'),

                                TextInput::make('notes')
                                    ->label('Catatan')
                                    ->maxLength(255),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->addActionLabel('+ Tambah Add-on')
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotalPrice($set, $get))
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                $addon = Addon::find($data['addon_id'] ?? null);
                                if ($addon) {
                                    $data['unit_price'] = $addon->price;
                                    $data['subtotal'] = VillaAddonOrder::calculateSubtotal(
                                        $addon,
                                        (int) ($data['quantity'] ?? 1),
                                        (int) ($data['nights'] ?? 1),
                                        (int) ($data['persons'] ?? 1),
                                    );
                                }
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                $addon = Addon::find($data['addon_id'] ?? null);
                                if ($addon) {
                                    $data['unit_price'] = $addon->price;
                                    $data['subtotal'] = VillaAddonOrder::calculateSubtotal(
                                        $addon,
                                        (int) ($data['quantity'] ?? 1),
                                        (int) ($data['nights'] ?? 1),
                                        (int) ($data['persons'] ?? 1),
                                    );
                                }
                                return $data;
                            }),
                    ])
                    ->columnSpanFull(),

                // === Section 5: Rincian Harga (Breakdown) ===
                Section::make('Rincian Harga')
                    ->description('Detail perhitungan total harga reservasi')
                    ->icon('heroicon-o-calculator')
                    ->collapsible()
                    ->schema([
                        // Villa Price Breakdown
                        TextEntry::make('villa_price_breakdown')
                            ->label('')
                            ->columnSpanFull()
                            ->state(function (Get $get) {
                                $villa = self::resolveVilla($get);
                                $dates = self::getDateBreakdown($get);

                                if (!$villa || $dates['nights'] <= 0) {
                                    return new HtmlString(
                                        '<div class="p-3 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg">' .
                                        'Pilih tanggal check-in dan check-out untuk melihat rincian harga villa.' .
                                        '</div>'
                                    );
                                }

                                $weekdayTotal = $dates['weekdays'] * $villa->base_price_weekday;
                                $weekendTotal = $dates['weekends'] * $villa->base_price_weekend;
                                $villaPrice = $weekdayTotal + $weekendTotal;

                                $html = '<div class="space-y-3">';

                                // Header
                                $html .= '<div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">';
                                $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>';
                                $html .= '<span>Harga Villa — ' . e($villa->name) . '</span>';
                                $html .= '</div>';

                                // Detail rows
                                $html .= '<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2 text-sm">';

                                if ($dates['weekdays'] > 0) {
                                    $html .= '<div class="flex justify-between items-center">';
                                    $html .= '<span class="text-gray-600 dark:text-gray-400">Weekday (' . $dates['weekdays'] . ' malam × ' . Number::rupiah($villa->base_price_weekday) . ')</span>';
                                    $html .= '<span class="font-medium text-gray-800 dark:text-gray-200">' . Number::rupiah($weekdayTotal) . '</span>';
                                    $html .= '</div>';
                                }

                                if ($dates['weekends'] > 0) {
                                    $html .= '<div class="flex justify-between items-center">';
                                    $html .= '<span class="text-gray-600 dark:text-gray-400">Weekend (' . $dates['weekends'] . ' malam × ' . Number::rupiah($villa->base_price_weekend) . ')</span>';
                                    $html .= '<span class="font-medium text-gray-800 dark:text-gray-200">' . Number::rupiah($weekendTotal) . '</span>';
                                    $html .= '</div>';
                                }

                                $html .= '<div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between items-center">';
                                $html .= '<span class="font-semibold text-gray-700 dark:text-gray-300">Subtotal Villa (' . $dates['nights'] . ' malam)</span>';
                                $html .= '<span class="font-bold text-primary-600 dark:text-primary-400">' . Number::rupiah($villaPrice) . '</span>';
                                $html .= '</div>';

                                $html .= '</div></div>';

                                return new HtmlString($html);
                            }),

                        // Add-ons Breakdown
                        TextEntry::make('addons_price_breakdown')
                            ->label('')
                            ->columnSpanFull()
                            ->state(function (Get $get) {
                                $items = self::getAddonsBreakdown($get);

                                if (empty($items)) {
                                    return new HtmlString(
                                        '<div class="p-3 text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-lg">' .
                                        'Belum ada add-on yang ditambahkan.' .
                                        '</div>'
                                    );
                                }

                                $addonsTotal = 0;
                                $html = '<div class="space-y-3">';

                                // Header
                                $html .= '<div class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-200">';
                                $html .= '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-primary-500"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.959.401v0a.656.656 0 0 0 .658-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z"/></svg>';
                                $html .= '<span>Tambahan (Add-ons)</span>';
                                $html .= '</div>';

                                // Items
                                $html .= '<div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 space-y-2 text-sm">';

                                foreach ($items as $item) {
                                    $addonsTotal += $item['subtotal'];
                                    $html .= '<div class="flex flex-col sm:flex-row sm:justify-between gap-0.5">';
                                    $html .= '<span class="text-gray-600 dark:text-gray-400">' . e($item['name']) . ' <span class="text-xs text-gray-400 dark:text-gray-500">(' . $item['detail'] . ')</span></span>';
                                    $html .= '<span class="font-medium text-gray-800 dark:text-gray-200 sm:text-right">' . Number::rupiah($item['subtotal']) . '</span>';
                                    $html .= '</div>';
                                }

                                $html .= '<div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between items-center">';
                                $html .= '<span class="font-semibold text-gray-700 dark:text-gray-300">Subtotal Add-ons</span>';
                                $html .= '<span class="font-bold text-primary-600 dark:text-primary-400">' . Number::rupiah($addonsTotal) . '</span>';
                                $html .= '</div>';

                                $html .= '</div></div>';

                                return new HtmlString($html);
                            }),

                        // Grand Total
                        TextEntry::make('grand_total_display')
                            ->label('')
                            ->columnSpanFull()
                            ->state(function (Get $get) {
                                $villaPrice = self::calculateVillaPrice($get);
                                $addonsTotal = self::calculateAddonsTotal($get);
                                $calculated = $villaPrice + $addonsTotal;

                                // Use the form field value if admin has manually edited it
                                $formTotalPrice = self::parseMoney($get('total_price'));
                                $grandTotal = $formTotalPrice > 0 ? $formTotalPrice : $calculated;

                                $html = '<div class="bg-primary-50 dark:bg-primary-500/10 border border-primary-200 dark:border-primary-500/20 rounded-lg p-4">';
                                $html .= '<div class="flex justify-between items-center">';
                                $html .= '<span class="text-base font-bold text-primary-700 dark:text-primary-300">Grand Total</span>';
                                $html .= '<span class="text-lg font-bold text-primary-700 dark:text-primary-300">' . Number::rupiah($grandTotal) . '</span>';
                                $html .= '</div>';
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                    ])
                    ->columnSpanFull(),

                // === Section 6: Pembayaran & Status ===
                Section::make('Pembayaran & Status')
                    ->description('Kelola pembayaran dan status reservasi')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Total Harga (Rp)')
                            ->required()
                            ->prefix('Rp')
                            ->live()
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state))),

                        TextInput::make('dp_amount')
                            ->label('Jumlah DP (Rp)')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state)))
                            ->live(onBlur: true)
                            ->suffixAction(
                                Action::make('dpHelper')
                                    ->label('Atur DP')
                                    ->icon('heroicon-s-bolt')
                                    ->color('primary')
                                    ->modalHeading('Atur Jumlah Down Payment')
                                    ->modalDescription('Masukkan persentase DP dari total harga, atau gunakan shortcut di bawah.')
                                    ->modalSubmitActionLabel('Terapkan')
                                    ->fillForm(fn(Get $get) => [
                                        'dp_percent' => 30,
                                        'current_total' => self::parseMoney($get('total_price')),
                                    ])
                                    ->form([
                                        Hidden::make('current_total'),
                                        TextInput::make('dp_percent')
                                            ->label('Persentase DP (%)')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(100)
                                            ->suffix('%')
                                            ->default(30)
                                            ->live(),
                                        TextEntry::make('dp_preview')
                                            ->label('Perkiraan DP')
                                            ->state(function (Get $get) {
                                                $percent = (int) ($get('dp_percent') ?? 0);
                                                $totalRaw = (int) ($get('current_total') ?? 0);
                                                $dpValue = (int) round($totalRaw * $percent / 100);
                                                return new HtmlString(
                                                    '<span class="text-lg font-bold text-primary-600">' . Number::rupiah($dpValue) . '</span>' .
                                                    '<span class="text-xs text-gray-500 ml-2">(' . $percent . '% dari ' . Number::rupiah($totalRaw) . ')</span>'
                                                );
                                            }),
                                    ])
                                    ->action(function (Set $set, array $data) {
                                        $percent = (int) ($data['dp_percent'] ?? 0);
                                        $totalRaw = (int) ($data['current_total'] ?? 0);
                                        $set('dp_amount', (int) round($totalRaw * $percent / 100));
                                    })
                            ),

                        TextInput::make('paid_amount')
                            ->label('Sudah Dibayar (Rp)')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state)))
                            ->suffixAction(
                                Action::make('paidHelper')
                                    ->label('Atur Pembayaran')
                                    ->icon('heroicon-s-bolt')
                                    ->color('primary')
                                    ->modalHeading('Atur Jumlah Pembayaran')
                                    ->modalDescription('Pilih jumlah pembayaran yang ingin diterapkan.')
                                    ->modalSubmitActionLabel('Terapkan')
                                    ->fillForm(fn(Get $get) => [
                                        'paid_dp_value' => self::parseMoney($get('dp_amount')),
                                        'paid_total_value' => self::parseMoney($get('total_price')),
                                    ])
                                    ->form([
                                        Hidden::make('paid_dp_value'),
                                        Hidden::make('paid_total_value'),
                                        Select::make('paid_preset')
                                            ->label('Jumlah Pembayaran')
                                            ->options(function (Get $get) {
                                                $dp = (int) ($get('paid_dp_value') ?? 0);
                                                $total = (int) ($get('paid_total_value') ?? 0);
                                                return [
                                                    'dp' => 'Sesuai DP — ' . Number::rupiah($dp),
                                                    'lunas' => 'Lunas — ' . Number::rupiah($total),
                                                ];
                                            })
                                            ->native(false)
                                            ->required(),
                                    ])
                                    ->action(function (Set $set, array $data) {
                                        if ($data['paid_preset'] === 'dp') {
                                            $set('paid_amount', (int) ($data['paid_dp_value'] ?? 0));
                                        } else {
                                            $set('paid_amount', (int) ($data['paid_total_value'] ?? 0));
                                        }
                                    })
                            ),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai / Cash',
                                'transfer' => 'Transfer Bank',
                                'qris' => 'QRIS',
                            ])
                            ->required(),

                        Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'unpaid' => 'Belum Bayar',
                                'dp_paid' => 'DP Dibayar',
                                'paid' => 'Lunas',
                                'refunded' => 'Refund',
                            ])
                            ->required(),

                        Select::make('status')
                            ->label('Status Reservasi')
                            ->options([
                                'pending' => 'Pending',
                                'booked' => 'Booked (Terkonfirmasi)',
                                'checked_in' => 'Checked In',
                                'checked_out' => 'Checked Out',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->footer([
                        Action::make('save')
                            ->label('Simpan Perubahan')
                            ->color('primary')
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $livewire->save();

                                Notification::make()
                                    ->title('Reservasi Berhasil Diperbarui')
                                    ->success()
                                    ->send();
                            }),
                        Action::make('back')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(ReservationResource::getUrl('index'));
                            }),
                    ]),

                // Hidden fields preserved from create
                Hidden::make('source'),
                Hidden::make('booking_code'),
            ]);
    }
}
