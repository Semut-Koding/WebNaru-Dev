<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan')
                ->extraAttributes(['class' => 'w-full sm:w-auto']),
            $this->getCreateAnotherFormAction()
                ->label('Simpan & Baru')
                ->color('primary')
                ->extraAttributes(['class' => 'w-full sm:w-auto']),
            $this->getCancelFormAction()
                ->label('Kembali')
                ->color('gray')
                ->extraAttributes(['class' => 'w-full sm:w-auto']),
        ];
    }
}
