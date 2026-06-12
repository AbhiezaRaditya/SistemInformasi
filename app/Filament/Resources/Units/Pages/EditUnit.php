<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    public function getTitle(): string
    {
        return 'Ubah Unit';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),

            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Unit')
                ->modalDescription('Apakah Anda yakin ingin menghapus unit ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Unit Berhasil Dihapus')
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