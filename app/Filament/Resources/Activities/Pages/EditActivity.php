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

            // TOMBOL HAPUS DI HALAMAN EDIT — Menggunakan logika status draft + kepemilikan data (Owner-based)
            DeleteAction::make()
                ->label('Hapus')
                ->visible(function () {
                    $user = Auth::user();
                    $record = $this->record;

                    // Validasi awal data dan status draft
                    if (! $record || $record->status !== 'draft') {
                        return false;
                    }

                    // Kondisi 1: Jika Super Admin, diijinkan hapus
                    if ($user->hasRole('super_admin')) {
                        return true;
                    }

                    // Kondisi 2: Jika user_id data ini cocok dengan akun Himpunan yang sedang login
                    if ($record->user_id === $user->id) {
                        return true;
                    }

                    return false;
                })
                ->modalHeading('Hapus Aktivitas')
                ->modalDescription('Apakah Anda yakin ingin menghapus aktivitas ini? Tindakan ini tidak dapat dibatalkan.')
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
            // Tombol Simpan sebagai Draft → MERAH
            Action::make('draft')
                ->label('Simpan sebagai Draft')
                ->extraAttributes([
                    'style' => 'background-color: #dc2626 !important; color: #ffffff !important; border: none !important;'
                ])
                ->action(function () {
                    $this->submitStatus = 'draft';
                    $this->save();
                })
                ->visible(
                    fn () =>
                    ! Auth::user()->hasRole(['kaprodi', 'super_admin']) &&
                    in_array($this->record->status, ['draft', 'revisi'])
                ),

            // Tombol Kirim ke Kaprodi → MENGIKUTI WARNA PRIMARY (Spatie)
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

            // Tombol Simpan Perubahan → MENGIKUTI WARNA PRIMARY (Spatie) untuk kaprodi/super_admin
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary')
                ->action(fn () => $this->save())
                ->visible(
                    fn () => Auth::user()->hasRole(['kaprodi', 'super_admin'])
                ),

            // Tombol Batal → tetap putih (default)
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