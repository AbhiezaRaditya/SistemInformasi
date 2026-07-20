<?php

namespace App\Filament\Resources\ActivityHistories\Tables;

use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class ActivityHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),

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
                        'danger'  => 'reject',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'reject'    => 'Ditolak',
                        default     => $state,
                    }),
            ])
            ->actions([
                ActionsViewAction::make()
                    ->label('Detail')
                    ->color('gray'),

                ActionsDeleteAction::make()
                    ->label('Hapus')
                    ->color('danger')
                    ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false),
            ]);
    }
}