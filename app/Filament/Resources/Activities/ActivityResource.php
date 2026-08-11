<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\CreateActivity;
use App\Filament\Resources\Activities\Pages\EditActivity;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Pages\ViewActivity;
use App\Filament\Resources\Activities\Schemas\ActivityForm;
use App\Filament\Resources\Activities\Schemas\ActivityInfolist;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;
use App\Models\Activity;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|UnitEnum|null $navigationGroup = 'Aktivitas';

    protected static ?string $navigationLabel = 'Aktivitas Kemahasiswaan';
    protected static ?string $modelLabel = 'Aktivitas Kemahasiswaan';
    protected static ?string $pluralModelLabel = 'Aktivitas Kemahasiswaan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table)
            ->actions([
                ViewAction::make()
                    ->label('Detail')
                    ->color('gray'),

                EditAction::make()
                    ->label('Edit')
                    ->color('warning')
                    ->link()
                    ->visible(function ($record) {
                        $user = Auth::user();

                        // Super Admin / User tanpa batasan unit/prodi bisa edit kapan saja
                        if ($user->studyPrograms()->doesntExist() && $user->units()->doesntExist()) return true;

                        // Jika pemilik data dan status draft/revisi
                        if ($record->user_id === $user->id) {
                            if ($record->status === 'draft') return true;
                            if ($record->status === 'revisi' && empty($record->realization_file)) return true;
                        }

                        return false;
                    }),

                DeleteAction::make()
                    ->label('Hapus')
                    ->color('danger')
                    ->link()
                    ->visible(function ($record) {
                        $user = Auth::user();
                        if ($record->status !== 'draft') return false;

                        // Super Admin bisa hapus
                        if ($user->studyPrograms()->doesntExist() && $user->units()->doesntExist()) return true;

                        // Pemilik data bisa hapus saat draft
                        if ($record->user_id === $user->id) return true;

                        return false;
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Aktivitas')
                    ->modalDescription('Apakah kamu yakin ingin menghapus aktivitas ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus'),

                Action::make('approval')
                    ->label('Tinjauan')
                    ->color('warning')
                    ->icon('heroicon-o-check-circle')
                    ->link()
                    ->visible(function ($record) {
                        $user = Auth::user();

                        // Memeriksa status pending dan hak akses 'Approve:Activity' (sesuai Shield)
                        return $record->status === 'pending' && ($user->can('Approve:Activity') || $user->can('approve_activity'));
                    })
                    ->form([
                        Select::make('status')
                            ->label('Keputusan')
                            ->options(function ($record) {
                                if (empty($record->realization_file)) {
                                    return [
                                        'revisi' => 'Revisi Proposal',
                                        'accept' => 'Setujui Proposal',
                                        'reject' => 'Tolak Proposal'
                                    ];
                                }
                                return [
                                    'revisi' => 'Revisi Dokumen',
                                    'completed' => 'Setujui & Selesaikan',
                                    'reject' => 'Tolak Dokumen'
                                ];
                            })
                            ->required()
                            ->live(),
                        Textarea::make('catatan_revisi')
                            ->label('Catatan Review')
                            ->required(fn ($get) => in_array($get('status'), ['revisi', 'reject']))
                            ->visible(fn ($get) => in_array($get('status'), ['revisi', 'reject'])),
                    ])
                    ->action(function (array $data, $record): void {
                        $newStatus = $data['status'];
                        if ($newStatus === 'accept') {
                            $newStatus = 'dalam_realisasi';
                        }

                        $record->update([
                            'status' => $newStatus,
                            'catatan_revisi' => $data['catatan_revisi'] ?? null,
                        ]);

                        $statusLabel = match ($newStatus) {
                            'revisi' => 'Revisi',
                            'dalam_realisasi' => 'Dalam Realisasi',
                            'reject' => 'Ditolak',
                            'completed' => 'Selesai Realisasi',
                            default => ucfirst(str_replace('_', ' ', $newStatus)),
                        };

                        Notification::make()
                            ->title('Tinjauan berhasil ditambahkan')
                            ->body("Status: {$statusLabel}")
                            ->success()
                            ->send();
                    })
                    ->successNotification(null),

                Action::make('lampirkan_realisasi')
                    ->label('Lampirkan')
                    ->color('success')
                    ->icon('heroicon-o-paper-clip')
                    ->link()
                    ->visible(function ($record) {
                        $user = Auth::user();

                        // Jika user adalah Kaprodi murni (punya prodi tapi tidak punya unit spesifik), tidak boleh lampirkan
                        if ($user->studyPrograms()->exists() && $user->units()->doesntExist() && empty($user->can('bypass_activity_rules'))) {
                            return false;
                        }

                        if ($record->status === 'dalam_realisasi') return true;
                        if ($record->status === 'revisi' && filled($record->realization_file)) return true;

                        return false;
                    })
                    ->form([
                        Section::make('Instruksi Revisi')
                            ->visible(fn ($record) => filled($record->catatan_revisi))
                            ->schema([
                                Placeholder::make('catatan')
                                    ->label('')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #ef4444; font-weight: bold; background: #fee2e2; padding: 10px; border-radius: 5px;">' . $record->catatan_revisi . '</div>')),
                            ]),
                        FileUpload::make('realization_file')
                            ->label('Unggah Dokumen Realisasi')
                            ->disk('public')
                            ->directory('realisasi')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->multiple()
                            ->default(fn ($record) => $record->realization_file)
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'realization_file' => $data['realization_file'],
                            'status' => 'pending',
                        ]);

                        Notification::make()
                            ->title('Lampiran berhasil ditambahkan')
                            ->success()
                            ->send();
                    })
                    ->successNotification(null),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            'view' => ViewActivity::route('/{record}'),
            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Cek apakah user memiliki hak akses view_any / viewAny
        $hasViewAny = $user->can('ViewAny:Activity') || $user->can('view_any_activity') || $user->can('view_any_activities');

        // Ambil daftar ID Program Studi & Unit milik user
        $studyProgramIds = $user->studyPrograms()->pluck('study_programs.id');
        $unitIds = $user->units()->pluck('units.id');

        // 1. Jika Super Admin / User tingkat atas (tidak terikat ke prodi/unit)
        if ($studyProgramIds->isEmpty() && $unitIds->isEmpty()) {
            // Jika punya izin ViewAny, boleh lihat semua. Jika tidak, hanya miliknya sendiri.
            if ($hasViewAny || $user->hasRole('super_admin')) {
                return $query;
            }
            return $query->where('user_id', $user->id);
        }

        // 2. Jika Kaprodi (punya Program Studi, tapi tidak punya Unit)
        if ($studyProgramIds->isNotEmpty() && $unitIds->isEmpty()) {
            return $query->where(function (Builder $q) use ($user, $studyProgramIds, $hasViewAny) {
                $unitQueryCheck = function ($unitQuery) use ($studyProgramIds) {
                    $unitQuery->whereIn('study_program_id', $studyProgramIds);
                };

                if ($hasViewAny) {
                    $q->whereHas('unit', $unitQueryCheck)
                      ->where('status', '!=', 'draft')
                      ->orWhere('user_id', $user->id);
                } else {
                    $q->whereHas('unit', $unitQueryCheck)
                      ->where('user_id', $user->id);
                }
            });
        }

        // 3. Jika Himpunan / Unit (punya Unit)
        if ($unitIds->isNotEmpty()) {
            $query->whereIn('unit_id', $unitIds);

            if (!$hasViewAny) {
                $query->where('user_id', $user->id);
            }

            return $query;
        }

        // Default fallback: Hanya tampilkan data milik sendiri
        return $query->where('user_id', $user->id);
    }
}