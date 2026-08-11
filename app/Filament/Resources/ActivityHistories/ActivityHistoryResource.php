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
use Illuminate\Support\Facades\Gate;

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
    // PENGUNCIAN MUTLAK & SINKRONISASI SHIELD
    // ==========================================

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Murni membaca izin dari Filament Shield / Policy
        return Gate::allows('viewAny', Activity::class);
    }

    public static function canView($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return Gate::allows('view', $record);
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return Gate::allows('delete', $record);
    }

    public static function canDeleteAny(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return Gate::allows('deleteAny', Activity::class);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Selalu filter hanya aktivitas yang sudah 'completed' (selesai)
        $query->where('status', 'completed');

        // Cek apakah user memiliki izin View Any melalui Gate/Shield
        $hasViewAny = Gate::allows('viewAny', Activity::class);

        // Jika TIDAK punya izin View Any, batasi secara mutlak HANYA miliknya sendiri!
        if (!$hasViewAny) {
            return $query->where('user_id', $user->id);
        }

        // Ambil daftar ID Program Studi & Unit milik user jika punya izin View Any
        $studyProgramIds = $user->studyPrograms()->pluck('study_programs.id');
        $unitIds = $user->units()->pluck('units.id');

        // 1. Jika Super Admin / User tanpa batasan unit/prodi (tapi punya izin ViewAny) -> Lihat Semua
        if ($studyProgramIds->isEmpty() && $unitIds->isEmpty()) {
            return $query;
        }

        // 2. Jika Kaprodi (punya Program Studi, tapi tidak punya Unit)
        if ($studyProgramIds->isNotEmpty() && $unitIds->isEmpty()) {
            return $query->whereHas('unit', function (Builder $q) use ($studyProgramIds) {
                $q->whereIn('study_program_id', $studyProgramIds);
            });
        }

        // 3. Jika Himpunan / Unit (punya Unit)
        if ($unitIds->isNotEmpty()) {
            return $query->whereIn('unit_id', $unitIds);
        }

        // Default fallback: Hanya tampilkan data milik user yang sedang login
        return $query->where('user_id', $user->id);
    }
}