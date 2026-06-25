<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;

class UserVerificationTable extends TableWidget
{
    protected static ?string $heading = 'Role User';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'Super Admin' => 'danger',
                        'Himpunan'    => 'warning',
                        'himpunan'    => 'warning',
                        'Kaprodi'     => 'info',
                        'kaprodi'     => 'info',
                        default       => 'gray',
                    }),
            ]);
    }
} 