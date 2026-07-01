<?php

namespace App\Filament\Resources\Activities;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Activities\Pages\CreateActivity;
use App\Filament\Resources\Activities\Pages\EditActivity;
use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Pages\ViewActivity;
use App\Filament\Resources\Activities\Schemas\ActivityForm;
use App\Filament\Resources\Activities\Schemas\ActivityInfolist;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;
use App\Models\Activity;
use BackedEnum;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\EditAction as ActionsEditAction;
use Filament\Actions\ViewAction as ActionsViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section; // HANYA gunakan impor ini
use Filament\Schemas\Components\Section as ComponentsSection;
use Illuminate\Support\HtmlString;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

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
                ActionsViewAction::make(),

                // TOMBOL EDIT PINTAR
                ActionsEditAction::make()
                    ->visible(function ($record) {
                        if (Auth::user()->hasRole('kaprodi')) return false;
                        if ($record->status === 'draft') return true;
                        if ($record->status === 'revisi' && empty($record->realization_file)) return true;
                        return false;
                    }),

              

                // TOMBOL REVIEW (KAPRODI)
                ActionsAction::make('approval')
                    ->label('Review')
                    ->color('warning')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->hasRole(['kaprodi', 'super_admin']))
                    ->form([
                        Select::make('status')
                            ->label('Keputusan')
                            ->options(function ($record) {
                                if (empty($record->realization_file)) {
                                    return ['revisi' => 'Revisi Proposal', 'accept' => 'Setujui Proposal', 'reject' => 'Tolak Proposal'];
                                }
                                return ['revisi' => 'Revisi Dokumen', 'completed' => 'Setujui & Selesaikan', 'reject' => 'Tolak Dokumen'];
                            })
                            ->required()->live(),
                        Textarea::make('catatan_revisi')
                            ->label('Catatan Review')
                            ->required(fn ($get) => in_array($get('status'), ['revisi', 'reject']))
                            ->visible(fn ($get) => in_array($get('status'), ['revisi', 'reject'])),
                    ])
                    ->action(function (array $data, $record): void {
                        $newStatus = $data['status'];
                        if ($newStatus === 'accept') $newStatus = 'dalam_realisasi';
                        
                        $record->update([
                            'status' => $newStatus,
                            'catatan_revisi' => $data['catatan_revisi'] ?? null,
                        ]);
                    }),

                // TOMBOL LAMPIRKAN (MAHASISWA)
                ActionsAction::make('lampirkan_realisasi')
                    ->label('Lampirkan')
                    ->color('success')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(fn ($record) => in_array($record->status, ['dalam_realisasi', 'revisi']) && !Auth::user()->hasRole('kaprodi'))
                    ->form([
                        ComponentsSection::make('Instruksi Revisi') // Section yang benar
                            ->visible(fn ($record) => filled($record->catatan_revisi))
                            ->schema([
                                Placeholder::make('catatan')
                                    ->label('')
                                    ->content(fn ($record) => new HtmlString('<div style="color: #ef4444; font-weight: bold; background: #fee2e2; padding: 10px; border-radius: 5px;">' . $record->catatan_revisi . '</div>')),
                            ]),
                        FileUpload::make('realization_file')
                            ->label('Unggah Dokumen Realisasi')
                            ->directory('realisasi')
                            ->multiple()
                            ->default(fn ($record) => $record->realization_file)
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'realization_file' => $data['realization_file'],
                            'status' => 'pending', 
                        ]);
                    })

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
        if ($user->hasRole('super_admin')) return $query;

        if ($user->hasRole('kaprodi')) {
            return $query->where(function (Builder $q) use ($user) {
                $q->whereHas('unit', fn ($unitQuery) => $unitQuery->where('study_program_id', $user->study_program_id))
                  ->where('status', '!=', 'draft')
                  ->orWhere('user_id', $user->id);
            });
        }
        return $query->where('user_id', $user->id);
    }
}