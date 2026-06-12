<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    public function getTitle(): string
    {
        return 'Ubah Kategori Kegiatan';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Detail'),

            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Kategori Kegiatan')
                ->modalDescription('Apakah Anda yakin ingin menghapus kategori kegiatan ini?')
                ->modalSubmitActionLabel('Ya, Hapus')
                ->successNotification(
                    Notification::make()
                        ->title('Kategori Kegiatan Berhasil Dihapus')
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

            $this->getCancelFormAction()->label('Batal'),
        ];
    }
}