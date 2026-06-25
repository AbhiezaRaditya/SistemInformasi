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

class ManageGeneralSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static string $settings = GeneralSettings::class;
    protected static ?string $navigationLabel = 'Pengaturan';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                // BARIS 1: INFORMASI UMUM (KIRI) & SIDEBAR (KANAN)
                Grid::make(2)
                    ->schema([
                        // KOTAK 1: INFORMASI UMUM
                        Section::make('Informasi Umum')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nama Situs')
                                    ->required()
                                    ->maxLength(255),

                                FileUpload::make('site_logo')
                                    ->label('Logo Situs')
                                    ->image() // Menandakan bahwa ini adalah file gambar
                                    // FITUR EDITOR & CROP TELAH DIHAPUS DARI SINI
                                    ->disk('public')
                                    ->directory('logos')
                                    ->visibility('public'),
                            ])
                            ->columnSpan(1),

                        // KOTAK 2: TEMA SIDEBAR & BACKGROUND
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
                            ->columnSpan(1),
                    ]), // Tutup Baris 1

                // BARIS 2: TEMA HEADER (KIRI) & TOMBOL (KANAN)
                Grid::make(2)
                    ->schema([
                        // KOTAK 3: TEMA HEADER
                        Section::make('Tema Warna — Header')
                            ->schema([
                                ToggleButtons::make('header_style')
                                    ->label('Gaya Header')
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

                                ColorPicker::make('header_color')
                                    ->label('Warna Header')
                                    ->default('#ffffff'),
                            ])
                            ->columnSpan(1),

                        // KOTAK 4: TEMA TOMBOL & AKSEN
                        Section::make('Tema Warna — Tombol & Aksen Sidebar')
                            ->description('Satu warna ini dipakai untuk semua tombol simpan dan efek aktif sidebar.')
                            ->schema([
                                ColorPicker::make('button_color')
                                    ->label('Warna Tombol & Aksen Sidebar')
                                    ->default('#2563eb'),
                            ])
                            ->columnSpan(1),
                    ]), // Tutup Baris 2

                // BARIS 3: TIPOGRAFI (FULL WIDTH)
                Grid::make(1)
                    ->schema([
                        Section::make('Tipografi')
                            ->description('Atur jenis dan warna huruf untuk nama situs/brand.')
                            ->schema([
                                Select::make('font_family')
                                    ->label('Jenis Font')
                                    ->options([
                                        'Nunito' => 'Nunito', 
                                        'Poppins' => 'Poppins', 
                                        'Inter' => 'Inter',
                                        'Roboto' => 'Roboto', 
                                        'Montserrat' => 'Montserrat', 
                                        'Plus Jakarta Sans' => 'Plus Jakarta Sans'
                                    ])
                                    ->default('Nunito')
                                    ->searchable()
                                    ->native(false),

                                ColorPicker::make('font_color')
                                    ->label('Warna Font (Brand)')
                                    ->default('#1e293b'),
                            ])
                            ->columns(2),
                    ]), // Tutup Baris 3
                    
            ]);
    }

    public function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Simpan Perubahan');
    }
}