<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pengurus_unit.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                    // TextColumn::make('Himpunan_role')
                    // ->label('Himpunan Role')
                    // ->getStateUsing(fn ($record)=>
                    // str(
                    //     $record->Himpunan?->getRoleNames()->first() ?? "User"
                    //     )->replace('_',' ')->title()
                    // )
                    // ->badge()
                    // ->color('success')
                    // ->sortable(false)
                    // ->toggleable(),

                TextColumn::make('title')
                    ->searchable(),
                    TextColumn::make('category.name')
                    ->label('kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal_berlangsung')
                    ->label('Tanggal Berlangsung')
                    ->date()
                    ->sortable(),
                TextColumn::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
