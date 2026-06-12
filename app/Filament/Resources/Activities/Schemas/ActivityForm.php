<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Aktivitas')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('attachment')
                    ->label('Lampiran / Berkas Pendukung')
                    ->disk('public')
                    ->directory('attachment')
                    ->visibility('public')
                    ->preserveFilenames()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->maxSize(2048),

                Textarea::make('description')
                    ->label('Deskripsi Kegiatan')
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('tanggal_berlangsung')
                    ->label('Tanggal Berlangsung'),

                DatePicker::make('tanggal_berakhir')
                    ->label('Tanggal Berakhir'),

                Select::make('category_id')
                    ->label('Kategori Kegiatan')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('status')
                    ->label('Status Persetujuan')
                    ->options([
                        'revisi' => 'Revisi',
                        'accept' => 'Disetujui',
                        'reject' => 'Ditolak',
                    ])
                    ->default('draft')
                    ->live()
                    ->visible(
                        fn () => Auth::user()->hasRole([
                            'kaprodi',
                            'super_admin',
                        ])
                    ),

                Textarea::make('catatan_revisi')
                    ->label('Catatan Revisi / Penolakan dari Kaprodi')
                    ->required(
                        fn ($get) => in_array(
                            $get('status'),
                            ['revisi', 'reject']
                        )
                    )
                    ->visible(
                        fn ($get) => in_array(
                            $get('status'),
                            ['revisi', 'reject']
                        )
                    )
                    ->disabled(
                        fn () => ! Auth::user()->hasRole([
                            'kaprodi',
                            'super_admin',
                        ])
                    )
                    ->columnSpanFull(),
            ]);
    }
}