<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class KaprodiStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | array | null $columns = [
        'default' => 2,
        'sm' => 2,
        'md' => 3,
        'lg' => 3,
    ];

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('kaprodi') ?? false;
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        $unitIds = Unit::where('study_program_id', $user->study_program_id)
            ->pluck('id');

        return [
            $this->makeStat('Pending', 'pending', 'warning', $unitIds, '/dashboard/activities?tab=pending'),
            $this->makeStat('Revisi', 'revisi', 'info', $unitIds, '/dashboard/activities?tab=revisi'),
            $this->makeStat('Ditolak', 'reject', 'danger', $unitIds, '/dashboard/activities?tab=reject'),
            $this->makeStat('Dalam Realisasi', 'dalam_realisasi', 'info', $unitIds, '/dashboard/activities?tab=realisasi'),
            // URL diarahkan ke halaman Riwayat Aktivitas
            $this->makeStat('Selesai', 'completed', 'success', $unitIds, '/dashboard/activity-histories'),
        ];
    }

    protected function makeStat(
        string $title,
        string $status,
        string $color,
        $unitIds,
        string $urlPath
    ): Stat {
        $total = Activity::whereIn('unit_id', $unitIds)
            ->where('status', $status)
            ->count();

        $url = url($urlPath);

        $lihatLink = '<a href="' . $url . '" style="display:inline-block; padding:2px 10px; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:0.72rem; font-weight:600; text-decoration:none; border:1px solid #bfdbfe;">Lihat</a>';

        return Stat::make($title, $total)
            ->description(new HtmlString($lihatLink))
            ->color($color)
            ->extraAttributes([
                'style' => 'border: 1px solid #e2e8f0; border-radius: 16px;',
            ]);
    }
}