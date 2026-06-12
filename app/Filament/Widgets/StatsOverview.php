<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Super Admin', User::role('super_admin')->count())
                ->description('Total Super Admin')
                ->color('danger'),

            Stat::make('Kaprodi', User::role('kaprodi')->count())
                ->description('Total Kaprodi')
                ->color('warning'),

            Stat::make('Himpunan', User::role('himpunan')->count())
                ->description('Total Himpunan')
                ->color('success'),
        ];
    }
}