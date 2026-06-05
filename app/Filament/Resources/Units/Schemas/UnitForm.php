<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('codename')
                    ->required(),
                Select::make('study_program_id')
                ->label('Study Program')
                ->relationship('studyProgram', 'codename')
                ->required()
                ->searchable()
                ->preload(),
            ]);
    }
}
