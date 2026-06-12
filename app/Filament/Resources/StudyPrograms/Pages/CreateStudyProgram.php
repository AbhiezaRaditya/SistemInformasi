<?php

namespace App\Filament\Resources\StudyPrograms\Pages;

use App\Filament\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStudyProgram extends CreateRecord
{
    protected static string $resource = StudyProgramResource::class;

    // Menghilangkan tombol "Create & create another"
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Tambah Program Studi';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Program Studi');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Program Studi Berhasil Ditambahkan')
            ->success();
    }
}