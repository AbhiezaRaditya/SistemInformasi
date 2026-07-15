<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama'),

                        TextEntry::make('role')
                            ->label('Role')
                            ->getStateUsing(
                                fn ($record) =>
                                str($record->getRoleNames()->first() ?? 'User')
                                    ->replace('_', ' ')
                                    ->title()
                            )
                            ->badge()
                            // PERBAIKAN: Mengembalikan warna badge sesuai role di halaman View
                            ->color(fn (string $state): string => match ($state) {
                                'Super Admin' => 'danger',
                                'Kaprodi'     => 'info',
                                'Himpunan'    => 'warning',
                                default       => 'gray',
                            }),

                        TextEntry::make('username')
                            ->label('Username'),

                        TextEntry::make('studyProgram.codename')
                            ->label('Program Studi')
                            // PERBAIKAN: Hanya pasang badge jika data relasi prodi tidak kosong
                            ->badge(fn ($record) => !empty($record->studyProgram?->codename))
                            ->color('success')
                            ->placeholder('-'),

                        TextEntry::make('unit.codename')
                            ->label('Unit')
                            // PERBAIKAN: Hanya pasang badge jika data relasi unit tidak kosong
                            ->badge(fn ($record) => !empty($record->unit?->codename))
                            ->color('info')
                            ->placeholder('-'),
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
                            ->visible(fn (User $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}