<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudyProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Program Studi')
                    ->required(),

                TextInput::make('codename')
                    ->label('Kode Prodi')
                    ->required(),
            ]);
    }
}