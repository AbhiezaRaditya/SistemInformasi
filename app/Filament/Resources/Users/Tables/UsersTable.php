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

                TextColumn::make('studyProgram.codename')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('unit.codename')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Detail'),

                EditAction::make()
                    ->label('Ubah'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}