<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. Tombol Edit (Hanya muncul jika bukan Kaprodi)
            EditAction::make()
                ->hidden(fn () => Auth::user()->hasRole('kaprodi')),

            // 2. Tombol Review Dokumen (Hanya muncul untuk Kaprodi saat status pending)
            Action::make('review_realisasi')
                ->label('Review Dokumen')
                ->color('primary')
                ->icon('heroicon-o-check-badge')
                ->visible(fn ($record) => $record->status === 'pending' && Auth::user()->hasRole('kaprodi'))
                ->form([
                    Select::make('status_baru')
                        ->label('Keputusan')
                        ->options([
                            'completed' => 'Setujui Realisasi',
                            'reject' => 'Tolak',
                            'revisi' => 'Revisi',
                        ])
                        ->required(),
                    RichEditor::make('catatan_revisi')
                        ->label('Catatan Review')
                        ->requiredIf('status_baru', 'revisi'),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'status' => $data['status_baru'],
                        'catatan_revisi' => $data['catatan_revisi'] ?? null,
                    ]);

                    Notification::make()
                        ->title('Review Berhasil')
                        ->success()
                        ->send();
                }),
        ];
    }
}