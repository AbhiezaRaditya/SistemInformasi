<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('username')
                    ->required(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Select::make('study_program_id')
                    ->label('Study Program')
                    ->relationship('studyProgram', 'codename')
                    ->searchable()
                    ->preload(),
                Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('Unit', 'codename')
                    ->searchable()
                    ->preload(),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }
}
