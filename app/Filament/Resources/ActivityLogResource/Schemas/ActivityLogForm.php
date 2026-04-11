<?php

namespace App\Filament\Resources\ActivityLogResource\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Aktivitas Sistem')
                    ->description('Detail riwayat aktivitas yang dicatat')
                    ->schema([
                        TextInput::make('log_name')
                            ->disabled(),
                        TextInput::make('description')
                            ->disabled(),
                        TextInput::make('subject_type')
                            ->disabled(),
                        TextInput::make('event')
                            ->disabled(),
                        TextInput::make('subject_id')
                            ->disabled(),
                        TextInput::make('causer_type')
                            ->disabled(),
                        TextInput::make('causer_id')
                            ->disabled(),
                        KeyValue::make('properties')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
            ]);
    }
}
