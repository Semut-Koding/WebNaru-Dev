<?php

namespace App\Filament\Resources\VillaUnits\Schemas;

use App\Filament\Resources\VillaUnits\VillaUnitResource;
use App\Models\VillaUnit;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VillaUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Unit Villa')
                    ->columnSpanFull()
                    ->description('Kelola data unit villa')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null && !$record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (VillaUnit $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Unit Villa berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                        Action::make('restore')
                            ->label('')
                            ->color('gray')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-arrow-uturn-left')
                            ->requiresConfirmation()
                            ->modalHeading('Kembalikan Unit Villa')
                            ->modalDescription('Unit villa ini akan dikembalikan dan dapat digunakan kembali.')
                            ->modalSubmitActionLabel('Ya, Kembalikan')
                            ->action(function (VillaUnit $record) {
                                $record->restore();

                                Notification::make()
                                    ->title('Data Berhasil Dikembalikan')
                                    ->body('Unit Villa berhasil dikembalikan')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                        Action::make('force_delete')
                            ->label('')
                            ->color('danger')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Permanen Unit Villa')
                            ->modalDescription('Unit villa ini akan dihapus secara permanen dan tidak dapat dikembalikan.')
                            ->modalSubmitActionLabel('Ya, Hapus Permanen')
                            ->action(function (VillaUnit $record) {
                                $record->forceDelete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus Permanen')
                                    ->body('Unit Villa telah dihapus secara permanen')
                                    ->success()
                                    ->send();

                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                        Action::make('reset')
                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'villa_id' => null,
                                    'unit_name' => null,
                                    'status' => null,
                                    'is_active' => null,
                                    'notes' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        Select::make('villa_id')
                            ->label('Villa')
                            ->relationship('villa', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('unit_name')
                            ->label('Nama Unit')
                            ->hint('Contoh: Villa A1, Kamar 101')
                            ->required()
                            ->maxLength(100),
                        Select::make('status')
                            ->label('Status Unit')
                            ->options([
                                'available' => '✅ Tersedia',
                                'occupied' => '🔴 Dihuni',
                                'cleaning' => '🧹 Dibersihkan',
                                'maintenance' => '🔧 Perbaikan',
                            ])
                            ->default('available')
                            ->required(),
                        Checkbox::make('is_active')
                            ->label('Unit Aktif')
                            ->hint('Nonaktifkan jika unit tidak tersedia untuk booking.')
                            ->inline(false)
                            ->default(true)
                            ->extraInputAttributes(['class' => 'p-4']),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label('Simpan')
                            ->color('primary')
                            ->visible(fn($record) => $record === null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                VillaUnit::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Unit Villa berhasil ditambahkan')
                                    ->success()
                                    ->send();
                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                        Action::make('update')
                            ->label('Perbarui')
                            ->color('primary')
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                $data['id'] = $livewire->record->id;
                                VillaUnit::where('id', $data['id'])->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Unit Villa berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(VillaUnitResource::getUrl('index'));
                            }),
                    ])
            ]);
    }
}
