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
use Filament\Schemas\Components\Section; // Kembali ke Schema bawaan Filament v4+
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationLabel = 'Pengaturan';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasAnyRole')) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->columns(1)
                    ->columnSpan(1), // Kotak Kiri Atas

                Section::make('Tema Warna — Sidebar & Background')
                    ->schema([
                        ColorPicker::make('sidebar_color')
                            ->label('Warna Sidebar (Atas)')
                            ->default('#1e40af'),

                        ColorPicker::make('primary_color')
                            ->label('Warna Sidebar (Bawah)')
                            ->helperText('Dipakai untuk akhir gradasi sidebar saja.')
                            ->default('#2563eb'),

                        ColorPicker::make('background_color')
                            ->label('Warna Background')
                            ->default('#dbeafe'),
                    ])
                    ->columns(1) 
                    ->columnSpan(1), // Kotak Kanan Atas

                Section::make('Tema Warna — Header')
                    ->schema([
                        ToggleButtons::make('header_style')
                            ->label('Gaya Header')
                            ->inline()
                            ->options([
                                'solid' => 'Solid',
                                'glass' => 'Kaca (Blur)',
                            ])
                            ->default('solid'),

                        ColorPicker::make('header_color')
                            ->label('Warna Header')
                            ->default('#ffffff'),
                    ])
                    ->columns(1)
                    ->columnSpan(1), // Kotak Kiri Bawah

                Section::make('Tema Warna — Tombol & Aksen Sidebar')
                    ->description('Satu warna ini dipakai untuk tombol Simpan/Save di semua halaman, dan juga untuk efek hover/active menu di sidebar.')
                    ->schema([
                        ColorPicker::make('button_color')
                            ->label('Warna Tombol & Aksen Sidebar')
                            ->default('#2563eb'),
                    ])
                    ->columns(1)
                    ->columnSpan(1), // Kotak Kanan Bawah

                Section::make('Tipografi')
                    ->description('Atur jenis dan warna huruf untuk nama situs/brand dan menu sidebar.')
                    ->schema([
                        Select::make('font_family')
                            ->label('Jenis Font')
                            ->options([
                                'Nunito' => 'Nunito',
                                'Poppins' => 'Poppins',
                                'Inter' => 'Inter',
                                'Roboto' => 'Roboto',
                                'Montserrat' => 'Montserrat',
                                'Plus Jakarta Sans' => 'Plus Jakarta Sans',
                                'Lora' => 'Lora (Serif)',
                                'Playfair Display' => 'Playfair Display (Serif)',
                            ])
                            ->default('Nunito')
                            ->searchable()
                            ->native(false),

                        ColorPicker::make('font_color')
                            ->label('Warna Font (Brand)')
                            ->default('#1e293b'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // Melintang penuh di paling bawah
            ])
            ->columns(2); // MEMBAGI LAYOUT UTAMA MENJADI 2 KOLOM (Kiri & Kanan)
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan Perubahan');
    }
}