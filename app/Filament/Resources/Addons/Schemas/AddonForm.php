<?php

namespace App\Filament\Resources\Addons\Schemas;

use App\Filament\Resources\Addons\AddonResource;
use App\Models\Addon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class AddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Addon')
                    ->columnSpanFull()
                    ->description('Masukkan data lengkap addon')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null)
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (Addon $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Addon berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(AddonResource::getUrl('index'));
                            }),
                        Action::make('reset')
                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'name' => null,
                                    'type' => null,
                                    'pricing_unit' => null,
                                    'price' => null,
                                    'description' => null,
                                    'is_active' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Add-on')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('Tipe')
                            ->required()
                            ->native(false)
                            ->options([
                                'food' => 'Makanan / Minuman',
                                'activity' => 'Aktivitas',
                                'item' => 'Barang / Perlengkapan',
                            ]),

                        Select::make('pricing_unit')
                            ->label('Satuan Harga')
                            ->required()
                            ->native(false)
                            ->options([
                                'flat' => 'Per Order (Sekali Bayar)',
                                'per_night' => 'Per Malam',
                                'per_person' => 'Per Orang',
                                'per_person_per_night' => 'Per Orang / Per Malam',
                            ])
                            ->helperText('Menentukan bagaimana harga dihitung saat order.'),

                        TextInput::make('price')
                            ->label('Harga Satuan (Rp)')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->required()
                            ->default(0)
                            ->formatStateUsing(fn($state) => number_format((float) $state, 0, ',', '.'))
                            ->dehydrateStateUsing(fn($state) => (int) str_replace('.', '', str_replace(',', '', $state))),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Deskripsi singkat tentang add-on ini...'),

                        Checkbox::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false)
                            ->extraInputAttributes(['class' => 'p-4'])
                            ->helperText('Jika dinonaktifkan, addon tidak akan muncul di halaman order.'),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label('Simpan')
                            ->color('primary')
                            ->visible(fn($record) => $record === null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                Addon::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Addon berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(AddonResource::getUrl('index'));
                            }),
                        Action::make('update')
                            ->label('Perbarui')
                            ->color('primary')
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                $data['id'] = $livewire->record->id;
                                Addon::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Addon berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(AddonResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->icon('heroicon-o-arrow-left')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(AddonResource::getUrl('index'));
                            }),
                    ])
            ]);
    }
}
