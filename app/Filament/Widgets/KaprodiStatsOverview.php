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
    protected ?string $heading = 'Ringkasan Aktivitas';

    protected static ?int $sort = 1;

    protected int | array | null $columns = [
        'default' => 2,
        'sm' => 2,
        'md' => 3,
        'lg' => 3,
    ];



public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Cek izin berdasarkan permission (kaprodi atau himpunan)
        return $user->getAllPermissions()->contains(function ($p) {
            $name = strtolower($p->name ?? '');
            return str_contains($name, 'kaprodi') || str_contains($name, 'himpunan');
        });
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Ambil data relasi persis seperti di ActivityResource
        $studyProgramIds = method_exists($user, 'studyPrograms') ? $user->studyPrograms()->pluck('study_programs.id') : collect();
        $unitIds = method_exists($user, 'units') ? $user->units()->pluck('units.id') : collect();

        $targetUnitIds = collect();
        $onlyOwnActivities = false;

        // KASUS 1: Super Admin / User tanpa batasan (Lihat semua unit)
        if ($studyProgramIds->isEmpty() && $unitIds->isEmpty()) {
            $targetUnitIds = Unit::pluck('id');
        } 
        // KASUS 2: Kaprodi (Punya Program Studi, tapi tidak punya Unit spesifik)
        elseif ($studyProgramIds->isNotEmpty() && $unitIds->isEmpty()) {
            $targetUnitIds = Unit::whereIn('study_program_id', $studyProgramIds)->pluck('id');
        } 
        // KASUS 3: Himpunan / Unit (Punya Unit spesifik)
        elseif ($unitIds->isNotEmpty()) {
            $targetUnitIds = $unitIds;
            $onlyOwnActivities = true; // Sesuai logika: hanya data miliknya sendiri di unit tersebut
        }

        if ($targetUnitIds->isEmpty()) {
            $targetUnitIds = [-999];
        }

        return [
            // DIUBAH: dari '?tab=pending' dst (tidak berfungsi) jadi '?activeTab=...'
            // — ini parameter URL asli yang dibaca oleh sistem Tab di
            // ListActivities::getTabs(). Key di sini harus SAMA PERSIS dengan
            // key array di getTabs() (pending, revisi, reject, dalam_realisasi).
            $this->makeStat('Pending', 'pending', 'warning', $targetUnitIds, $this->activitiesUrl('pending'), $onlyOwnActivities, $user->id, $studyProgramIds, $unitIds),
            $this->makeStat('Revisi', 'revisi', 'info', $targetUnitIds, $this->activitiesUrl('revisi'), $onlyOwnActivities, $user->id, $studyProgramIds, $unitIds),
            $this->makeStat('Ditolak', 'reject', 'danger', $targetUnitIds, $this->activitiesUrl('reject'), $onlyOwnActivities, $user->id, $studyProgramIds, $unitIds),
            $this->makeStat('Dalam Realisasi', 'dalam_realisasi', 'info', $targetUnitIds, $this->activitiesUrl('dalam_realisasi'), $onlyOwnActivities, $user->id, $studyProgramIds, $unitIds),
            $this->makeStat('Selesai', 'completed', 'success', $targetUnitIds, '/dashboard/activity-histories', $onlyOwnActivities, $user->id, $studyProgramIds, $unitIds),
        ];
    }

    /**
     * TAMBAHAN: helper untuk membangun URL halaman Activities dengan tab aktif
     * yang benar-benar terbaca oleh sistem Tab Filament (parameter 'activeTab'),
     * bukan '?tab=...' yang tidak pernah dibaca oleh Livewire manapun.
     */
    protected function activitiesUrl(string $tabKey): string
    {
        return url('/dashboard/activities?activeTab=' . $tabKey);
    }

    protected function makeStat(
        string $title,
        string $status,
        string $color,
        $unitIds,
        string $urlPath,
        bool $onlyOwnActivities,
        int $userId,
        $studyProgramIds,
        $userUnitIds
    ): Stat {
        $query = Activity::query();

        // Mengikuti persis logika Eloquent Query di ActivityResource agar angka widget valid 100%
        if ($studyProgramIds->isEmpty() && $userUnitIds->isEmpty()) {
            // Super Admin: Semua unit
            $query->whereIn('unit_id', $unitIds);
        } elseif ($studyProgramIds->isNotEmpty() && $userUnitIds->isEmpty()) {
            // Kaprodi
            $query->where(function ($q) use ($studyProgramIds, $userId, $unitIds) {
                $q->whereHas('unit', fn ($unitQuery) => $unitQuery->whereIn('study_program_id', $studyProgramIds))
                  ->where('status', '!=', 'draft')
                  ->orWhere('user_id', $userId);
            });
        } elseif ($userUnitIds->isNotEmpty()) {
            // Himpunan / Unit
            $query->whereIn('unit_id', $unitIds);
            if ($onlyOwnActivities) {
                $query->where('user_id', $userId);
            }
        } else {
            $query->where('user_id', $userId);
        }

        $total = $query->where('status', $status)->count();

        // $urlPath sekarang sudah berupa URL absolut lengkap (lihat activitiesUrl()
        // dan URL activity-histories untuk 'Selesai'), jadi tidak perlu dibungkus url() lagi.
        $url = str_starts_with($urlPath, 'http') ? $urlPath : url($urlPath);

        $lihatLink = '<a href="' . $url . '" style="display:inline-block; padding:2px 10px; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:0.72rem; font-weight:600; text-decoration:none; border:1px solid #bfdbfe;">Lihat</a>';

        return Stat::make($title, $total)
            ->description(new HtmlString($lihatLink))
            ->color($color)
            ->extraAttributes([
                'style' => 'border: 1px solid #e2e8f0; border-radius: 16px;',
            ]);
    }
}