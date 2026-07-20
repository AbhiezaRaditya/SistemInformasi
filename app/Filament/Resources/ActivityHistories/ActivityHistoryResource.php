<?php

namespace App\Filament\Resources\ActivityHistories;

use App\Filament\Resources\ActivityHistories\Pages\ListActivityHistories;
use App\Filament\Resources\ActivityHistories\Pages\ViewActivityHistory;
use App\Filament\Resources\ActivityHistories\Schemas\ActivityHistoryForm;
use App\Filament\Resources\ActivityHistories\Tables\ActivityHistoriesTable;
use App\Filament\Resources\Activities\Schemas\ActivityInfolist; // <-- Pastikan import ini ada untuk fix View kosong
use App\Models\Activity;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ActivityHistoryResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Riwayat Aktivitas';
    protected static string|UnitEnum|null $navigationGroup = 'Aktivitas';

    public static function getModelLabel(): string
    {
        return 'Riwayat Aktivitas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Riwayat Aktivitas';
    }

    public static function form(Schema $schema): Schema
    {
        return ActivityHistoryForm::configure($schema);
    }


    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityHistoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityHistories::route('/'),
            'view'  => ViewActivityHistory::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

      
        $query->where('status', 'completed');

        // Logika hak akses
        if ($user->hasRole('super_admin')) {
            return $query; 
        }

        if ($user->hasRole('kaprodi')) {
            return $query->whereHas('unit', function (Builder $q) use ($user) {
                $q->where('study_program_id', $user->study_program_id);
            });
        }

        return $query->where('user_id', $user->id);
    }
}