<?php

namespace App\Filament\Resources\Attractions\Schemas;

use App\Filament\Resources\Attractions\AttractionResource;
use App\Models\Attraction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\IconSize;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class AttractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Wahana')
                    ->columnSpanFull()
                    ->description('Masukkan data lengkap wahana')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null && !$record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (Attraction $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Wahana berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(AttractionResource::getUrl('index'));
                            }),
                        Action::make('restore')
                            ->label('')
                            ->color('gray')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-arrow-uturn-left')
                            ->requiresConfirmation()
                            ->modalHeading('Kembalikan Data Wahana')
                            ->modalDescription('Data wahana ini akan dikembalikan dan dapat digunakan kembali.')
                            ->modalSubmitActionLabel('Ya, Kembalikan')
                            ->action(function (Attraction $record) {
                                $record->restore();

                                Notification::make()
                                    ->title('Data Berhasil Dikembalikan')
                                    ->body('Data Wahana berhasil dikembalikan')
                                    ->success()
                                    ->send();

                                return redirect()->to(AttractionResource::getUrl('index'));
                            }),
                        Action::make('force_delete')
                            ->label('')
                            ->color('danger')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Permanen Data Wahana')
                            ->modalDescription('Data wahana ini akan dihapus secara permanen dan tidak dapat dikembalikan.')
                            ->modalSubmitActionLabel('Ya, Hapus Permanen')
                            ->action(function (Attraction $record) {
                                $record->forceDelete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus Permanen')
                                    ->body('Data Wahana telah dihapus secara permanen')
                                    ->success()
                                    ->send();

                                return redirect()->to(AttractionResource::getUrl('index'));
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
                                    'base_price' => null,
                                    'status' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Wahana')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(callable $set, ?string $state) => $set('slug', Str::slug($state))),
                        Hidden::make('slug')
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        TextInput::make('base_price')
                            ->label('Harga')
                            ->helperText('Harga tiket wahana')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->required()
                            ->default(0)
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state))),
                        Select::make('status')
                            ->label('Status')
                            ->native(false)
                            ->options([
                                'active' => 'Aktif',
                                'coming_soon' => 'Segera Hadir',
                                'closed' => 'Ditutup'
                            ])
                            ->default('active')
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
                            ->default(Attraction::withTrashed()->max('sort_order') + 1)
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
                        TextInput::make('coordinate')
                            ->label('Koordinat Maps')
                            ->hidden()
                            ->helperText('Format Latitude,Longitude'),
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
                        Checkbox::make('is_free')
                            ->label('Gratis')
                            ->extraInputAttributes(['class' => 'p-4'])
                            ->inline(false),
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
                                Attraction::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Wahana berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(AttractionResource::getUrl('index'));
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
                                Attraction::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Wahana berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(AttractionResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(AttractionResource::getUrl('index'));
                            }),
                    ])
            ]);
    }
}
