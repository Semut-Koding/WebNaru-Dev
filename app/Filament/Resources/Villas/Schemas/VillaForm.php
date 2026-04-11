<?php

namespace App\Filament\Resources\Villas\Schemas;

use App\Filament\Resources\Villas\VillaResource;
use App\Models\Villa;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Set;
use Filament\Support\Enums\IconSize;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class VillaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Villa')
                    ->columnSpanFull()
                    ->description('Masukkan data lengkap villa')
                    ->extraAttributes([
                        'x-data' => '{ uploading: false }',
                        'x-on:livewire-upload-start' => 'uploading = true',
                        'x-on:livewire-upload-finish' => 'uploading = false',
                        'x-on:livewire-upload-error' => 'uploading = false',
                    ])
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null && !$record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (Villa $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Villa berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                        Action::make('restore')
                            ->label('')
                            ->color('gray')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-arrow-uturn-left')
                            ->requiresConfirmation()
                            ->modalHeading('Kembalikan Data Villa')
                            ->modalDescription('Data villa ini akan dikembalikan dan dapat digunakan kembali.')
                            ->modalSubmitActionLabel('Ya, Kembalikan')
                            ->action(function (Villa $record) {
                                $record->restore();

                                Notification::make()
                                    ->title('Data Berhasil Dikembalikan')
                                    ->body('Data Villa berhasil dikembalikan')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                        Action::make('force_delete')
                            ->label('')
                            ->color('danger')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Permanen Data Villa')
                            ->modalDescription('Data villa ini akan dihapus secara permanen dan tidak dapat dikembalikan.')
                            ->modalSubmitActionLabel('Ya, Hapus Permanen')
                            ->action(function (Villa $record) {
                                $record->forceDelete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus Permanen')
                                    ->body('Data Villa telah dihapus secara permanen')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                        Action::make('reset')
                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'name' => null,
                                    'description' => null,
                                    'bedroom_count' => 1,
                                    'bathroom_count' => 1,
                                    'capacity' => 1,
                                    'base_price_weekday' => null,
                                    'base_price_weekend' => null,
                                    'status' => null,
                                    'amenities' => null,
                                    'benefits' => null,
                                    'coordinates' => null,
                                    'cover_image' => null,
                                    'gallery_images' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Villa')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(callable $set, ?string $state) => $set('slug', Str::slug($state))),
                        Hidden::make('slug')
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        TextInput::make('bedroom_count')
                            ->label('Jumlah Kamar Tidur')
                            ->hint('Misal: 2')
                            ->required()
                            ->minValue(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\d]/g)",
                            ])
                            ->default(1)
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
                        TextInput::make('bathroom_count')
                            ->label('Jumlah Kamar Mandi')
                            ->hint('Misal: 1')
                            ->required()
                            ->minValue(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\d]/g)",
                            ])
                            ->default(1)
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
                        TextInput::make('capacity')
                            ->label('Kapasitas Ruangan')
                            ->hint('Jumlah maksimal orang')
                            ->required()
                            ->minValue(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\d]/g)",
                            ])
                            ->default(1)
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
                        TextInput::make('base_price_weekday')
                            ->label('Harga Weekday')
                            ->hint('Harga reguler hari biasa')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->required()
                            ->default(0)
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state))),
                        TextInput::make('base_price_weekend')
                            ->label('Harga Weekend')
                            ->hint('Harga khusus akhir pekan')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->required()
                            ->default(0)
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state))),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'available' => 'Tersedia',
                                'coming_soon' => 'Segera Hadir',
                                'closed' => 'Ditutup'
                            ])
                            ->default('available')
                            ->required(),
                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->required()
                            ->unique()
                            ->minValue(1)
                            ->numeric()
                            ->inputMode('numeric')
                            ->extraInputAttributes([
                                'class' => 'text-center p-4',
                                'onkeypress' => 'return event.charCode >= 48 && event.charCode <= 57',
                                'onpaste' => "return !event.clipboardData.getData('text').match(/[^\d]/g)",
                            ])
                            ->default(Villa::withTrashed()->max('sort_order') + 1)
                            ->prefixAction(
                                Action::make('decrement')
                                    ->icon('heroicon-s-minus')
                                    ->color('danger')
                                    ->iconSize(IconSize::Large)
                                    ->extraAttributes(['class' => 'w-full sm:w-auto p-2'])
                                    ->action(function ($set, $state) {
                                        $set('sort_order', max(1, $state - 1));
                                    })
                            )
                            ->suffixAction(
                                Action::make('increment')
                                    ->icon('heroicon-s-plus')
                                    ->color('success')
                                    ->iconSize(IconSize::Large)
                                    ->extraAttributes(['class' => 'w-full sm:w-auto p-2'])
                                    ->action(function ($set, $state) {
                                        $set('sort_order', $state + 1);
                                    })
                            ),
                        Repeater::make('amenities')
                            ->label('Fasilitas (Amenities)')
                            ->simple(
                                TextInput::make('name')->required(),
                            )
                            ->addActionLabel('Tambah Fasilitas'),
                        Repeater::make('benefits')
                            ->label('Keuntungan (Benefits)')
                            ->simple(
                                TextInput::make('name')->required(),
                            )
                            ->addActionLabel('Tambah Keuntungan'),
                        TextInput::make('coordinates')
                            ->label('Koordinat Maps')
                            ->hidden()
                            ->hint('Format Latitude,Longitude'),
                        SpatieMediaLibraryFileUpload::make('cover_image')
                            ->label('Foto / Video Sampul')
                            ->collection('cover_image')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm', 'video/quicktime'])
                            ->maxSize(51200)
                            ->helperText('Maks 50MB. Format: JPG, PNG, WebP, GIF, MP4, WebM, MOV'),
                        SpatieMediaLibraryFileUpload::make('gallery_images')
                            ->label('Galeri Media')
                            ->collection('gallery_images')
                            ->visibility('public')
                            ->multiple()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'video/mp4', 'video/webm', 'video/quicktime'])
                            ->maxSize(51200)
                            ->helperText('Maks 50MB per file. Format: JPG, PNG, WebP, GIF, MP4, WebM, MOV'),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label('Simpan')
                            ->color('primary')
                            ->visible(fn($record) => $record === null)
                            ->extraAttributes([
                                'class' => 'w-full sm:w-auto p-4',
                                'x-bind:disabled' => 'uploading',
                                'x-bind:class' => "uploading ? 'opacity-50 cursor-wait' : ''",
                            ])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                Villa::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Villa berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                        Action::make('update')
                            ->label('Perbarui')
                            ->color('primary')
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes([
                                'class' => 'w-full sm:w-auto p-4',
                                'x-bind:disabled' => 'uploading',
                                'x-bind:class' => "uploading ? 'opacity-50 cursor-wait' : ''",
                            ])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                $data['id'] = $livewire->record->id;
                                Villa::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Villa berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(VillaResource::getUrl('index'));
                            }),
                    ])
            ]);
    }
}
