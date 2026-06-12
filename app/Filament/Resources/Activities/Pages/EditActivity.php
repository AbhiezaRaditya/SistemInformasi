<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;

    protected ?string $submitStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),

            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Aktivitas')
                ->modalDescription('Apakah Anda yakin ingin menghapus aktivitas ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Aktivitas berhasil dihapus')
                        ->body('Data aktivitas telah dihapus dari sistem.')
                        ->success()
                ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('draft')
                ->label('Simpan sebagai Draft')
                ->color('gray')
                ->action(function () {
                    $this->submitStatus = 'draft';
                    $this->save();
                })
                ->visible(
                    fn () =>
                    ! Auth::user()->hasRole(['kaprodi', 'super_admin']) &&
                    in_array($this->record->status, ['draft', 'revisi'])
                ),

            Action::make('send')
                ->label('Kirim ke Kaprodi')
                ->color('primary')
                ->action(function () {
                    $this->submitStatus = 'pending';
                    $this->save();
                })
                ->visible(
                    fn () =>
                    ! Auth::user()->hasRole(['kaprodi', 'super_admin']) &&
                    in_array($this->record->status, ['draft', 'revisi'])
                ),

            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary')
                ->action(fn () => $this->save())
                ->visible(
                    fn () => Auth::user()->hasRole(['kaprodi', 'super_admin'])
                ),

            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! Auth::user()->hasRole(['kaprodi', 'super_admin'])) {
            if (in_array($this->record->status, ['draft', 'revisi'])) {
                $data['status'] = $this->submitStatus;
            }
        }

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->record->refresh();

        // Pengurus Unit
        if (! Auth::user()->hasRole(['kaprodi', 'super_admin'])) {

            if ($this->submitStatus === 'draft') {
                Notification::make()
                    ->title('Aktivitas berhasil disimpan sebagai Draft')
                    ->success()
                    ->send();

                return;
            }

            if ($this->submitStatus === 'pending') {
                Notification::make()
                    ->title('Aktivitas berhasil dikirim ke Kaprodi')
                    ->body('Status aktivitas sekarang: Pending')
                    ->success()
                    ->send();

                return;
            }
        }

        // Kaprodi
        if (Auth::user()->hasRole(['kaprodi', 'super_admin'])) {

            if ($this->record->status === 'accept') {

                Notification::make()
                    ->title('Aktivitas berhasil disetujui')
                    ->success()
                    ->send();

            } elseif ($this->record->status === 'revisi') {

                Notification::make()
                    ->title('Aktivitas berhasil direvisi')
                    ->warning()
                    ->send();

            } elseif ($this->record->status === 'reject') {

                Notification::make()
                    ->title('Aktivitas berhasil ditolak')
                    ->danger()
                    ->send();

            } else {

                Notification::make()
                    ->title('Status saat ini: ' . $this->record->status)
                    ->warning()
                    ->send();
            }
        }
    }
}