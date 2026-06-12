<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected static bool $canCreateAnother = true;

    // Mengubah judul halaman
    public function getTitle(): string
    {
        return 'Tambah Kategori Kegiatan';
    }

    /**
     * Kustomisasi Notifikasi Sukses Setelah Berhasil Menambahkan Kategori
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Kategori kegiatan berhasil ditambahkan')
            ->success();
    }

    // Tombol utama
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Kategori');
    }

    // Tombol kedua
    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Tambah & Buat Lagi');
    }
}