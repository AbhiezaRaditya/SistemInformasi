<?php

namespace App\Filament\Resources\ActivityHistories;

use App\Filament\Resources\ActivityHistories\Pages\ListActivityHistories;
use App\Filament\Resources\ActivityHistories\Pages\ViewActivityHistory;
use App\Filament\Resources\ActivityHistories\Schemas\ActivityHistoryForm;
use App\Filament\Resources\ActivityHistories\Tables\ActivityHistoriesTable;
use App\Filament\Resources\Activities\Schemas\ActivityInfolist;
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

    // ==========================================
    // PENGUNCIAN MUTLAK DI LEVEL KODE (AMAN)
    // ==========================================
    
    public static function canCreate(): bool
    {
        return false; // Mutlak dikunci, tidak bisa membuat data baru sama sekali di sini
    }

    public static function canEdit($record): bool
    {
        return false; // Mutlak dikunci, tidak bisa mengedit data sama sekali di sini
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->hasRole('super_admin')) return true;

        return $user->can('view_activity') || $user->can('view_any_activity');
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->hasRole('super_admin')) return true;

        return $user->can('view_activity');
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->hasRole('super_admin')) return true;

        return $user->can('delete_activity');
    }

    public static function canDeleteAny(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ($user->hasRole('super_admin')) return true;

        return $user->can('delete_any_activity');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        // Selalu filter hanya aktivitas yang sudah 'completed' (selesai)
        $query->where('status', 'completed');

        // 1. Jika Super Admin (Program Studi dan Unit kosong) -> Lihat Semua Riwayat
        if (empty($user->study_program_id) && empty($user->unit_id)) {
            return $query;
        }

        // 2. Jika Kaprodi (Program Studi terisi, Unit kosong) -> Lihat riwayat unit di bawah Prodi tersebut
        if (!empty($user->study_program_id) && empty($user->unit_id)) {
            return $query->whereHas('unit', function (Builder $q) use ($user) {
                $q->where('study_program_id', $user->study_program_id);
            });
        }

        // 3. Jika Himpunan / Unit (Unit terisi) -> Hanya lihat riwayat unit miliknya sendiri
        if (!empty($user->unit_id)) {
            return $query->where('unit_id', $user->unit_id);
        }

        return $query->where('user_id', $user->id);
    }
}