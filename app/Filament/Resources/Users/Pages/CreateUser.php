<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static bool $canCreateAnother = true;

    public function getTitle(): string
    {
        return 'Tambah Pengguna';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Pengguna');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Tambah & Buat Lagi');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Pengguna Berhasil Ditambahkan')
            ->success();
    }
}