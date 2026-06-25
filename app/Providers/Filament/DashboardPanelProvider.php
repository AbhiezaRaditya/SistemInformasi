<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Widgets\ActivityCategoryChart;
use App\Filament\Widgets\CustomAccountWidget;
use App\Filament\Widgets\KaprodiStatsOverview;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UserVerificationTable;
use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $siteName = 'Sistem Informasi';
        $siteLogo = asset('images/logo-unud.png');

        $sidebarColor = '#1e40af';
        $primaryColor = '#2563eb';
        $backgroundColor = '#dbeafe';

        $headerStyle = 'solid';
        $headerColor = '#ffffff';

        $fontFamily = 'Nunito';
        $fontColor = '#1e293b';
        
        // Default value untuk button & aksen sidebar
        $buttonColor = '#2563eb'; 

        try {
            $settings = app(GeneralSettings::class);

            $siteName = $settings->site_name ?: $siteName;

            $siteLogo = $settings->site_logo
                ? asset('storage/' . $settings->site_logo)
                : $siteLogo;

            $sidebarColor = $settings->sidebar_color ?: $sidebarColor;
            $primaryColor = $settings->primary_color ?: $primaryColor;
            $backgroundColor = $settings->background_color ?: $backgroundColor;

            $headerStyle = $settings->header_style ?? 'solid';
            $headerColor = $settings->header_color ?: '#ffffff';

            $fontFamily = $settings->font_family ?: $fontFamily;
            $fontColor = $settings->font_color ?: $fontColor;
            
            // Ambil dari database settings jika ada
            $buttonColor = $settings->button_color ?: $buttonColor; 
        } catch (\Throwable $e) {
            //
        }

        // Hitung kecerahan warna header untuk menentukan warna icon hamburger yang kontras
        $hex = ltrim($headerColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Header gelap (luminance rendah) -> icon terang. Header terang -> icon gelap.
        $burgerColor = $luminance < 0.5 ? '#ffffff' : '#1e293b';

        // URL Google Fonts dinamis sesuai font yang dipilih
        $fontUrlName = str_replace(' ', '+', $fontFamily);
        $googleFontUrl = "https://fonts.googleapis.com/css2?family={$fontUrlName}:wght@600;700;800;900&display=swap";

        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->brandName($siteName)
            ->brandLogo(new HtmlString('
                <div class="custom-brand-container">
                    <img
                        src="' . $siteLogo . '"
                        alt="Logo"
                        class="custom-brand-logo"
                    >
                    <span class="custom-brand-text">
                        ' . e($siteName) . '
                    </span>
                </div>
            '))
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->darkMode(false)
            ->login(Login::class)
            ->colors([
                'primary' => Color::Sky,
                'gray' => Color::Zinc,
                'danger' => Color::Red,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => new HtmlString('
                    <link rel="preconnect" href="https://fonts.googleapis.com">
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="' . $googleFontUrl . '" rel="stylesheet">

                    <style>

                        .fi-sidebar {
                            background: linear-gradient(
                                180deg,
                                ' . e($sidebarColor) . ' 0%,
                                ' . e($primaryColor) . ' 100%
                            ) !important;
                        }

                        /* Menu item sidebar — hover & active disesuaikan dengan button_color (opasitas diatur kontras) */
                        .fi-sidebar .fi-sidebar-item:hover,
                        .fi-sidebar .fi-sidebar-item:hover .fi-sidebar-item-button,
                        .fi-sidebar .fi-sidebar-item:hover a {
                            background: ' . e($buttonColor) . '26 !important; /* Opacity 15% (#XX26) */
                        }

                        .fi-sidebar .fi-sidebar-item.fi-active,
                        .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-button,
                        .fi-sidebar .fi-sidebar-item.fi-active a {
                            background: ' . e($buttonColor) . '40 !important; /* Opacity 25% (#XX40) */
                            border-left: 4px solid ' . e($buttonColor) . ' !important; /* Efek border aktif */
                        }

                        html,
                        body,
                        .fi-body,
                        .fi-main,
                        .fi-main-ctn {
                            background: ' . e($backgroundColor) . ' !important;
                        }

                        /* Customisasi Warna Tombol Utama (Primary Buttons Filament) */
                        .fi-btn.fi-color-primary, 
                        .fi-btn.fi-color-action,
                        button[type="submit"].fi-btn {
                            background-color: ' . e($buttonColor) . ' !important;
                        }
                        .fi-btn.fi-color-primary:hover, 
                        .fi-btn.fi-color-action:hover,
                        button[type="submit"].fi-btn:hover {
                            filter: brightness(90%);
                        }

                        /* Hilangkan kotak/background bawaan wrapper logo Filament */
                        html body .fi-logo,
                        html body a .fi-logo,
                        html body .fi-sidebar-header .fi-logo {
                            background: transparent !important;
                            background-color: transparent !important;
                            box-shadow: none !important;
                        }

                        /* Icon hamburger menu (toggle sidebar) — kontras otomatis dengan warna header */
                        html body .fi-topbar svg,
                        html body .fi-topbar button svg,
                        html body .fi-topbar button svg path,
                        html body .fi-topbar button svg line,
                        html body .fi-topbar-open-sidebar-btn,
                        html body .fi-topbar-open-sidebar-btn svg,
                        html body [x-data*="sidebar"] svg,
                        html body button[aria-label*="sidebar" i] svg,
                        html body button[aria-label*="menu" i] svg,
                        html body button[aria-label*="navigation" i] svg,
                        html body .fi-icon-btn svg {
                            color: ' . e($burgerColor) . ' !important;
                            stroke: ' . e($burgerColor) . ' !important;
                            fill: none !important;
                        }

                        /* Header sidebar (termasuk versi mobile) menyatu transparan dengan sidebar */
                        html body .fi-sidebar-header {
                            background: transparent !important;
                            background-color: transparent !important;
                            border-bottom: none !important;
                        }

                        @media (max-width: 1024px) {
                            html body .custom-brand-text {
                                color: ' . e($fontColor) . ' !important;
                            }
                        }

                        /* Font — jenis huruf & warna untuk brand DAN seluruh teks menu sidebar */
                        html body .fi-topbar .custom-brand-text,
                        html body .fi-sidebar-header .custom-brand-text,
                        html body div.custom-brand-text,
                        html body span.custom-brand-text {
                            font-family: "' . e($fontFamily) . '", sans-serif !important;
                            color: ' . e($fontColor) . ' !important;
                        }

                        html body .fi-sidebar-nav,
                        html body .fi-sidebar-nav a,
                        html body .fi-sidebar-nav span,
                        html body .fi-sidebar-nav button,
                        html body .fi-sidebar-nav div,
                        html body .fi-sidebar .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-group-label {
                            font-family: "' . e($fontFamily) . '", sans-serif !important;
                        }

                        /* Warna teks menu sidebar (Dashboard, Pengaturan, dst) */
                        html body .fi-sidebar .fi-sidebar-item .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-item:hover .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-item svg,
                        html body .fi-sidebar .fi-sidebar-item.fi-active svg,
                        html body .fi-sidebar .fi-sidebar-item:hover svg {
                            color: ' . e($fontColor) . ' !important;
                            stroke: ' . e($fontColor) . ' !important;
                        }

                        ' . (
                            $headerStyle === 'glass'
                                ? '

                        .fi-topbar {
                            background: ' . e($headerColor) . '80 !important;
                            backdrop-filter: blur(15px) !important;
                            -webkit-backdrop-filter: blur(15px) !important;
                            border-bottom: 1px solid rgba(255,255,255,.25) !important;
                            box-shadow: none !important;
                        }

                        .fi-topbar nav,
                        .fi-topbar-ctn {
                            background: transparent !important;
                        }

                        '
                                : '

                        .fi-topbar {
                            background: ' . e($headerColor) . ' !important;
                            background-color: ' . e($headerColor) . ' !important;
                        }

                        '
                        ) . '

                    </style>
                ')
            )
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
            ->widgets([
                CustomAccountWidget::class,
                StatsOverview::class,
                KaprodiStatsOverview::class,
                ActivityCategoryChart::class,
                UserVerificationTable::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                ValidateCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}