<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section; 
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Notifications\Notification;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static string $settings = GeneralSettings::class;
    
    // Diubah menjadi "Pengaturan Halaman"
    protected static ?string $navigationLabel = 'Pengaturan Halaman';

    // Opsional: Mengubah judul halaman di header browser / halaman utama admin
    protected static ?string $title = 'Pengaturan Halaman';

    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // 1. Jika Super Admin / Admin, langsung izinkan
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        // 2. Cek apakah user memiliki permission apa pun yang mengandung kata kunci pengaturan/setting
        return $user->getAllPermissions()->contains(function ($permission) {
            $name = strtolower($permission->name);
            return str_contains($name, 'setting') 
                || str_contains($name, 'general') 
                || str_contains($name, 'manage_general_settings');
        });
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                // BARIS 1: INFORMASI UMUM
                Section::make('Informasi Umum')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('Nama Situs')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('site_logo')
                            ->label('Logo Situs')
                            ->image()
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public'),
                    ])
                    ->columns(2),

                // BARIS 2: WARNA & PENGATURAN LAINNYA
                Grid::make(4)
                    ->schema([
                        Section::make('Sidebar (Atas)')
                            ->schema([
                                ColorPicker::make('sidebar_color')
                                    ->hiddenLabel()
                                    ->default('#1e40af'),
                            ]),

                        Section::make('Sidebar (Bawah)')
                            ->schema([
                                ColorPicker::make('primary_color')
                                    ->hiddenLabel()
                                    ->default('#2563eb'),
                            ]),

                        Section::make('Background')
                            ->schema([
                                ColorPicker::make('background_color')
                                    ->hiddenLabel()
                                    ->default('#dbeafe'),
                            ]),

                        Section::make('Header')
                            ->schema([
                                ColorPicker::make('header_color')
                                    ->hiddenLabel()
                                    ->default('#ffffff'),
                            ]),

                        Section::make('Tombol & Aksen Sidebar')
                            ->schema([
                                ColorPicker::make('button_color')
                                    ->hiddenLabel()
                                    ->default('#2563eb'),
                            ]),

                        Section::make('Font Judul & Sidebar')
                            ->schema([
                                ColorPicker::make('font_color')
                                    ->hiddenLabel()
                                    ->default('#1e293b'),
                            ]),

                        Section::make('Background Login')
                            ->schema([
                                ColorPicker::make('login_bg_color')
                                    ->hiddenLabel()
                                    ->default('#f3f4f6'),
                            ]),

                        Section::make('Gaya Header')
                            ->schema([
                                ToggleButtons::make('header_style')
                                    ->hiddenLabel()
                                    ->inline()
                                    ->options([
                                        'solid' => 'Solid',
                                        'glass' => 'Kaca (Blur)',
                                    ])
                                    ->default('solid')
                                    ->colors([
                                        'solid' => 'gray',
                                        'glass' => 'gray',
                                    ])
                                    ->extraAttributes([
                                        'class' => 'custom-toggle-buttons'
                                    ]),
                            ]),

                        Section::make('Jenis Font')
                            ->schema([
                                Select::make('font_family')
                                    ->hiddenLabel()
                                    ->options([
                                        'Nunito' => 'Nunito',
                                        'Poppins' => 'Poppins',
                                        'Inter' => 'Inter',
                                        'Roboto' => 'Roboto',
                                        'Montserrat' => 'Montserrat',
                                        'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                                    ])
                                    ->default('Nunito')
                                    ->searchable()
                                    ->native(false),
                            ]),
                    ]),

            ]);
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Simpan Pengaturan');
    }

    public function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengaturan Halaman berhasil diubah')
            ->body('Silakan refresh halaman untuk melihat perubahan.');
    }
}