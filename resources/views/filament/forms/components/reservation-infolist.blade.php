<div class="fi-section rounded-xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4 overflow-hidden">
    {{-- Header --}}
    <div class="px-4 py-3 border-b border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 flex items-center gap-2">
        <h3 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white">
            Ringkasan Reservasi
        </h3>
    </div>

    {{-- Content --}}
    <div class="p-4 space-y-4 text-sm">

        {{-- Guest Info (paling atas) --}}
        @if(isset($guestName) && $guestName)
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Data Tamu</p>
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2 mb-3">
                        <x-heroicon-o-user class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                        <span class="font-bold text-gray-950 dark:text-white">{{ $guestName }}</span>
                    </div>
                    @if(isset($guestPhone) && $guestPhone)
                        <div class="flex items-center gap-2 mb-3">
                            <x-heroicon-o-phone class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-300">{{ $guestPhone }}</span>
                        </div>
                    @endif
                    @if(isset($guestEmail) && $guestEmail)
                        <div class="flex items-center gap-2 mb-3">
                            <x-heroicon-o-envelope class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-300">{{ $guestEmail }}</span>
                        </div>
                    @endif
                    @if(isset($totalGuests) && $totalGuests)
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-3.5 h-3.5 text-gray-400 shrink-0" />
                            <span class="text-gray-600 dark:text-gray-300">{{ $totalGuests }} Tamu</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Dates & Duration --}}
        @if($checkIn && $checkOut && $nights > 0)
            <hr class="border-gray-100 dark:border-white/5">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Tanggal & Durasi</p>
                <div class="flex items-center gap-2 mb-2">
                    <span class="font-bold text-gray-950 dark:text-white">{{ $nights }} Malam</span>
                    <span class="text-xs text-gray-400">({{ $weekdays }} Weekday, {{ $weekends }} Weekend)</span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 ml-6">
                    {{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }} →
                    {{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }}
                </p>
            </div>
        @endif

        {{-- Villa Info --}}
        @if($villa)
            <hr class="border-gray-100 dark:border-white/5">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Villa</p>
                <div class="flex gap-3 items-start">
                    @if($villa->hasMedia('cover_image'))
                        <img src="{{ $villa->getFirstMediaUrl('cover_image', 'thumb') ?: $villa->getFirstMediaUrl('cover_image') }}"
                            alt="{{ $villa->name }}"
                            class="h-14 w-14 object-cover rounded-lg ring-1 ring-gray-950/10 shrink-0">
                    @else
                        <div class="h-14 w-14 flex items-center justify-center bg-gray-100 dark:bg-white/5 rounded-lg border border-dashed border-gray-300 dark:border-white/20 shrink-0">
                            <x-heroicon-o-home class="w-6 h-6 text-gray-400" />
                        </div>
                    @endif
                    <div class="flex-1 min-w-0 mt-1">
                        <h4 class="font-bold text-gray-950 dark:text-white truncate">{{ $villa->name }}</h4>
                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1"><x-heroicon-o-users class="w-3.5 h-3.5" /> {{ $villa->capacity }} Tamu</span>
                            <span class="inline-flex items-center gap-1"><x-heroicon-o-moon class="w-3.5 h-3.5" /> {{ $villa->bedroom_count }} Kamar</span>
                            <span class="inline-flex items-center gap-1"><x-heroicon-o-sparkles class="w-3.5 h-3.5" /> {{ $villa->bathroom_count }} KM</span>
                        </div>
                        @if($unit)
                            <p class="mt-2 text-xs font-semibold text-primary-600 dark:text-primary-400">
                                ✓ Unit: {{ $unit->unit_name }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Price Breakdown --}}
        @if(isset($villaPrice) && $villaPrice > 0)
            <hr class="border-gray-100 dark:border-white/5">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Rincian Harga Villa</p>
                @if($weekdays > 0)
                    <div class="text-xs flex justify-between py-0.5">
                        <span class="text-gray-600 dark:text-gray-300">{{ $weekdays }} × Weekday</span>
                        <span class="font-medium text-gray-950 dark:text-white">Rp {{ number_format($weekdays * $villa->base_price_weekday, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($weekends > 0)
                    <div class="text-xs flex justify-between py-0.5">
                        <span class="text-gray-600 dark:text-gray-300">{{ $weekends }} × Weekend</span>
                        <span class="font-medium text-gray-950 dark:text-white">Rp {{ number_format($weekends * $villa->base_price_weekend, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold mt-2 pt-2 border-t border-gray-200 dark:border-white/10">
                    <span class="text-gray-950 dark:text-white">Subtotal Villa</span>
                    <span class="text-gray-700 dark:text-gray-300">Rp {{ number_format($villaPrice, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        {{-- Add-ons Section --}}
        @if(isset($addonsDisplay) && count($addonsDisplay) > 0)
            <hr class="border-gray-100 dark:border-white/5">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Tambahan (Add-ons)</p>
                @foreach($addonsDisplay as $addon)
                    <div class="text-xs flex justify-between py-0.5">
                        <span class="text-gray-600 dark:text-gray-300">
                            {{ $addon['name'] }}
                            <span class="text-gray-400">
                                @if($addon['pricing_unit'] === 'flat')
                                    × {{ $addon['qty'] }}
                                @elseif($addon['pricing_unit'] === 'per_night')
                                    × {{ $addon['qty'] }} × {{ $addon['nights'] }} mlm
                                @elseif($addon['pricing_unit'] === 'per_person')
                                    × {{ $addon['persons'] }} org × {{ $addon['qty'] }}
                                @elseif($addon['pricing_unit'] === 'per_person_per_night')
                                    × {{ $addon['persons'] }} org × {{ $addon['nights'] }} mlm
                                @endif
                            </span>
                        </span>
                        <span class="font-medium text-gray-950 dark:text-white">Rp {{ number_format($addon['subtotal'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="flex justify-between font-bold mt-2 pt-2 border-t border-gray-200 dark:border-white/10">
                    <span class="text-gray-950 dark:text-white">Subtotal Add-ons</span>
                    <span class="text-gray-700 dark:text-gray-300">Rp {{ number_format($addonsTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        {{-- Grand Total --}}
        @if(isset($totalPrice) && $totalPrice > 0)
            <hr class="border-gray-100 dark:border-white/5">
            <div class="flex justify-between font-bold text-base pt-1">
                <span class="text-gray-950 dark:text-white">Total Keseluruhan</span>
                <span class="text-primary-600 dark:text-primary-400">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
            </div>
        @endif

        {{-- Payment Info — hanya tampil saat ada data DP/payment yang diisi --}}
        @if((isset($dpAmount) && (int) $dpAmount > 0) || (isset($paidAmount) && (int) $paidAmount > 0) || (isset($paymentMethod) && $paymentMethod))
            <hr class="border-gray-100 dark:border-white/5">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Pembayaran</p>
                @if(isset($paymentMethod) && $paymentMethod)
                    <div class="text-xs flex justify-between py-0.5">
                        <span class="text-gray-600 dark:text-gray-300">Metode</span>
                        <span class="font-medium text-gray-950 dark:text-white">
                            @switch($paymentMethod)
                                @case('cash') Tunai / Cash @break
                                @case('transfer') Transfer Bank @break
                                @case('qris') QRIS @break
                                @default {{ $paymentMethod }}
                            @endswitch
                        </span>
                    </div>
                @endif
                @if(isset($dpAmount) && (int) $dpAmount > 0)
                    <div class="text-xs flex justify-between py-0.5 mt-2">
                        <span class="text-gray-600 dark:text-gray-300">Jumlah DP</span>
                        <span class="font-medium text-gray-950 dark:text-white">Rp {{ number_format((int) $dpAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if(isset($paidAmount) && (int) $paidAmount > 0)
                    <div class="text-xs flex justify-between py-0.5 mt-2">
                        <span class="text-gray-600 dark:text-gray-300">Sudah Dibayar</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Rp {{ number_format((int) $paidAmount, 0, ',', '.') }}</span>
                    </div>
                @endif

                {{-- Payment Status Badge --}}
                @if(isset($paymentStatus) && $paymentStatus)
                    <div class="flex mt-2 justify-between py-0.5">
                        <span class="text-gray-950 dark:text-gray-300">Status Pembayaran</span>
                        <span @class([
                            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                            'bg-red-50 text-red-700 ring-1 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400' => $paymentStatus === 'unpaid',
                            'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-400' => $paymentStatus === 'dp_paid',
                            'bg-green-50 text-green-700 ring-1 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400' => $paymentStatus === 'paid',
                            'bg-gray-50 text-gray-700 ring-1 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400' => $paymentStatus === 'refunded',
                        ])>
                            @switch($paymentStatus)
                                @case('unpaid') Belum Bayar @break
                                @case('dp_paid') DP Dibayar @break
                                @case('paid') Lunas @break
                                @case('refunded') Refund @break
                                @default {{ $paymentStatus }}
                            @endswitch
                        </span>
                    </div>
                @endif

                {{-- Reservation Status Badge --}}
                @if(isset($status) && $status)
                    <div class="flex mt-2 justify-between py-0.5">
                        <span class="text-gray-950 dark:text-gray-300">Status Reservasi</span>
                        <span @class([
                            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                            'bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-400' => $status === 'pending',
                            'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400' => $status === 'booked',
                            'bg-green-50 text-green-700 ring-1 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400' => $status === 'checked_in' || $status === 'checked_out',
                            'bg-red-50 text-red-700 ring-1 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400' => $status === 'cancelled',
                        ])>
                            @switch($status)
                                @case('pending') Pending @break
                                @case('booked') Booked @break
                                @case('checked_in') Checked In @break
                                @case('checked_out') Checked Out @break
                                @case('cancelled') Dibatalkan @break
                                @default {{ $status }}
                            @endswitch
                        </span>
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>