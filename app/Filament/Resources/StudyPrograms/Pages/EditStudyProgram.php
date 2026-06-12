<?php

namespace App\Filament\Resources\StudyPrograms\Pages;

use App\Filament\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStudyProgram extends EditRecord
{
    protected static string $resource = StudyProgramResource::class;

    public function getTitle(): string
    {
        return 'Ubah Program Studi';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),

            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Program Studi')
                ->modalDescription('Apakah Anda yakin ingin menghapus program studi ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Program Studi Berhasil Dihapus')
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