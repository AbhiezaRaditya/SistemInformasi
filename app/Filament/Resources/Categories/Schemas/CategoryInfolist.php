<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Kategori Kegiatan'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime()
                            ->placeholder('-')
                            ->visible(fn (Category $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}