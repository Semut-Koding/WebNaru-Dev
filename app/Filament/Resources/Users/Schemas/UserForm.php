<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Support\HtmlString;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pegawai')
                    ->columnSpanFull()
                    ->description('Lengkapi informasi kredensial dan profil pegawai di bawah ini.')
                    ->afterHeader([
                        Action::make('delete')
                            ->label('Hapus Data')
                            ->color('danger')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->visible(fn($record) => $record !== null && !$record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->action(function (User $record) {
                                $record->delete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus')
                                    ->body('Data Pegawai berhasil dihapus')
                                    ->success()
                                    ->send();

                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                        Action::make('restore')
                            ->label('')
                            ->color('gray')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-arrow-uturn-left')
                            ->requiresConfirmation()
                            ->modalHeading('Kembalikan Data Pegawai')
                            ->modalDescription('Data pegawai ini akan dikembalikan dan dapat digunakan kembali.')
                            ->modalSubmitActionLabel('Ya, Kembalikan')
                            ->action(function (User $record) {
                                $record->restore();

                                Notification::make()
                                    ->title('Data Berhasil Dikembalikan')
                                    ->body('Data Pegawai berhasil dikembalikan')
                                    ->success()
                                    ->send();

                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                        Action::make('force_delete')
                            ->label('')
                            ->color('danger')
                            ->visible(fn($record) => $record !== null && $record->trashed())
                            ->icon('heroicon-m-trash')
                            ->requiresConfirmation()
                            ->modalHeading('Hapus Permanen Data Pegawai')
                            ->modalDescription('Data pegawai ini akan dihapus secara permanen dan tidak dapat dikembalikan.')
                            ->modalSubmitActionLabel('Ya, Hapus Permanen')
                            ->action(function (User $record) {
                                $record->forceDelete();

                                Notification::make()
                                    ->title('Data Berhasil Dihapus Permanen')
                                    ->body('Data Pegawai telah dihapus secara permanen')
                                    ->success()
                                    ->send();

                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                        Action::make('reset')
                            ->color('danger')
                            ->visible(fn($record) => $record === null)
                            ->icon('heroicon-m-arrow-path')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function ($livewire) {
                                $livewire->form->fill([
                                    'name' => null,
                                    'email' => null,
                                    'phone' => null,
                                    'password' => null,
                                ]);
                            }),
                    ])
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->label('Nama Pegawai'),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('phone')
                            ->label('No. Telepon / WhatsApp')
                            ->tel()
                            ->maxLength(20),
                        // DateTimePicker::make('email_verified_at')
                        //     ->label('Waktu Verifikasi Email')
                        //     ->hint('Biarkan kosong jika verifikasi dilakukan manual oleh sistem.')
                        //     ->native(false)
                        //     ->displayFormat('d/m/Y H:i'),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->hint('Minimal 8 karakter')
                            ->minLength(8)
                            ->autocomplete(false)
                            ->password()
                            ->required(fn($record) => $record === null)
                            ->saved(fn (?string $state): bool => filled($state))
                            ->revealable(),
                        TextEntry::make('role_reminder')
                            ->label('')
                            ->state(new HtmlString(
                                '<div class="flex items-center gap-2 p-3 text-sm text-amber-700 bg-amber-50 dark:text-amber-400 dark:bg-amber-500/10 rounded-lg border border-amber-200 dark:border-amber-500/20">' .
                                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>' .
                                '<span><strong>Perhatian:</strong> Setelah data pegawai berhasil disimpan, jangan lupa untuk mengatur <strong>Peran & Jabatan</strong> melalui menu Edit.</span>' .
                                '</div>'
                            ))
                            ->columnSpanFull()
                            ->visible(fn($record) => $record === null),
                        Select::make('roles')
                            ->label('Peran & Jabatan')
                            ->hint('Tentukan hak akses.')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required()
                            ->visible(fn($record) => $record !== null),
                        Checkbox::make('is_active')
                            ->label('Status Akun Aktif')
                            ->hint('Jika dinonaktifkan, pegawai tidak akan bisa login.')
                            ->inline(false)
                            ->extraInputAttributes(['class' => 'p-4']),
                        // Toggle::make('is_online')
                        //     ->label('Status Online')
                        //     ->hint('Hanya indikator status kehadiran real-time.')
                        //     ->disabled(),
                        // DateTimePicker::make('last_login_at')
                        //     ->label('Terakhir Login')
                        //     ->hint('Catatan otomatis waktu akses terakhir.')
                        //     ->disabled()
                        //     ->native(false),
                    ])
                    ->footer([
                        Action::make('create')
                            ->label('Simpan')
                            ->color('primary')
                            ->visible(fn($record) => $record === null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                User::create($data);

                                Notification::make()
                                    ->title('Berhasil Disimpan')
                                    ->body('Data Pegawai berhasil ditambahkan. Jangan lupa atur Peran & Jabatan melalui menu Edit!')
                                    ->warning()
                                    ->send();
                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                        Action::make('update')
                            ->label('Perbarui')
                            ->color('primary')
                            ->visible(fn($record) => $record !== null)
                            ->extraAttributes(['class' => 'w-full sm:w-auto p-4'])
                            ->action(function ($livewire) {
                                $data = $livewire->form->getState();
                                $livewire->record->update($data);
                                Notification::make()
                                    ->title('Berhasil Diperbarui')
                                    ->body('Data Pegawai berhasil diperbarui')
                                    ->success()
                                    ->send();
                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                        Action::make('cancel')
                            ->label('Kembali')
                            ->color('gray')
                            ->extraAttributes(['class' => 'w-full sm:w-auto'])
                            ->action(function () {
                                return redirect()->to(UserResource::getUrl('index'));
                            }),
                    ])
            ]);
    }
}
