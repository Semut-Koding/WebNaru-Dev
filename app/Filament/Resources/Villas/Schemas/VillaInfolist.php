<?php

namespace App\Filament\Resources\Villas\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Number;

class VillaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Villa')
                    ->description('Data detail villa')
                    ->icon('heroicon-o-home-modern')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Villa')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->markdown(),
                        TextEntry::make('bedroom_count')
                            ->label('Kamar Tidur')
                            ->icon('heroicon-o-moon')
                            ->suffix(' Kamar'),
                        TextEntry::make('bathroom_count')
                            ->label('Kamar Mandi')
                            ->icon('heroicon-o-sparkles')
                            ->suffix(' KM'),
                        TextEntry::make('capacity')
                            ->label('Kapasitas')
                            ->icon('heroicon-o-users')
                            ->suffix(' Tamu'),
                        TextEntry::make('base_price_weekday')
                            ->label('Harga Weekday')
                            ->formatStateUsing(fn($state) => Number::rupiah($state))
                            ->icon('heroicon-o-banknotes')
                            ->color('success'),
                        TextEntry::make('base_price_weekend')
                            ->label('Harga Weekend')
                            ->formatStateUsing(fn($state) => Number::rupiah($state))
                            ->icon('heroicon-o-banknotes')
                            ->color('warning'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'available' => 'success',
                                'coming_soon' => 'info',
                                'closed' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'available' => 'Tersedia',
                                'coming_soon' => 'Segera Hadir',
                                'closed' => 'Ditutup',
                                default => $state,
                            }),
                        TextEntry::make('amenities')
                            ->label('Fasilitas')
                            ->badge()
                            ->color('primary')
                            ->separator(',')
                            ->formatStateUsing(fn($state) => is_array($state) ? (($state['name'] ?? $state) ?: '-') : $state),
                        TextEntry::make('benefits')
                            ->label('Keuntungan')
                            ->badge()
                            ->color('success')
                            ->separator(',')
                            ->formatStateUsing(fn($state) => is_array($state) ? (($state['name'] ?? $state) ?: '-') : $state),
                        TextEntry::make('coordinate')
                            ->label('Koordinat Maps')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Belum diatur'),
                    ])
                    ->columnSpanFull()
                    ->columns(3),

                Section::make('Foto')
                    ->description('Cover & Galeri')
                    ->icon('heroicon-o-photo')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        SpatieMediaLibraryImageEntry::make('cover_image')
                            ->label('Foto Sampul')
                            ->collection('cover_image')
                            ->height(200)
                            ->visibility('public')
                            ->columnSpanFull(),
                        SpatieMediaLibraryImageEntry::make('gallery_images')
                            ->label('Galeri Foto')
                            ->collection('gallery_images')
                            ->height(150)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
