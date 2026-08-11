<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Unit;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class ActivityCategoryChart extends TableWidget
{
    protected static ?string $heading = 'Aktivitas per Kategori';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Cek izin secara fleksibel (mendukung spasi maupun underscore dari Filament Shield)
        return $user->getAllPermissions()->contains(function ($p) {
            $name = strtolower($p->name ?? '');
            return (str_contains($name, 'aktivitas') || str_contains($name, 'activity'))
                && (str_contains($name, 'kategori') || str_contains($name, 'category'));
        });
    }

    protected function getTableDescription(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        $studyProgramIds = method_exists($user, 'studyPrograms') ? $user->studyPrograms()->pluck('study_programs.id') : collect();
        $unitIds = method_exists($user, 'units') ? $user->units()->pluck('units.id') : collect();

        // 1. Super Admin / User tanpa batasan
        if ($studyProgramIds->isEmpty() && $unitIds->isEmpty()) {
            return 'Total Aktivitas: ' . Activity::count();
        } 
        // 2. Kaprodi (Punya Program Studi, tapi tidak punya Unit spesifik)
        elseif ($studyProgramIds->isNotEmpty() && $unitIds->isEmpty()) {
            $targetUnitIds = Unit::whereIn('study_program_id', $studyProgramIds)->pluck('id');
            $total = Activity::whereIn('unit_id', $targetUnitIds)->count();
            return 'Total Aktivitas Prodi: ' . $total;
        } 
        // 3. Himpunan / Unit (Punya Unit spesifik)
        elseif ($unitIds->isNotEmpty()) {
            $total = Activity::whereIn('unit_id', $unitIds)->count();
            return 'Total Aktivitas Unit: ' . $total;
        }

        return 'Total Aktivitas: ' . Activity::where('user_id', $user->id)->count();
    }

    public function table(Table $table): Table
    {
        $user = Auth::user();
        if (!$user) {
            return $table->query(Category::query()->whereNull('id'))->columns([]);
        }

        $columns = [
            TextColumn::make('name')
                ->label('Kategori')
                ->weight('bold'),
        ];

        $studyProgramIds = method_exists($user, 'studyPrograms') ? $user->studyPrograms()->pluck('study_programs.id') : collect();
        $unitIds = method_exists($user, 'units') ? $user->units()->pluck('units.id') : collect();

        // Menyesuaikan daftar unit berdasarkan hak akses relasi user
        if ($studyProgramIds->isEmpty() && $unitIds->isEmpty()) {
            $units = Unit::orderBy('codename')->get();
        } elseif ($studyProgramIds->isNotEmpty() && $unitIds->isEmpty()) {
            $units = Unit::whereIn('study_program_id', $studyProgramIds)
                ->orderBy('codename')
                ->get();
        } elseif ($unitIds->isNotEmpty()) {
            $units = Unit::whereIn('id', $unitIds)
                ->orderBy('codename')
                ->get();
        } else {
            $units = Unit::whereRaw('1 = 0')->get();
        }

        foreach ($units as $unit) {
            $columns[] = TextColumn::make('unit_' . $unit->id)
                ->label($unit->codename ?: $unit->name)
                ->alignCenter()
                ->state(function (Category $record) use ($unit) {
                    return Activity::where('category_id', $record->id)
                        ->where('unit_id', $unit->id)
                        ->count();
                });
        }

        return $table
            ->query(Category::query())
            ->columns($columns)
            ->paginated(false)
            ->striped();
    }
}