<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->extraAttributes(['class' => 'w-full sm:w-auto']),
            $this->getCancelFormAction()
                ->label('Kembali')
                ->color('gray')
                ->extraAttributes(['class' => 'w-full sm:w-auto']),
        ];
    }
}
