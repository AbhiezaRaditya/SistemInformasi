<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

use App\Models\StudyProgram;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Program Studi')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Program Studi'),

                        TextEntry::make('codename')
                            ->label('Kode Prodi'),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime()
                            ->placeholder('-')
                            ->visible(fn (StudyProgram $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}