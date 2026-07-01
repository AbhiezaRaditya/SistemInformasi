<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                    ->tooltip(
                        fn($record) =>
                        'Mulai: ' . \Carbon\Carbon::parse($record->tanggal_berlangsung)->format('d M Y') .
                            ' | Selesai: ' . \Carbon\Carbon::parse($record->tanggal_berakhir)->format('d M Y')
                    ),

                TextColumn::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir')
                    ->date()
                    ->sortable()
                    ->tooltip(
                        fn($record) =>
                        'Mulai: ' . \Carbon\Carbon::parse($record->tanggal_berlangsung)->format('d M Y') .
                            ' | Selesai: ' . \Carbon\Carbon::parse($record->tanggal_berakhir)->format('d M Y')
                    ),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'pending' => 'Pending',
                        'reject' => 'Ditolak',
                        'dalam_realisasi' => 'Dalam Realisasi',
                        'completed' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft'   => 'danger',
                        'pending' => 'warning',
                        'revisi'  => 'info',
                        // 'accept'  => 'success',
                        'reject'  => 'danger',
                        'completed' => 'success',
                        'dalam_realisasi' => 'info',
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
                    ->label('Ubah')
                    ->hidden(fn() => Auth::user()->hasRole('kaprodi')),
            ]);
    }
}
