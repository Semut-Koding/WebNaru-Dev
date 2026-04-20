<?php

namespace App\Filament\Resources\AttractionCounters\Schemas;

use App\Filament\Resources\AttractionCounters\AttractionCounterResource;
use App\Models\AttractionCounter;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class AttractionCounterForm
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

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Kunjungan Wahana')
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
                            ->modalHeading('Hapus Data Kunjungan Wahana')
                            ->modalDescription('Apakah Anda yakin ingin menghapus data kunjungan wahana ini?')
                            ->modalSubmitActionLabel('Ya, Hapus')
                            ->action(function (AttractionCounter $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Kunjungan Wahana berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(AttractionCounterResource::getUrl('index'));
                            }),
                        Action::make('reset')
                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->label('Reset Form')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'attraction_id' => session('last_attraction_id'),
                                    'count' => 0,
                                    'notes' => null,
                                    'date' => now()->toDateString(),
                                    'attraction_operator_id' => auth()->id(),
                                ]);
                            }),
                    ])
                    ->schema([
                        Select::make('attraction_id')
                            ->label('Wahana')
                            ->relationship('attraction', 'name', fn($query) => $query->where('status', 'active'))
                            ->preload()
                            ->native(false)
                            ->default(fn() => session('last_attraction_id'))
                            ->required(),
                        Hidden::make('date')
                            ->label('Tanggal Kunjungan')
                            ->default(now())
                            ->required(),
                        TextInput::make('count')
                            ->label('Jumlah Tiket Valid')
                            ->required()
                            ->minValue(1)
                            ->default(0)
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-2',
                                'style' => 'touch-action: manipulation; font-size: 1.1rem;',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\\d]/g)",
                            ])
                            ->prefix(self::counterMinus())
                            ->suffix(self::counterPlus()),
                        Toggle::make('has_notes')
                            ->label('Tambah Catatan')
                            ->dehydrated(false)
                            ->live()
                            ->default(false),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(1)
                            ->visible(fn (Get $get) => $get('has_notes'))
                            ->columnSpanFull(),
                        Hidden::make('attraction_operator_id')
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

                                // Validasi Minimal 1 Tiket — konsisten dengan VisitorCounter
                                if (($data['count'] ?? 0) < 1) {
                                    Notification::make()
                                        ->title('Data Tidak Valid')
                                        ->body('Minimal harus ada 1 tiket tervalidasi.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                    return;
                                }
                                AttractionCounter::create($data);
                                session(['last_attraction_id' => $data['attraction_id']]);
                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Kunjungan Wahana berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(AttractionCounterResource::getUrl('create'));
                            }),
                        Action::make('update')
                            ->label(self::isWithinOperationalHours() ? 'Perbarui' : '🔒 Di Luar Jam Operasional')
                            ->color(self::isWithinOperationalHours() ? 'primary' : 'gray')
                            ->disabled(fn () => !self::isWithinOperationalHours())
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
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

                                // Validasi Minimal 1 Tiket — konsisten dengan VisitorCounter
                                if (($data['count'] ?? 0) < 1) {
                                    Notification::make()
                                        ->title('Data Tidak Valid')
                                        ->body('Minimal harus ada 1 tiket tervalidasi.')
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                    return;
                                }

                                $data['id'] = $livewire->record->id;
                                AttractionCounter::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Kunjungan Wahana berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(AttractionCounterResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(AttractionCounterResource::getUrl('index'));
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
