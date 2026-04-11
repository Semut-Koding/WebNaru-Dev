<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Addon;
use App\Models\Reservation;
use App\Models\Villa;
use App\Models\VillaUnit;
use App\Models\VillaAddonOrder;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Number;

class ReservationForm
{
    /**
     * Calculate villa price only (without add-ons).
     */
    public static function calculateVillaPrice(Get $get): float
    {
        $checkIn = $get('check_in_date');
        $checkOut = $get('check_out_date');
        $villaId = $get('villa_id');

        if (!$checkIn || !$checkOut || !$villaId) {
            return 0;
        }

        try {
            $startDate = Carbon::parse($checkIn);
            $endDate = Carbon::parse($checkOut);
            $nights = $startDate->diffInDays($endDate);

            $weekdays = 0;
            $weekends = 0;

            for ($i = 0; $i < $nights; $i++) {
                $current = $startDate->copy()->addDays($i);
                if ($current->isWeekend()) {
                    $weekends++;
                } else {
                    $weekdays++;
                }
            }

            $villa = Villa::find($villaId);
            if ($villa) {
                return ($weekdays * $villa->base_price_weekday) + ($weekends * $villa->base_price_weekend);
            }
        } catch (\Exception $e) {
        }

        return 0;
    }

    /**
     * Calculate add-ons total from repeater data.
     */
    public static function calculateAddonsTotal(Get $get): float
    {
        $addonOrders = $get('addon_orders') ?? [];
        $total = 0;

        foreach ($addonOrders as $order) {
            $addonId = $order['addon_id'] ?? null;
            if (!$addonId)
                continue;

            $addon = Addon::find($addonId);
            if (!$addon)
                continue;

            $qty = (int) ($order['quantity'] ?? 1);
            $nights = (int) ($order['nights'] ?? 1);
            $persons = (int) ($order['persons'] ?? 1);

            $total += VillaAddonOrder::calculateSubtotal($addon, $qty, $nights, $persons);
        }

        return $total;
    }

    /**
     * Recalculate total price = villa + add-ons.
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

    private static function summaryPlaceholder(string $name): Placeholder
    {
        return Placeholder::make($name)
            ->label('')
            ->columnSpanFull()
            ->content(function (Get $get) {
                $checkIn = $get('check_in_date');
                $checkOut = $get('check_out_date');
                $villaId = $get('villa_id');
                $villaUnitId = $get('villa_unit_id');
                $dpAmount = $get('dp_amount');
                $paidAmount = $get('paid_amount');
                $paymentMethod = $get('payment_method');
                $status = $get('status');
                $paymentStatus = $get('payment_status');
                $guestName = $get('guest_name');
                $guestPhone = $get('guest_phone');
                $guestEmail = $get('guest_email');
                $totalGuests = $get('total_guests');
                $addonOrders = $get('addon_orders') ?? [];

                $nights = 0;
                $weekdays = 0;
                $weekends = 0;
                $villaPrice = 0;
                $villa = null;
                $unit = null;

                if ($checkIn && $checkOut) {
                    try {
                        $startDate = Carbon::parse($checkIn);
                        $endDate = Carbon::parse($checkOut);
                        $nights = $startDate->diffInDays($endDate);

                        for ($i = 0; $i < $nights; $i++) {
                            $current = $startDate->copy()->addDays($i);
                            if ($current->isWeekend()) {
                                $weekends++;
                            } else {
                                $weekdays++;
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }

                if ($villaId) {
                    $villa = Villa::with('media')->find($villaId);
                    if ($villa && $nights > 0) {
                        $villaPrice = ($weekdays * $villa->base_price_weekday) + ($weekends * $villa->base_price_weekend);
                    }
                }

                if ($villaUnitId) {
                    $unit = VillaUnit::find($villaUnitId);
                }

                // Process addon orders for display
                $addonsDisplay = [];
                $addonsTotal = 0;
                foreach ($addonOrders as $order) {
                    $addonId = $order['addon_id'] ?? null;
                    if (!$addonId)
                        continue;
                    $addon = Addon::find($addonId);
                    if (!$addon)
                        continue;

                    $qty = (int) ($order['quantity'] ?? 1);
                    $orderNights = (int) ($order['nights'] ?? 1);
                    $orderPersons = (int) ($order['persons'] ?? 1);
                    $subtotal = VillaAddonOrder::calculateSubtotal($addon, $qty, $orderNights, $orderPersons);
                    $addonsTotal += $subtotal;

                    $addonsDisplay[] = [
                        'name' => $addon->name,
                        'qty' => $qty,
                        'nights' => $orderNights,
                        'persons' => $orderPersons,
                        'pricing_unit' => $addon->pricing_unit,
                        'subtotal' => $subtotal,
                    ];
                }

                // Use the form field value if admin has manually edited it,
                // otherwise use calculated value
                $formTotalPrice = self::parseMoney($get('total_price'));
                $totalPrice = $formTotalPrice > 0 ? $formTotalPrice : ($villaPrice + $addonsTotal);

                return view('filament.forms.components.reservation-infolist', compact(
                    'checkIn',
                    'checkOut',
                    'nights',
                    'weekdays',
                    'weekends',
                    'villaPrice',
                    'totalPrice',
                    'villa',
                    'unit',
                    'dpAmount',
                    'paidAmount',
                    'paymentMethod',
                    'status',
                    'paymentStatus',
                    'guestName',
                    'guestPhone',
                    'guestEmail',
                    'totalGuests',
                    'addonsDisplay',
                    'addonsTotal'
                ));
            });
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    // ── Step 1: Tanggal ──
                    Step::make('Tanggal')
                        ->description('Pilih Tanggal Menginap')
                        ->completedIcon(Heroicon::HandThumbUp)
                        ->schema([
                            DatePicker::make('check_in_date')
                                ->label('Tanggal Check-in')
                                ->required()
                                ->native(false)
                                ->minDate(now()->startOfDay())
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('check_out_date', null);
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
                                ->disabled(fn(Get $get) => !$get('check_in_date'))
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotalPrice($set, $get)),
                        ])->columns(2),

                    // ── Step 2: Unit Villa ──
                    Step::make('Unit Villa')
                        ->description('Pilih Ketersediaan Unit')
                        ->schema([
                            Select::make('villa_id')
                                ->label('Tipe Villa')
                                ->options(Villa::where('status', 'available')->pluck('name', 'id'))
                                ->preload()
                                ->native(false)
                                ->required()
                                ->live()
                                ->afterStateUpdated(function (Set $set, Get $get) {
                                    $set('villa_unit_id', null);
                                    self::recalculateTotalPrice($set, $get);
                                }),

                            // Info ketersediaan unit villa
                            Placeholder::make('villa_availability_info')
                                ->label('')
                                ->columnSpanFull()
                                ->visible(fn(Get $get) => $get('villa_id') && $get('check_in_date') && $get('check_out_date'))
                                ->content(function (Get $get) {
                                    $villaId = $get('villa_id');
                                    $checkIn = $get('check_in_date');
                                    $checkOut = $get('check_out_date');

                                    // Get all eligible unit IDs for this villa
                                    $villaUnitIds = VillaUnit::where('villa_id', $villaId)
                                        ->where('is_active', true)
                                        ->where('status', '!=', 'maintenance')
                                        ->pluck('id')
                                        ->toArray();

                                    $bookedUnitIds = [];
                                    if ($checkIn && $checkOut && !empty($villaUnitIds)) {
                                        $bookedUnitIds = Reservation::whereIn('villa_unit_id', $villaUnitIds)
                                            ->whereIn('status', ['pending', 'booked', 'checked_in'])
                                            ->where(function ($q) use ($checkIn, $checkOut) {
                                                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                                    ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                                        $q2->where('check_in_date', '<=', $checkIn)
                                                            ->where('check_out_date', '>=', $checkOut);
                                                    });
                                            })
                                            ->pluck('villa_unit_id')
                                            ->unique()
                                            ->toArray();
                                    }

                                    $availableCount = count($villaUnitIds) - count($bookedUnitIds);

                                    if ($availableCount > 0) {
                                        return new HtmlString(
                                            '<div class="flex items-center gap-2 p-3 text-sm text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/10 rounded-lg border border-emerald-200 dark:border-emerald-500/20">' .
                                            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>' .
                                            '<span><strong>' . $availableCount . ' unit tersedia</strong> dari total ' . count($villaUnitIds) . ' unit untuk tanggal yang dipilih.</span>' .
                                            '</div>'
                                        );
                                    } else {
                                        return new HtmlString(
                                            '<div class="flex items-center gap-2 p-3 text-sm text-amber-700 bg-amber-50 dark:text-amber-400 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-500/20">' .
                                            '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>' .
                                            '<span><strong>Tidak ada unit tersedia</strong> untuk tanggal yang dipilih. Silakan pilih tanggal lain atau tipe villa lain.</span>' .
                                            '</div>'
                                        );
                                    }
                                }),

                            Select::make('villa_unit_id')
                                ->label('Pilih Unit Villa')
                                ->visible(function (Get $get) {
                                    $villaId = $get('villa_id');
                                    $checkIn = $get('check_in_date');
                                    $checkOut = $get('check_out_date');
                                    if (!$villaId || !$checkIn || !$checkOut)
                                        return false;

                                    $villaUnitIds = VillaUnit::where('villa_id', $villaId)
                                        ->where('is_active', true)
                                        ->where('status', '!=', 'maintenance')
                                        ->pluck('id')->toArray();

                                    if (empty($villaUnitIds))
                                        return false;

                                    $bookedUnitIds = Reservation::whereIn('villa_unit_id', $villaUnitIds)
                                        ->whereIn('status', ['pending', 'booked', 'checked_in'])
                                        ->where(function ($q) use ($checkIn, $checkOut) {
                                            $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                                ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                                ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                                    $q2->where('check_in_date', '<=', $checkIn)
                                                        ->where('check_out_date', '>=', $checkOut);
                                                });
                                        })
                                        ->pluck('villa_unit_id')->unique()->toArray();

                                    return (count($villaUnitIds) - count($bookedUnitIds)) > 0;
                                })
                                ->options(function (Get $get) {
                                    $villaId = $get('villa_id');
                                    $checkIn = $get('check_in_date');
                                    $checkOut = $get('check_out_date');

                                    if (!$villaId) {
                                        return [];
                                    }

                                    $query = VillaUnit::where('villa_id', $villaId)
                                        ->where('is_active', true)
                                        ->where('status', '!=', 'maintenance');

                                    if ($checkIn && $checkOut) {
                                        $bookedUnitIds = Reservation::whereIn('status', ['pending', 'booked', 'checked_in'])
                                            ->where(function ($q) use ($checkIn, $checkOut) {
                                                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                                    ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                                                        $q2->where('check_in_date', '<=', $checkIn)
                                                            ->where('check_out_date', '>=', $checkOut);
                                                    });
                                            })
                                            ->pluck('villa_unit_id')
                                            ->toArray();

                                        if (!empty($bookedUnitIds)) {
                                            $query->whereNotIn('id', $bookedUnitIds);
                                        }
                                    }

                                    return $query->pluck('unit_name', 'id');
                                })
                                ->required()
                                ->preload()
                                ->live()
                                ->native(false)
                                ->disabled(fn(Get $get) => !$get('villa_id') || !$get('check_in_date') || !$get('check_out_date'))
                                ->helperText(fn(Get $get) => (!$get('check_in_date') || !$get('check_out_date')) ? 'Pilih tanggal menginap terlebih dahulu.' : ''),
                        ])->columns(2),

                    // ── Step 3: Data Tamu ──
                    Step::make('Data Tamu')
                        ->description('Informasi Data Tamu / Penyewa')
                        ->schema([
                            TextInput::make('total_guests')
                                ->label('Jumlah Tamu')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->live(onBlur: true)
                                ->rule(function (Get $get) {
                                    $villa = Villa::find($get('villa_id'));
                                    $maxCapacity = $villa ? $villa->capacity : 999;
                                    return 'max:' . $maxCapacity;
                                })
                                ->validationMessages([
                                    'max' => 'Kapasitas maksimal Villa ini adalah :max tamu.',
                                ]),

                            TextInput::make('guest_name')
                                ->label('Nama Tamu')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true),
                            TextInput::make('guest_phone')
                                ->label('No. Telepon / WhatsApp')
                                ->required()
                                ->tel()
                                ->inputMode('numeric')
                                ->live(onBlur: true)
                                ->extraInputAttributes([
                                    'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                    'onpaste' => "return !event.clipboardData.getData('text').match(/[^\d]/g)",
                                ]),
                            TextInput::make('guest_email')
                                ->label('Email (Opsional)')
                                ->email()
                                ->maxLength(255),
                            Textarea::make('notes')
                                ->label('Catatan Tambahan')
                                ->columnSpanFull(),
                        ])->columns(2),

                    // ── Step 4: Add-ons ──
                    Step::make('Tambahan (Add-ons)')
                        ->description('Tambah layanan ekstra')
                        ->schema([
                            Repeater::make('addon_orders')
                                ->label('Pesanan Add-on')
                                ->schema([
                                    Select::make('addon_id')
                                        ->label('Pilih Add-on')
                                        ->options(Addon::where('is_active', true)->pluck('name', 'id'))
                                        ->preload()
                                        ->native(false)
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

                                    Placeholder::make('addon_subtotal')
                                        ->label('Subtotal')
                                        ->content(function (Get $get) {
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
                                ])
                                ->columns(4)
                                ->defaultItems(0)
                                ->reorderable(false)
                                ->addActionLabel('+ Tambah Add-on')
                                ->columnSpanFull()
                                ->live()
                                ->afterStateUpdated(fn(Set $set, Get $get) => self::recalculateTotalPrice($set, $get)),

                            Placeholder::make('addons_total_display')
                                ->label('')
                                ->content(function (Get $get) {
                                    $addonsTotal = self::calculateAddonsTotal($get);
                                    if ($addonsTotal <= 0)
                                        return '';
                                    return new HtmlString('<div class="text-right font-bold text-primary-600">Total Add-ons: ' . Number::rupiah($addonsTotal) . '</div>');
                                }),
                        ]),

                    // ── Step 5: Pembayaran ──
                    Step::make('Pembayaran')
                        ->description('Konfirmasi Harga & Pembayaran')
                        ->schema([
                            TextInput::make('total_price')
                                ->label('Total Harga Keseluruhan (Rp)')
                                ->required()
                                ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                                ->stripCharacters('.')
                                ->numeric()
                                ->prefix('Rp')
                                ->live(onBlur: true)
                                ->extraInputAttributes(['class' => 'font-bold text-lg text-primary-600']),

                            TextInput::make('dp_amount')
                                ->label('Jumlah DP (Rp)')
                                ->required()
                                ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                                ->stripCharacters('.')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
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
                                            Placeholder::make('dp_preview')
                                                ->label('Perkiraan DP')
                                                ->content(function (Get $get) {
                                                    $percent = (int) ($get('dp_percent') ?? 0);
                                                    $totalRaw = (int) ($get('current_total') ?? 0);
                                                    $dpValue = (int) round($totalRaw * $percent / 100);
                                                    return new HtmlString(
                                                        '<span class="text-lg font-bold text-primary-600">' . Number::rupiah($dpValue) . '</span>' .
                                                        '<span class="text-xs text-gray-500 ml-2">(' . $percent . '% dari ' . Number::rupiah($totalRaw) . ')</span>'
                                                    );
                                                }),
                                        ])
                                        ->action(function (Set $set, Get $get, array $data) {
                                            $percent = (int) ($data['dp_percent'] ?? 0);
                                            $totalRaw = (int) ($data['current_total'] ?? 0);
                                            $set('dp_amount', (int) round($totalRaw * $percent / 100));
                                        })
                                ),
                            TextInput::make('paid_amount')
                                ->label('Sudah Dibayar (Rp)')
                                ->required()
                                ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                                ->stripCharacters('.')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0)
                                ->live(onBlur: true)
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
                                ->native(false)
                                ->required()
                                ->live(),

                            Select::make('payment_status')
                                ->label('Status Pembayaran')
                                ->options([
                                    'unpaid' => 'Belum Bayar',
                                    'dp_paid' => 'DP Dibayar',
                                    'paid' => 'Lunas',
                                    'refunded' => 'Refund',
                                ])
                                ->native(false)
                                ->default('unpaid')
                                ->required()
                                ->live(),

                            Select::make('status')
                                ->label('Status Reservasi')
                                ->options([
                                    'pending' => 'Pending',
                                    'booked' => 'Booked (Terkonfirmasi)',
                                    'checked_in' => 'Checked In',
                                    'checked_out' => 'Checked Out',
                                    'cancelled' => 'Dibatalkan',
                                ])
                                ->native(false)
                                ->default('pending')
                                ->required()
                                ->live(),

                            Hidden::make('source')
                                ->default('walk_in'),
                            Hidden::make('booking_code')
                                ->default(fn() => 'RES-' . strtoupper(uniqid())),
                            Hidden::make('created_by')
                                ->default(fn() => auth()->id()),
                        ])->columns(2),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                        <x-filament::button
                            type="submit"
                            size="sm"
                            icon="heroicon-o-check-circle"
                        >
                            Simpan
                        </x-filament::button>
                    BLADE)))
                    ->columnSpanFull(),

                self::summaryPlaceholder('reservation_summary'),
            ]);
    }
}
