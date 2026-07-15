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

                Select::make('study_program_id')
                    ->label('Program Studi')
                    ->relationship('studyProgram', 'codename')
                    ->searchable()
                    ->preload(),

                Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'codename') // BERHASIL DIPERBAIKI: Menggunakan 'unit' huruf kecil agar sinkron dengan Model
                    ->searchable()
                    ->preload(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    // BERHASIL DIPERBAIKI: Wajib diisi HANYA saat membuat user baru. Saat edit boleh dikosongkan.
                    ->required(fn (string $operation): bool => $operation === 'create')
                    // Mencegah password lama tertimpa kosong (null) jika kolom tidak diisi saat edit
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }
}