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
                            ->color(fn (string $state) => match ($state) {
                                'draft' => 'gray',
                                'pending' => 'warning',
                                'revisi' => 'info',
                                'accept' => 'success',
                                'reject' => 'danger',
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
                            ->formatStateUsing(fn ($state) => "
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
                            ->visible(fn ($record) => filled($record->catatan_revisi)),
                    ]),

                Section::make('Dokumen Lampiran')
                    ->schema([
                        TextEntry::make('attachment')
                            ->label('Nama File')
                            ->formatStateUsing(fn ($state) => basename($state))
                            ->icon('heroicon-o-document')
                            ->color('primary')
                            ->url(fn ($state) => asset('storage/' . $state))
                            ->openUrlInNewTab(),
                    ])
                    ->visible(fn ($record) => filled($record->attachment)),

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