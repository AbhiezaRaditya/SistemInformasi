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
                    ->required(),
                TextInput::make('codename')
                    ->required(),
            ]);
    }
}
