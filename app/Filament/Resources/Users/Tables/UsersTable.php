<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->getStateUsing(
                        fn ($record) => str($record->getRoleNames()->first() ?? 'User')
                            ->replace('_', ' ')
                            ->title()
                    )
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Admin' => 'danger',
                        'Kaprodi' => 'info',
                        'Himpunan' => 'warning',
                        default => 'gray',
                    }),

                // DIUBAH: dari 'studyProgram.codename' (singular) jadi relasi many-to-many
                // 'studyPrograms' (plural). Filament otomatis menampilkan badge untuk
                // setiap item saat relasinya to-many.
                TextColumn::make('studyPrograms.codename')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('-'),

                // DIUBAH: dari 'unit.codename' (singular) jadi 'units' (plural, many-to-many).
                // Logic singkatan kamu tetap dipertahankan, tapi sekarang dijalankan
                // per-item untuk setiap unit yang dimiliki user.
                TextColumn::make('units.codename')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $codenames = $record->units->pluck('codename');

                        if ($codenames->isEmpty()) {
                            return null; // biar placeholder '-' yang muncul
                        }

                        return $codenames->map(function ($value) {
                            if (strlen($value) <= 5) {
                                return strtoupper($value);
                            }

                            return collect(explode(' ', $value))
                                ->map(fn ($word) => str($word)->substr(0, 1)->upper())
                                ->implode('');
                        })->all();
                    })
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),
                EditAction::make()
                    ->label('Ubah')
                    ->color('warning'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}