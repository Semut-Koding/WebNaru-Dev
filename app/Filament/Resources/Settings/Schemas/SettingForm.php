<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengaturan')
                    ->description('Masukkan data pengaturan')
                    ->schema([
                        TextInput::make('key')
                            ->required(),
                        Textarea::make('value')
                            ->columnSpanFull(),
                        TextInput::make('group')
                            ->required()
                            ->default('general'),
                        TextInput::make('description'),
                    ])
            ]);
    }
}
