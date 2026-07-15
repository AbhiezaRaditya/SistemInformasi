<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Unit;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class ActivityCategoryChart extends TableWidget
{
    protected static ?string $heading = 'Aktivitas per Kategori';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'kaprodi',
        ]) ?? false;
    }

    protected function getTableDescription(): ?string
    {
        $user = auth()->user();

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
        $user = auth()->user();

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