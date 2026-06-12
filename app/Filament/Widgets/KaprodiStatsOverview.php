<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class KaprodiStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kaprodi') ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        $query = Activity::whereHas('unit', function ($q) use ($user) {
            $q->where('study_program_id', $user->study_program_id);
        });

        return [
            // Stat untuk status Menunggu Persetujuan / Pending
            Stat::make('Pending', (clone $query)->where('status', 'pending')->count())
                ->description('Total aktivitas menunggu persetujuan')
                ->color('warning'),

            Stat::make('Disetujui', (clone $query)->where('status', 'accept')->count())
                ->description('Total aktivitas disetujui')
                ->color('success'),

            Stat::make('Revisi', (clone $query)->where('status', 'revisi')->count())
                ->description('Total aktivitas perlu revisi')
                ->color('info'),

            Stat::make('Ditolak', (clone $query)->where('status', 'reject')->count())
                ->description('Total aktivitas ditolak')
                ->color('danger'),
        ];
    }
}