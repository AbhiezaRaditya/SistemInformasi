<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Settings\GeneralSettings;
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
        $buttonColor = '#2563eb';
        $buttonTextColor = '#ffffff';
        
        try {
            $settings = app(GeneralSettings::class);
            $buttonColor = $settings->button_color ?? '#2563eb';
            
            // Hitung luminance untuk menentukan warna teks
            $hex = ltrim($buttonColor, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
            $buttonTextColor = $luminance < 0.5 ? '#ffffff' : '#1e293b';
            
        } catch (\Throwable $e) {
            // Gunakan default jika error
        }

        return [
            // Tombol Simpan Draft → TETAP MERAH
            Action::make('draft')
                ->label('Simpan Draft')
                ->extraAttributes([
                    'style' => 'background-color: #dc2626 !important; color: #ffffff !important; border: none !important;'
                ])
                ->action(function () {
                    $this->buttonStatus = 'draft';
                    $this->create();
                }),

            // Tombol Kirim ke Kaprodi → MENGIKUTI WARNA SETTING
            Action::make('submit')
                ->label('Kirim ke Kaprodi')
                ->extraAttributes([
                    'style' => "background-color: {$buttonColor} !important; color: {$buttonTextColor} !important; border: none !important;"
                ])
                ->action(function () {
                    $this->buttonStatus = 'pending';
                    $this->create();
                }),

            // Tombol Batal → TETAP PUTIH
            Action::make('cancel')
                ->label('Batal')
                ->extraAttributes([
                    'style' => 'background-color: #ffffff !important; color: #374151 !important; border: 1px solid #d1d5db !important;'
                ])
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}