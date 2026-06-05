<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('pengurus_unit.name')
                    ->label('Name'),
                TextEntry::make('unit.codename')
                    ->label('unit'),
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('category.name')
                    ->label('Kategori'),
                TextEntry::make('attachment')
                    ->label('Dokumen Lampiran')
                    ->formatStateUsing(fn() => 'Lihat')
                    ->color('primary')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->listWithLineBreaks()
                    ->url(fn($state) => asset('storage/' . $state))
                    ->openUrlInNewTab(),
                TextEntry::make('tanggal_berlangsung')
                    ->label('Tanggal Berlangsung')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
