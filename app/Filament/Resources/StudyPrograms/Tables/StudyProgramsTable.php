<?php

namespace App\Filament\Resources\StudyPrograms\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class StudyProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('codename')
                    ->label('Kode Prodi')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
                EditAction::make()
                    ->label('Ubah')
                    ->color('warning'),
            ]);
    }
}