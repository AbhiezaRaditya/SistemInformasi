<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

use App\Models\StudyProgram;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudyProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('codename'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (StudyProgram $record): bool => $record->trashed()),
            ]);
    }
}
