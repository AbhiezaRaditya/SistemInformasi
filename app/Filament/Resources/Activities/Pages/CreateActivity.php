<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    protected static bool $canCreateAnother = false;

    public ?string $buttonStatus = 'pending';

    public function getTitle(): string
    {
        return 'Tambah Kegiatan';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $unitId = Auth::user()->unit_id;

        if (! $unitId) {
            Notification::make()
                ->title('Gagal')
                ->body('Akun Anda belum memiliki unit. Hubungi administrator.')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['status'] = $this->buttonStatus;
        $data['user_id'] = Auth::id();
        $data['unit_id'] = $unitId;

        return $data;
    }

    /**
     * Menonaktifkan notifikasi bawaan Filament ("Saved")
     */
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    /**
     * Notifikasi custom setelah berhasil membuat data
     */
    protected function afterCreate(): void
    {
        if ($this->buttonStatus === 'draft') {
            Notification::make()
                ->title('Aktivitas berhasil disimpan sebagai Draft')
                ->success()
                ->send();

            return;
        }

        Notification::make()
            ->title('Aktivitas berhasil dikirim ke Kaprodi')
            ->body('Status aktivitas sekarang: Pending')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [

            Action::make('draft')
                ->label('Simpan Draft')
                ->color('gray')
                ->action(function () {
                    $this->buttonStatus = 'draft';
                    $this->create();
                }),

            Action::make('submit')
                ->label('Kirim ke Kaprodi')
                ->color('primary')
                ->action(function () {
                    $this->buttonStatus = 'pending';
                    $this->create();
                }),

            Action::make('cancel')
                ->label('Batal')
                ->color('danger')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}