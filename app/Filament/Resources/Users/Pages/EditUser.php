<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return 'Ubah Pengguna';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),

            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Pengguna')
                ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Pengguna Berhasil Dihapus')
                        ->success()
                ),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->color('primary')
                ->action(fn () => $this->save()),

            $this->getCancelFormAction()
                ->label('Batal'),
        ];
    }
}