<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('name')
                    ->label('Unit')
                    ->searchable(),
                TextColumn::make('studyProgram.codename')
                    ->label('Kode Unit')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                
                    ->badge(fn ($record) => !empty($record->studyProgram?->codename))
                    ->color('info'),
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