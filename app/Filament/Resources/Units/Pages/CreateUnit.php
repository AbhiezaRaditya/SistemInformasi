<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected static bool $canCreateAnother = true;

    public function getTitle(): string
    {
        return 'Tambah Unit';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Unit');
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Tambah & Buat Lagi');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Unit Berhasil Ditambahkan')
            ->success();
    }
}