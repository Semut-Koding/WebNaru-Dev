<?php

namespace App\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Utilities\Get;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    use BaseDashboard\Concerns\HasFiltersForm;
    // NOTE: Tidak menggunakan HasPageShield karena trait tersebut
    // memanggil abort(403) via mountCanAuthorizeAccess() SEBELUM mount() kita.
    // Solusi: override canAccess() + shouldRegisterNavigation() secara manual.

    /**
     * Sidebar visibility — sembunyikan Dashboard dari nav bagi user tanpa permission.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('View:Dashboard') ?? false;
    }

    /**
     * PENTING: Return true di sini agar mountCanAuthorizeAccess() dari
     * trait CanAuthorizeAccess TIDAK melempar abort(403).
     *
     * Pengecekan akses yang sesungguhnya dilakukan di mount().
     */
    public static function canAccess(): bool
    {
        return true;
    }

    /**
     * Manual authorization + redirect untuk user tanpa akses Dashboard.
     */
    public function mount(): void
    {
        // Cek permission manual — jika tidak punya akses, redirect ke resource pertama
        if (!auth()->user()?->can('View:Dashboard')) {
            $panel = Filament::getCurrentPanel();

            foreach ($panel->getResources() as $resource) {
                if ($resource::canAccess()) {
                    $this->redirect($resource::getUrl('index'));

                    return;
                }
            }

            // Fallback: tidak ada resource yang bisa diakses
            abort(403);
        }
    }

    public function filtersForm(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                Select::make('period')
                    ->label('Periode')
                    ->options([
                        'today' => 'Hari Ini',
                        'week' => 'Minggu Ini',
                        'month' => 'Bulan Ini',
                        'year' => 'Tahun Ini',
                        'custom' => 'Kustom',
                    ])
                    ->default('today')
                    ->live(),
                DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->visible(fn(Get $get) => $get('period') === 'custom')
                    ->default(now()->startOfMonth())
                    ->native(false),
                DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->visible(fn(Get $get) => $get('period') === 'custom')
                    ->default(now())
                    ->native(false),
            ]);
    }
}
