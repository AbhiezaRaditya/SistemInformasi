<?php

namespace App\Filament\Resources\ActivityHistories\Tables;

use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\DeleteBulkAction as ActionsDeleteBulkAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;

class ActivityHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pengurus_unit.name')
                    ->label('Nama Pengurus')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Judul Aktivitas')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Kategori Kegiatan')
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
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'completed',
                        'warning' => 'pending',
                        'danger'  => 'reject',
                        'info'    => 'dalam_realisasi',
                    ]),
            ])
            ->actions([
                ActionsViewAction::make()->color('gray'), 
                ActionsDeleteAction::make()->color('danger'),
            ])
            ->bulkActions([
                ActionsDeleteBulkAction::make(),
            ]);
    }
}