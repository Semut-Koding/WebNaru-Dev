<?php

namespace App\Filament\Resources\ActivityLogResource\Schemas;

use Filament\Infolists\Components\TextEntry;
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
                        TextEntry::make('log_name')
                            ->label('Nama Log'),
                        TextEntry::make('description')
                            ->label('Deskripsi'),
                        TextEntry::make('subject_type')
                            ->label('Tipe Subjek')
                            ->formatStateUsing(fn (?string $state) => $state ? class_basename($state) : '-'),
                        TextEntry::make('event')
                            ->label('Event')
                            ->badge()
                            ->color(fn (?string $state) => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('subject_id')
                            ->label('ID Subjek'),
                        TextEntry::make('causer.name')
                            ->label('Dilakukan Oleh')
                            ->default('-'),
                        TextEntry::make('created_at')
                            ->label('Waktu')
                            ->dateTime('d/m/Y H:i:s'),
                    ]),

                Section::make('Nilai Lama')
                    ->description('Data sebelum perubahan dilakukan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->collapsed(false)
                    ->visible(fn ($record) => !empty($record?->properties['old'] ?? null))
                    ->schema(fn ($record) => static::buildPropertyEntries($record?->properties['old'] ?? [], 'old')),

                Section::make('Nilai Baru')
                    ->description('Data setelah perubahan dilakukan')
                    ->icon('heroicon-o-arrow-right')
                    ->collapsed(false)
                    ->visible(fn ($record) => !empty($record?->properties['attributes'] ?? null))
                    ->schema(fn ($record) => static::buildPropertyEntries($record?->properties['attributes'] ?? [], 'attributes')),
            ]);
    }

    /**
     * Build TextEntry components for each property key-value pair.
     */
    protected static function buildPropertyEntries(array $data, string $prefix): array
    {
        if (empty($data)) {
            return [];
        }

        $entries = [];
        foreach ($data as $key => $value) {
            $label = str($key)->replace('_', ' ')->title()->toString();
            $displayValue = match (true) {
                is_null($value) => '-',
                is_bool($value) => $value ? 'Ya' : 'Tidak',
                is_array($value) => json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                default => (string) $value,
            };

            $entries[] = TextEntry::make("properties.{$prefix}.{$key}")
                ->label($label)
                ->state($displayValue)
                ->badge(in_array($key, ['status', 'payment_status', 'payment_method', 'source']))
                ->color(fn () => match ($key) {
                    'status' => match ($value) {
                        'checked_in' => 'success',
                        'booked' => 'info',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'checked_out' => 'gray',
                        default => 'gray',
                    },
                    'payment_status' => match ($value) {
                        'paid' => 'success',
                        'dp_paid' => 'warning',
                        'unpaid' => 'danger',
                        default => 'gray',
                    },
                    default => null,
                });
        }

        return $entries;
    }
}
