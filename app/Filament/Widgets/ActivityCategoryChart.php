<?php

namespace App\Filament\Widgets;

use App\Models\Activity;
use App\Models\Category;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;

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
        $total = Activity::count();
        return 'Total Aktivitas: ' . $total;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Category::withCount('activities')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Kategori'),

                TextColumn::make('activities_count')
                    ->label('Jumlah Aktivitas')
                    ->sortable(),
            ])
            ->paginated(false)
            ->striped();
    }
}