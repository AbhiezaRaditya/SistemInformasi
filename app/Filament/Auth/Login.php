<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autofocus()
            ->autocomplete();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    /**
     * Hilangkan title halaman login
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * Hilangkan heading "Masuk"
     */
    public function getHeading(): string
    {
        return '';
    }

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk');
    }

    protected function getSuccessNotification(): ?Notification
    {
        $username = $this->form->getState()['username'] ?? 'Pengguna';

        return Notification::make()
            ->title('Selamat Datang, ' . $username)
            ->body('Anda berhasil masuk ke sistem.')
            ->success();
    }

    protected function throwFailureValidationException(): never
    {
        $username = $this->form->getState()['username'] ?? null;

        $userExists = User::where('username', $username)->exists();

        if (! $userExists) {
            Notification::make()
                ->title('Username Tidak Ditemukan')
                ->body('Username yang Anda masukkan tidak terdaftar.')
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Password Salah')
                ->body('Password yang Anda masukkan tidak sesuai.')
                ->danger()
                ->send();
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.username' => '',
        ]);
    }
}