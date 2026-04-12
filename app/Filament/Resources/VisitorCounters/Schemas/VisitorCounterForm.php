<?php

namespace App\Filament\Resources\VisitorCounters\Schemas;

use App\Filament\Resources\VisitorCounters\VisitorCounterResource;
use App\Models\VisitorCounter;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class VisitorCounterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengunjung')
                    ->columnSpanFull()
                    ->description('Masukkan data pengunjung')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null)
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (VisitorCounter $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Pengunjung berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(VisitorCounterResource::getUrl('index'));
                            }),
                        Action::make('reset')

                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'date' => null,
                                    'adult_count' => 1,
                                    'teenager_count' => 1,
                                    'child_count' => 1,
                                    'is_group' => null,
                                    'notes' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        TextEntry::make('operational_hours_info')
                            ->label('')
                            ->state(function () {
                                $isWeekend = Carbon::now()->isWeekend();
                                $openKey = $isWeekend ? 'operational_hour_weekend_open' : 'operational_hour_weekday_open';
                                $closeKey = $isWeekend ? 'operational_hour_weekend_close' : 'operational_hour_weekday_close';
                                $open = Setting::where('key', $openKey)->value('value') ?? '08:00';
                                $close = Setting::where('key', $closeKey)->value('value') ?? '17:00';
                                $dayType = $isWeekend ? 'Weekend' : 'Weekday';
                                $now = Carbon::now();
                                $isOpen = $now->between(
                                    Carbon::today()->setTimeFromTimeString($open),
                                    Carbon::today()->setTimeFromTimeString($close)
                                );

                                if ($isOpen) {
                                    return new HtmlString(
                                        '<div class="flex items-center gap-2 p-3 text-sm text-emerald-700 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-500/10 rounded-lg border border-emerald-200 dark:border-emerald-500/20">' .
                                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>' .
                                        '<span>Jam operasional hari ini (' . $dayType . '): <strong>' . $open . ' - ' . $close . '</strong> — <strong class="text-emerald-600 dark:text-emerald-300">BUKA</strong></span>' .
                                        '</div>'
                                    );
                                } else {
                                    return new HtmlString(
                                        '<div class="flex items-center gap-2 p-3 text-sm text-red-700 bg-red-50 dark:text-red-400 dark:bg-red-500/10 rounded-lg border border-red-200 dark:border-red-500/20">' .
                                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>' .
                                        '<span>Jam operasional hari ini (' . $dayType . '): <strong>' . $open . ' - ' . $close . '</strong> — <strong class="text-red-600 dark:text-red-300">TUTUP</strong>. Pencatatan tidak dapat dilakukan di luar jam operasional.</span>' .
                                        '</div>'
                                    );
                                }
                            }),

                        Hidden::make('date')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),

                        TextInput::make('adult_count')
                            ->label('Jumlah Dewasa')
                            ->hint('18-59 Tahun')
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
                            ])
                            ->prefix(new HtmlString('
                                <button type="button"
                                    onmousedown="event.preventDefault()"
                                    ontouchend="
                                        event.preventDefault();
                                        event.stopPropagation();
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    onclick="
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    class="inline-flex items-center justify-center rounded-lg p-4 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path fill-rule="evenodd" d="M4.25 12a.75.75 0 0 1 .75-.75h14a.75.75 0 0 1 0 1.5H5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            '))
                            ->suffix(new HtmlString('
                            <button type="button"
                                onmousedown="event.preventDefault()"
                                ontouchend="
                                    event.preventDefault();
                                    event.stopPropagation();
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                onclick="
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                class="inline-flex items-center justify-center rounded-lg p-4 text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        ')),
                        TextInput::make('teenager_count')
                            ->label('Jumlah Remaja')
                            ->hint('12-17 Tahun')
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
                            ])
                            ->prefix(new HtmlString('
                                <button type="button"
                                    onmousedown="event.preventDefault()"
                                    ontouchend="
                                        event.preventDefault();
                                        event.stopPropagation();
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    onclick="
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    class="inline-flex items-center justify-center rounded-lg p-4 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path fill-rule="evenodd" d="M4.25 12a.75.75 0 0 1 .75-.75h14a.75.75 0 0 1 0 1.5H5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            '))
                            ->suffix(new HtmlString('
                            <button type="button"
                                onmousedown="event.preventDefault()"
                                ontouchend="
                                    event.preventDefault();
                                    event.stopPropagation();
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                onclick="
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                class="inline-flex items-center justify-center rounded-lg p-4 text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        ')),
                        TextInput::make('child_count')
                            ->label('Jumlah Anak')
                            ->hint('3-11 Tahun')
                            ->required()
                            ->minValue(1)
                            ->default(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
                            ])
                            ->prefix(new HtmlString('
                                <button type="button"
                                    onmousedown="event.preventDefault()"
                                    ontouchend="
                                        event.preventDefault();
                                        event.stopPropagation();
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    onclick="
                                        let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                        let current = parseInt(input.value) || 0;
                                        let newVal = Math.max(0, current - 1);
                                        input.value = newVal;
                                        input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                    "
                                    class="inline-flex items-center justify-center rounded-lg p-4 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                        <path fill-rule="evenodd" d="M4.25 12a.75.75 0 0 1 .75-.75h14a.75.75 0 0 1 0 1.5H5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            '))
                            ->suffix(new HtmlString('
                            <button type="button"
                                onmousedown="event.preventDefault()"
                                ontouchend="
                                    event.preventDefault();
                                    event.stopPropagation();
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                onclick="
                                    let input = this.closest(\'.fi-input-wrp\').querySelector(\'input\');
                                    let current = parseInt(input.value) || 0;
                                    input.value = current + 1;
                                    input.dispatchEvent(new Event(\'input\', { bubbles: true }));
                                "
                                class="inline-flex items-center justify-center rounded-lg p-4 text-green-500 hover:bg-green-50 dark:hover:bg-green-500/10 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        ')),
                        Checkbox::make('is_group')
                            ->label('Rombongan')
                            ->hint('Lebih dari 20 orang')
                            ->inline(false)
                            ->extraInputAttributes(['class' => 'p-4']),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                        Hidden::make('cashier_id')
                            ->default(fn() => auth()->id())
                            ->required(),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label('Simpan & Tambah Lagi')
                            ->color('primary')
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->visible(fn($record) => $record === null)
                            ->action(function ($livewire) {
                                // Check operational hours
                                if (!self::isWithinOperationalHours()) {
                                    Notification::make()
                                        ->title('Di Luar Jam Operasional')
                                        ->body('Pencatatan hanya dapat dilakukan dalam jam operasional.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $data = $livewire->form->getState();
                                VisitorCounter::create($data);
                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Pengunjung berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(VisitorCounterResource::getUrl('create'));
                            }),
                        Action::make('update')
                            ->label('Perbarui')
                            ->color('primary')
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                // Check operational hours
                                if (!self::isWithinOperationalHours()) {
                                    Notification::make()
                                        ->title('Di Luar Jam Operasional')
                                        ->body('Pencatatan hanya dapat dilakukan dalam jam operasional.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $data = $livewire->form->getState();
                                $data['id'] = $livewire->record->id;
                                VisitorCounter::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Pengunjung berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(VisitorCounterResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(VisitorCounterResource::getUrl('index'));
                            }),
                    ])
            ]);
    }

    /**
     * Check if the current time is within operational hours.
     */
    private static function isWithinOperationalHours(): bool
    {
        $isWeekend = Carbon::now()->isWeekend();
        $openKey = $isWeekend ? 'operational_hour_weekend_open' : 'operational_hour_weekday_open';
        $closeKey = $isWeekend ? 'operational_hour_weekend_close' : 'operational_hour_weekday_close';
        $open = Setting::where('key', $openKey)->value('value') ?? '08:00';
        $close = Setting::where('key', $closeKey)->value('value') ?? '17:00';

        return Carbon::now()->between(
            Carbon::today()->setTimeFromTimeString($open),
            Carbon::today()->setTimeFromTimeString($close)
        );
    }
}
