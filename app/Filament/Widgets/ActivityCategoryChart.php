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
            $name = strtolower($p->name);
            return (str_contains($name, 'aktivitas') || str_contains($name, 'activity'))
                && (str_contains($name, 'kategori') || str_contains($name, 'category'));
        });
    }

    protected function getTableDescription(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        if ($user->hasRole('super_admin')) {
            return 'Total Aktivitas: ' . Activity::count();
        }

        $unitIds = Unit::where('study_program_id', $user->study_program_id)
            ->pluck('id');

        $total = Activity::whereIn('unit_id', $unitIds)->count();

        return 'Total Aktivitas Prodi: ' . $total;
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

        if ($user->hasRole('super_admin')) {
            $units = Unit::orderBy('codename')->get();
        } else {
            $units = Unit::where('study_program_id', $user->study_program_id)
                ->orderBy('codename')
                ->get();
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