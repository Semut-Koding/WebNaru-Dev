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
    /**
     * Render counter minus button from Blade component.
     */
    private static function counterMinus(int $min = 0): HtmlString
    {
        return new HtmlString(view('components.counter-minus', ['min' => $min])->render());
    }

    /**
     * Render counter plus button from Blade component.
     */
    private static function counterPlus(): HtmlString
    {
        return new HtmlString(view('components.counter-plus')->render());
    }

    /**
     * Build a counter TextInput field with +/- buttons.
     */
    private static function counterField(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->default(0)
            ->numeric()
            ->inputMode('numeric')
            ->extraInputAttributes([
                'class' => 'text-center p-2',
                'style' => 'touch-action: manipulation; font-size: 1.1rem;',
                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
            ])
            ->prefix(self::counterMinus())
            ->suffix(self::counterPlus());
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pengunjung')
                    ->columnSpanFull()
                    ->description(fn () => self::isWithinOperationalHours() ? null : '🔴 Di luar jam operasional')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null)
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Data Pengunjung')
                            ->modalDescription('Apakah Anda yakin ingin menghapus data pengunjung ini?')
                            ->modalSubmitActionLabel('Ya, Hapus')
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
                                    'adult_count' => 0,
                                    'teenager_count' => 0,
                                    'child_count' => 0,
                                    'is_group' => null,
                                    'notes' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        Hidden::make('date')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),

                        self::counterField('adult_count', 'Dewasa (18-59)'),
                        self::counterField('teenager_count', 'Remaja (12-17)'),
                        self::counterField('child_count', 'Anak (3-11)'),

                        Checkbox::make('is_group')
                            ->label('Rombongan (>20 orang)')
                            ->inline(false),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(1)
                            ->columnSpanFull(),
                        Hidden::make('cashier_id')
                            ->default(fn() => auth()->id())
                            ->required(),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label(self::isWithinOperationalHours() ? 'Simpan & Tambah Lagi' : '🔒 Di Luar Jam Operasional')
                            ->color(self::isWithinOperationalHours() ? 'primary' : 'gray')
                            ->disabled(fn () => !self::isWithinOperationalHours())
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record === null)
                            ->action(function ($livewire) {
                                // Validasi jam operasional
                                if (!self::isWithinOperationalHours()) {
                                    Notification::make()
                                        ->title('Di Luar Jam Operasional')
                                        ->body('Pencatatan hanya dapat dilakukan dalam jam operasional.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $data = $livewire->form->getState();

                                // Validasi minimal 1 pengunjung dari semua kategori
                                $totalVisitors = ($data['adult_count'] ?? 0)
                                    + ($data['teenager_count'] ?? 0)
                                    + ($data['child_count'] ?? 0);

                                if ($totalVisitors < 1) {
                                    Notification::make()
                                        ->title('Data Tidak Valid')
                                        ->body('Minimal harus ada 1 pengunjung. Isi setidaknya satu dari: Dewasa, Remaja, atau Anak-anak.')
                                        ->danger()
                                        ->persistent() // tidak auto-dismiss
                                        ->send();
                                    return; // hentikan eksekusi, tidak simpan
                                }

                                VisitorCounter::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Pengunjung berhasil ditambahkan')
                                    ->success()
                                    ->send();

                                return redirect()->to(VisitorCounterResource::getUrl('create'));
                            }),
                        Action::make('update')
                            ->label(self::isWithinOperationalHours() ? 'Perbarui' : '🔒 Di Luar Jam Operasional')
                            ->color(self::isWithinOperationalHours() ? 'primary' : 'gray')
                            ->disabled(fn () => !self::isWithinOperationalHours())
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                if (!self::isWithinOperationalHours()) {
                                    Notification::make()
                                        ->title('Di Luar Jam Operasional')
                                        ->body('Pencatatan hanya dapat dilakukan dalam jam operasional.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $data = $livewire->form->getState();

                                // Validasi minimal 1 pengunjung
                                $totalVisitors = ($data['adult_count'] ?? 0)
                                    + ($data['teenager_count'] ?? 0)
                                    + ($data['child_count'] ?? 0);

                                if ($totalVisitors < 1) {
                                    Notification::make()
                                        ->title('Data Tidak Valid')
                                        ->body('Minimal harus ada 1 pengunjung. Isi setidaknya satu dari: Dewasa, Remaja, atau Anak-anak.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                    return;
                                }

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
