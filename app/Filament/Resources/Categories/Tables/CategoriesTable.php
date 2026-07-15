<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Kategori Kegiatan')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
                EditAction::make()
                    ->label('Ubah')
                    ->color('warning'),
            ])
            ->selectable(false) // Menghilangkan checkbox
            ->bulkActions([]); // Menghilangkan semua bulk actions
    }
}