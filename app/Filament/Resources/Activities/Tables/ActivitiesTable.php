<?php

namespace App\Filament\Resources\Activities\Tables;

use Filament\Actions\DeleteAction;
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
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'revisi' => 'Revisi', // FIX: sebelumnya tidak ada, sehingga tampil "revisi" apa adanya
                        'reject' => 'Ditolak',
                        'dalam_realisasi' => 'Dalam Realisasi',
                        'completed' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'draft'   => 'danger',
                        'pending' => 'warning',
                        'revisi'  => 'info',
                        'reject'  => 'danger',
                        'completed' => 'success',
                        'dalam_realisasi' => 'info',
                        default   => 'gray',
                    }),
            ])
            ->filters([])
            ->recordActions([
                // Detail — selalu muncul
                ViewAction::make()
                    ->label('Detail')
                    ->color('gray'), // FIX: dulu tidak ada color(), sehingga fallback ke warna kuning (warning) dari tema/default

                // Edit — super_admin kapan saja, himpunan saat draft/revisi
                EditAction::make()
                    ->label('Edit')
                    ->color('info') // ditambahkan biar warna konsisten & tidak ikut default
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->hasRole('super_admin')) return true;
                        if ($user->hasRole('himpunan')) {
                            return in_array($record->status, ['draft', 'revisi']);
                        }
                        return false;
                    }),

                // Hapus — super_admin kapan saja, himpunan hanya saat draft
                DeleteAction::make()
                    ->label('Hapus')
                    ->color('danger') // ditambahkan biar warna konsisten & tidak ikut default
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($user->hasRole('super_admin')) return true;
                        if ($user->hasRole('himpunan')) {
                            return $record->status === 'draft';
                        }
                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Aktivitas')
                    ->modalDescription('Apakah kamu yakin ingin menghapus aktivitas ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),
            ]);
    }
}