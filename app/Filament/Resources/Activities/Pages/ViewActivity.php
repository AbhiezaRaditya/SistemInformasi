<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();

        return [
            EditAction::make()
                ->visible(function () use ($user) {
                    if ($user->hasRole('super_admin')) return true;
                    if ($user->hasRole('himpunan')) {
                        return in_array($this->record->status, ['draft', 'revisi']);
                    }
                    return false;
                }),

            DeleteAction::make()
                ->label('Hapus')
                ->visible(function () use ($user) {
                    if ($user->hasRole('super_admin')) return true;
                    if ($user->hasRole('himpunan')) {
                        return $this->record->status === 'draft';
                    }
                    return false;
                })
                ->requiresConfirmation()
                ->modalHeading('Hapus Aktivitas')
                ->modalDescription('Apakah kamu yakin ingin menghapus aktivitas ini? Tindakan ini tidak dapat dibatalkan.')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Aktivitas berhasil dihapus')
                        ->success()
                ),

            // === TAHAP 1: Review Proposal Awal ===
            // Muncul saat status masih 'pending' dan belum ada dokumen realisasi diunggah.
            Action::make('review_proposal')
                ->label('Tinjauan Proposal')
                ->color('primary')
                ->icon('heroicon-o-document-magnifying-glass')
                ->visible(fn ($record) =>
                    $record->status === 'pending'
                    && is_null($record->realization_file)
                    && $user->hasRole('kaprodi')
                )
                ->modalSubmitAction(fn ($action) => $action->color('warning'))
                ->form([
                    Select::make('status_baru')
                        ->label('Keputusan')
                        ->options([
                            'dalam_realisasi' => 'Setujui Proposal',
                            'reject' => 'Tolak Proposal',
                            'revisi' => 'Revisi Proposal',
                        ])
                        ->reactive()
                        ->required(),

                    Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi')
                        ->rows(3)
                        ->required()
                        ->visible(fn ($get) => $get('status_baru') === 'revisi'),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'status' => $data['status_baru'],
                        'catatan_revisi' => $data['status_baru'] === 'revisi' ? ($data['catatan_revisi'] ?? null) : null,
                    ]);

                    Notification::make()
                        ->title('Review Proposal Berhasil')
                        ->success()
                        ->send();
                }),

            // === TAHAP 2: Review Dokumen Realisasi ===
            // Muncul saat proposal sudah disetujui (status 'realisasi') dan himpunan sudah
            // mengunggah dokumen realisasi.
            Action::make('review_realisasi')
                ->label('Tinjauan Dokumen')
                ->color('primary')
                ->icon('heroicon-o-check-badge')
                ->visible(fn ($record) =>
                    $record->status === 'dalam_realisasi'
                    && ! is_null($record->realization_file)
                    && $user->hasRole('kaprodi')
                )
                ->modalSubmitAction(fn ($action) => $action->color('warning'))
                ->form([
                    Select::make('status_baru')
                        ->label('Keputusan')
                        ->options([
                            'completed' => 'Setujui & Selesaikan',
                            'reject' => 'Tolak Dokumen',
                            'revisi' => 'Revisi Dokumen',
                        ])
                        ->reactive()
                        ->required(),

                    Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi')
                        ->rows(3)
                        ->required()
                        ->visible(fn ($get) => $get('status_baru') === 'revisi'),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'status' => $data['status_baru'],
                        'catatan_revisi' => $data['status_baru'] === 'revisi' ? ($data['catatan_revisi'] ?? null) : null,
                    ]);

                    Notification::make()
                        ->title('Review Dokumen Berhasil')
                        ->success()
                        ->send();
                }),
        ];
    }
}