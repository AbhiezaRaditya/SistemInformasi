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
                    // PERBAIKAN: Hanya aktifkan badge jika data relasi studyProgram tidak kosong
                    ->badge(fn ($record) => !empty($record->studyProgram?->codename))
                    ->color('success')
                    ->placeholder('-'),

                TextColumn::make('unit.codename')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $value = $record->unit?->codename;
                        if (!$value) return '-';
                        
                        if (strlen($value) <= 5) return strtoupper($value);
                        
                        return collect(explode(' ', $value))
                            ->map(fn ($word) => str($word)->substr(0, 1)->upper())
                            ->implode('');
                    })
                    // PERBAIKAN: Hanya aktifkan badge jika data relasi unit tidak kosong
                    ->badge(fn ($record) => !empty($record->unit?->codename))
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