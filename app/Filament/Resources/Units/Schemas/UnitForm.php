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
                      ->label('Nama Himpunan')
                    ->required(),
                TextInput::make('codename')
                     ->label('Kode Unit')
                    ->required(),
                Select::make('study_program_id')
                ->label('Program Studi')
                ->relationship('studyProgram', 'codename')
                ->required()
                ->searchable()
                ->preload(),
            ]);
    }
}
