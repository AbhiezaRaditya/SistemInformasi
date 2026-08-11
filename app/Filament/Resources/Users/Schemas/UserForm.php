<?php

namespace App\Filament\Resources\Users\Schemas;

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
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->maxLength(255),

                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                // DIUBAH: dari 'study_program_id' (single) jadi 'studyPrograms' (multiple)
                Select::make('studyPrograms')
                    ->label('Program Studi')
                    ->relationship('studyPrograms', 'codename')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                // DIUBAH: dari 'unit_id' (single) jadi 'units' (multiple)
                Select::make('units')
                    ->label('Unit')
                    ->relationship('units', 'codename')
                    ->multiple()
                    ->searchable()
                    ->preload(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }
}