<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Umum')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('pengurus_unit.name')
                            ->label('Nama Pengurus'),

                        TextEntry::make('unit.codename')
                            ->label('Unit'),

                        TextEntry::make('title')
                            ->label('Judul Aktivitas')
                            ->columnSpanFull(),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Kegiatan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('category.name')
                            ->label('Kategori Kegiatan'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn($state) => match ($state) {
                                'reject' => 'Ditolak',
                                'dalam_realisasi' => 'Dalam Realisasi',
                                'completed' => 'Selesai',
                                default => $state,
                            })
                            ->color(fn(string $state) => match ($state) {
                                'draft'   => 'danger',
                                'pending' => 'warning',
                                'revisi'  => 'info',
                                'accept'  => 'success',
                                'reject'  => 'danger',
                                'completed' => 'success',
                                'dalam_realisasi' => 'info',
                                default => 'gray',
                            }),

                        TextEntry::make('tanggal_berlangsung')
                            ->label('Tanggal Berlangsung')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir')
                            ->date()
                            ->placeholder('-'),

                        TextEntry::make('catatan_revisi')
                            ->label('Catatan Revisi')
                            ->html()
                            ->formatStateUsing(fn($state) => "
                                <div style='
                                    background: rgba(255, 235, 59, 0.20);
                                    border-left: 4px solid #facc15;
                                    padding: 12px;
                                    border-radius: 8px;
                                '>
                                    {$state}
                                </div>
                            ")
                            ->columnSpanFull()
                            ->visible(fn($record) => filled($record->catatan_revisi)),
                    ]),

                Section::make('Dokumen Lampiran')
                    ->schema([
                        TextEntry::make('attachment')
                            ->label('Nama File')
                            ->formatStateUsing(fn($state) => basename($state))
                            ->icon('heroicon-o-document')
                            ->color('primary')
                            ->url(fn($state) => $state ? asset('storage/' . $state) : null)
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn($record) => filled($record->attachment)),


                    
                Section::make('Dokumen Realisasi')
                    ->schema([
                        TextEntry::make('realization_file')
                            ->label('File Realisasi / LPJ')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return implode(', ', array_map('basename', $state));
                                }
                                return $state ? basename($state) : null;
                            })
                            ->icon('heroicon-o-document-check')
                            ->color('success')
                            ->url(function ($state) {
                                $path = is_array($state) ? ($state[0] ?? null) : $state;
                                return $path ? asset('storage/' . $path) : null;
                            })
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn($record) => filled($record->realization_file)),

                Section::make('Timestamps')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime(),
                    ]),
            ]);
    }
}
