<?php

namespace App\Filament\Resources\Activities\Tables;

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
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('pengurus_unit.name')
                    ->label('Nama Pengurus')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul Aktivitas')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Kategori Kegiatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_berlangsung')
                    ->label('Tanggal Berlangsung')
                    ->date()
                    ->sortable()
                    ->tooltip(fn ($record) =>
                        'Mulai: ' . \Carbon\Carbon::parse($record->tanggal_berlangsung)->format('d M Y') .
                        ' | Selesai: ' . \Carbon\Carbon::parse($record->tanggal_berakhir)->format('d M Y')
                    ),

                TextColumn::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable()
                    ->tooltip(fn ($record) =>
                        'Mulai: ' . \Carbon\Carbon::parse($record->tanggal_berlangsung)->format('d M Y') .
                        ' | Selesai: ' . \Carbon\Carbon::parse($record->tanggal_berakhir)->format('d M Y')
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'   => 'danger',
                        'pending' => 'warning',
                        'revisi'  => 'info',
                        'accept'  => 'success',
                        'reject'  => 'danger',
                        default   => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),

                EditAction::make()
                    ->label('Ubah'),
            ]);
    }
}