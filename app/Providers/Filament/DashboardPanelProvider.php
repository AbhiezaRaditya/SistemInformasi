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

        $buttonColor = '#2563eb';
        $loginBgColor = '#ffffff';

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

            $buttonColor = $settings->button_color ?: $buttonColor;
            $loginBgColor = $settings->login_bg_color ?: $loginBgColor;
        } catch (\Throwable $e) {
            //
        }

        // =========================================================================
        // PERHITUNGAN UKURAN FONT OTOMATIS (LEBIH BESAR)
        // =========================================================================
        $nameLength = mb_strlen($siteName);

        if ($nameLength <= 15) {
            $desktopFontSize = '2.0rem';
            $mobileFontSize = '1.5rem';
        } elseif ($nameLength <= 22) {
            $desktopFontSize = '1.6rem';
            $mobileFontSize = '1.3rem';
        } else {
            $desktopFontSize = '1.3rem';
            $mobileFontSize = '1.1rem';
        }

        // =========================================================================
        // PERHITUNGAN LUMINANCE OTOMATIS (UNTUK KONTRAS WARNA TEKS)
        // =========================================================================

        // 1. Luminance Header (Icon Burger)
        $hex = ltrim($headerColor, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        $burgerColor = $luminance < 0.5 ? '#ffffff' : '#1e293b';

        // 2. Luminance Main Background
        $bgHex = ltrim($backgroundColor, '#');
        if (strlen($bgHex) === 3) {
            $bgHex = $bgHex[0] . $bgHex[0] . $bgHex[1] . $bgHex[1] . $bgHex[2] . $bgHex[2];
        }
        $bgR = hexdec(substr($bgHex, 0, 2));
        $bgG = hexdec(substr($bgHex, 2, 2));
        $bgB = hexdec(substr($bgHex, 4, 2));
        $bgLuminance = (0.299 * $bgR + 0.587 * $bgG + 0.114 * $bgB) / 255;
        $contentTextColor = $bgLuminance < 0.5 ? '#ffffff' : '#1e293b';
        $contentMutedColor = $bgLuminance < 0.5 ? '#cbd5e1' : '#64748b';

        // 3. Luminance Kotak Login
        $loginHex = ltrim($loginBgColor, '#');
        if (strlen($loginHex) === 3) {
            $loginHex = $loginHex[0] . $loginHex[0] . $loginHex[1] . $loginHex[1] . $loginHex[2] . $loginHex[2];
        }
        $loginR = hexdec(substr($loginHex, 0, 2));
        $loginG = hexdec(substr($loginHex, 2, 2));
        $loginB = hexdec(substr($loginHex, 4, 2));
        $loginLuminance = (0.299 * $loginR + 0.587 * $loginG + 0.114 * $loginB) / 255;
        $loginTextColor = $loginLuminance < 0.5 ? '#ffffff' : '#1e293b';

        // 4. Luminance Khusus Tombol Utama & Efek Aktif Sidebar
        $btnHex = ltrim($buttonColor, '#');
        if (strlen($btnHex) === 3) {
            $btnHex = $btnHex[0] . $btnHex[0] . $btnHex[1] . $btnHex[1] . $btnHex[2] . $btnHex[2];
        }
        $btnR = hexdec(substr($btnHex, 0, 2));
        $btnG = hexdec(substr($btnHex, 2, 2));
        $btnB = hexdec(substr($btnHex, 4, 2));
        $btnLuminance = (0.299 * $btnR + 0.587 * $btnG + 0.114 * $btnB) / 255;
        $buttonTextColor = $btnLuminance < 0.5 ? '#ffffff' : '#1e293b';

        // =========================================================================

        $fontUrlName = str_replace(' ', '+', $fontFamily);
        $googleFontUrl = "https://fonts.googleapis.com/css2?family={$fontUrlName}:wght@500;600;700;800&display=swap";

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
                'primary' => Color::hex($buttonColor),
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
                        /* CONTAINER BRAND */
                        .custom-brand-container {
                            display: flex !important;
                            align-items: center !important;
                            gap: 12px !important;
                            width: 100% !important;
                            flex-wrap: nowrap !important;
                        }

                        .custom-brand-logo {
                            max-height: 42px !important;
                            width: auto !important;
                            object-fit: contain !important;
                            flex-shrink: 0 !important;
                            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.08)) !important;
                        }

                        .fi-simple-layout .custom-brand-logo,
                        .fi-simple-main .custom-brand-logo {
                            max-height: 65px !important;
                            width: 65px !important;
                            height: 65px !important;
                        }

                        .custom-brand-text {
                            font-size: 1.35rem !important;
                            font-weight: 800 !important;
                            line-height: 1.2 !important;
                            width: max-content !important;
                            max-width: 180px !important;
                            display: block !important;
                            white-space: normal !important;
                            overflow: visible !important;
                            text-overflow: unset !important;
                            overflow-wrap: break-word !important;
                            word-break: normal !important;
                            letter-spacing: -0.01em !important;
                        }

                        /* SIDEBAR — GRADIENT */
                        .fi-sidebar {
                            background: linear-gradient(
                                165deg,
                                ' . e($sidebarColor) . ' 0%,
                                ' . e($primaryColor) . ' 100%
                            ) !important;
                            box-shadow: 4px 0 24px rgba(0,0,0,0.08) !important;
                            border-right: none !important;
                        }

                        html body .fi-sidebar-header {
                            background: transparent !important;
                            background-color: transparent !important;
                            border-bottom: none !important;
                            padding-top: 16px !important;
                            padding-bottom: 16px !important;
                            padding-left: 20px !important;
                            padding-right: 20px !important;
                            margin-bottom: 8px !important;
                        }

                        .fi-sidebar-nav {
                            padding: 0.75rem 1rem !important;
                            margin-top: 0.5rem !important;
                        }

                        .fi-sidebar .fi-sidebar-item {
                            margin-bottom: 8px !important;
                        }

                        /* RESET UTAMA SIDEBAR BUTTON/ANCHOR */
                        .fi-sidebar .fi-sidebar-item .fi-sidebar-item-btn,
                        .fi-sidebar .fi-sidebar-item > a {
                            border-radius: 0.75rem !important;
                            padding: 0.65rem 0.85rem !important;
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                        }

                        /* ========================================================================== */
                        /* SATU-SATUNYA SUMBER STYLING HOVER & ACTIVE SIDEBAR ITEM (JANGAN DIDUPLIKASI  */
                        /* DI theme.css ATAU DI TEMPAT LAIN — supaya tidak muncul efek "kotak dobel")   */
                        /* ========================================================================== */

                        /* 1. Reset total: matikan semua background/shadow bawaan di elemen pembungkus,
                           supaya hanya satu elemen (button/anchor) yang benar-benar menampilkan warna */
                        .fi-sidebar .fi-sidebar-item,
                        .fi-sidebar .fi-sidebar-item:hover,
                        .fi-sidebar .fi-sidebar-item:focus,
                        .fi-sidebar .fi-sidebar-item-btn,
                        .fi-sidebar .fi-sidebar-item > a {
                            background: transparent !important;
                            box-shadow: none !important;
                            outline: none !important;
                            border: none !important;
                            transform: none !important;
                            transition: all 0.2s ease !important;
                        }

                        /* 2. Hover: hanya aktif pada tombol/link, bukan pada pembungkus */
                        .fi-sidebar .fi-sidebar-item .fi-sidebar-item-btn:hover,
                        .fi-sidebar .fi-sidebar-item > a:hover {
                            background-color: ' . e($buttonColor) . 'cc !important;
                            transform: translateX(2px) !important;
                            box-shadow: none !important;
                            outline: none !important;
                        }

                        .fi-sidebar .fi-sidebar-item:hover .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-item:hover svg {
                            color: ' . e($buttonTextColor) . ' !important;
                            stroke: ' . e($buttonTextColor) . ' !important;
                        }

                        /* 3. Active (menu yang sedang dibuka): satu warna solid + border-left aksen,
                           digabung dalam satu blok saja supaya tidak tumpang tindih dengan blok lain */
                        .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
                        .fi-sidebar .fi-sidebar-item.fi-active > a,
                        .fi-sidebar .fi-sidebar-item:has(> .fi-active) .fi-sidebar-item-btn,
                        .fi-sidebar .fi-sidebar-item-btn[aria-current="page"],
                        .fi-sidebar a[aria-current="page"] {
                            background-color: ' . e($buttonColor) . ' !important;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
                            border-left: 4px solid #ffffff !important;
                            border-radius: 0.75rem !important;
                        }

                        .fi-sidebar .fi-sidebar-item.fi-active .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-item.fi-active svg,
                        .fi-sidebar .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label,
                        .fi-sidebar .fi-sidebar-item-btn[aria-current="page"] svg,
                        .fi-sidebar a[aria-current="page"] .fi-sidebar-item-label,
                        .fi-sidebar a[aria-current="page"] svg {
                            color: ' . e($buttonTextColor) . ' !important;
                            stroke: ' . e($buttonTextColor) . ' !important;
                            font-weight: 700 !important;
                        }

                        /* 4. Matikan background/shadow bawaan di wrapper icon (svg) supaya tidak ada
                           lingkaran/kotak tambahan menumpuk di atas highlight tombol.
                           Ditarget langsung ke tag svg sesuai struktur DOM Filament asli. */
                        .fi-sidebar svg.fi-sidebar-item-icon,
                        .fi-sidebar .fi-sidebar-item-btn svg.fi-sidebar-item-icon,
                        .fi-sidebar .fi-sidebar-item:hover svg.fi-sidebar-item-icon,
                        .fi-sidebar .fi-sidebar-item.fi-active svg.fi-sidebar-item-icon,
                        .fi-sidebar .fi-sidebar-item-btn[aria-current="page"] svg.fi-sidebar-item-icon {
                            background: transparent !important;
                            box-shadow: none !important;
                            outline: none !important;
                            border-radius: 0 !important;
                            padding: 0 !important;
                        }

                        /* ========================================================================== */

                        html, body, .fi-body, .fi-main, .fi-main-ctn {
                            background: ' . e($backgroundColor) . ' !important;
                        }

                        .fi-simple-layout, .fi-simple-layout body, .fi-simple-main-ctn {
                            background: ' . e($backgroundColor) . ' !important;
                        }

                        html body .fi-simple-main, html body main.fi-simple-main {
                            background-color: ' . e($loginBgColor) . ' !important;
                            border-radius: 1.25rem !important;
                            box-shadow: 0 20px 60px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06) !important;
                        }

                        /* TEKS HALAMAN LOGIN */
                        html body .fi-simple-main label:not(.fi-btn),
                        html body .fi-simple-main label:not(.fi-btn) span,
                        html body .fi-simple-main .fi-fo-field-wrp-label ,
                        html body .fi-simple-main .fi-fo-field-wrp-label span,
                        html body .fi-simple-main .fi-checkbox-label,
                        html body .fi-simple-main .fi-checkbox-label span,
                        html body .fi-simple-main .fi-simple-header-heading,
                        html body .fi-simple-main p,
                        html body .fi-simple-main header * {
                            color: ' . e($loginTextColor) . ' !important;
                        }

                        html body .fi-simple-main .custom-brand-text {
                            color: ' . e($loginTextColor) . ' !important;
                        }

                        html body .fi-simple-main a {
                            color: ' . e($loginTextColor) . ' !important;
                            text-decoration: underline !important;
                            text-underline-offset: 2px !important;
                        }

                        /* Link pembungkus logo+judul JANGAN diberi underline
                           (mencegah garis aneh muncul di bawah judul) */
                        html body .fi-simple-main header a,
                        html body .fi-simple-main header a:hover,
                        html body .fi-simple-main header a:focus {
                            text-decoration: none !important;
                        }

                        /* INPUT FIELD LOGIN */
                        html body .fi-simple-main .fi-input,
                        html body .fi-simple-main input {
                            border-radius: 0.65rem !important;
                            transition: box-shadow 0.2s ease, border-color 0.2s ease !important;
                        }

                        html body .fi-simple-main .fi-input:focus-within,
                        html body .fi-simple-main input:focus {
                            box-shadow: 0 0 0 3px ' . e($buttonColor) . '26 !important;
                        }

                        /* TOMBOL "MASUK" HALAMAN LOGIN */
                        html body .fi-simple-main form button[type="submit"],
                        html body .fi-simple-main .fi-btn.fi-color-primary,
                        html body .fi-simple-main button.fi-btn {
                            background: linear-gradient(135deg, ' . e($buttonColor) . ' 0%, ' . e($buttonColor) . 'e6 100%) !important;
                            color: ' . e($buttonTextColor) . ' !important;
                            border: none !important;
                            display: flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                            padding: 0.7rem 1.5rem !important;
                            border-radius: 0.65rem !important;
                            font-weight: 600 !important;
                            width: 100% !important;
                            box-shadow: 0 4px 12px ' . e($buttonColor) . '40 !important;
                            transition: all 0.2s ease !important;
                        }

                        html body .fi-simple-main form button[type="submit"]:hover,
                        html body .fi-simple-main .fi-btn.fi-color-primary:hover {
                            transform: translateY(-1px) !important;
                            box-shadow: 0 6px 16px ' . e($buttonColor) . '55 !important;
                        }

                        html body .fi-simple-main form button[type="submit"] *,
                        html body .fi-simple-main .fi-btn.fi-color-primary * {
                            color: ' . e($buttonTextColor) . ' !important;
                        }

                        /* DROPDOWN AKUN */
                        html body .fi-dropdown-panel:has(.custom-user-menu-list) {
                            width: max-content !important;
                            max-width: 18rem !important;
                            min-width: 15rem !important;
                            border-radius: 1rem !important;
                            box-shadow: 0 12px 32px rgba(0,0,0,0.14), 0 2px 8px rgba(0,0,0,0.06) !important;
                            overflow: hidden !important;
                        }

                        html body ul.custom-user-menu-list {
                            flex-direction: column !important;
                            width: 100% !important;
                            list-style: none !important;
                        }

                        html body .custom-user-menu-header {
                            display: block !important;
                            width: 100% !important;
                            box-sizing: border-box !important;
                        }

                        html body .custom-user-menu-row-top {
                            display: flex !important;
                            flex-direction: column !important;
                            align-items: stretch !important;
                            width: 100% !important;
                            gap: 1rem !important;
                        }

                        html body .custom-user-menu-identity {
                            display: flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            width: 100% !important;
                            flex-wrap: nowrap !important;
                            gap: 0.75rem !important;
                        }

                        html body .custom-user-menu-name-email {
                            display: block !important;
                            flex: 1 1 auto !important;
                            min-width: 0 !important;
                            overflow: hidden !important;
                        }

                        html body .custom-user-menu-name-email h4,
                        html body .custom-user-menu-name-email p {
                            display: block !important;
                            width: 100% !important;
                            white-space: nowrap !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                            margin: 0 !important;
                        }

                        html body .custom-user-menu-badges {
                            display: flex !important;
                            flex-flow: row wrap !important;
                            align-items: flex-start !important;
                            width: 100% !important;
                            margin-top: 0.25rem !important;
                            gap: 0.375rem !important;
                        }

                        html body .custom-user-menu-badges span {
                            flex: 0 0 auto !important;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.06) !important;
                            transition: transform 0.15s ease !important;
                        }

                        html body .custom-user-menu-badges span:hover {
                            transform: translateY(-1px) !important;
                        }

                        /* WIDGET DAN BADGE */
                        html body .fi-main-ctn table .fi-ta-col-wrp,
                        html body .fi-main-ctn table .fi-ta-col-wrp * {
                            width: max-content !important;
                            max-width: fit-content !important;
                        }

                        html body .fi-main-ctn .fi-badge {
                            display: inline-flex !important;
                            width: max-content !important;
                            max-width: fit-content !important;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
                        }

                        /* CARD / SECTION */
                        html body .fi-main-ctn .fi-section,
                        html body .fi-main-ctn .fi-ta-ctn,
                        html body .fi-main-ctn .fi-wi-stats-overview-stat {
                            border-radius: 1rem !important;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06) !important;
                            transition: box-shadow 0.2s ease !important;
                        }

                        html body .fi-main-ctn .fi-section:hover,
                        html body .fi-main-ctn .fi-wi-stats-overview-stat:hover {
                            box-shadow: 0 4px 12px rgba(0,0,0,0.06), 0 2px 4px rgba(0,0,0,0.04) !important;
                        }

                      
                        html body .fi-main-ctn .fi-btn.fi-color-primary,
                        html body .fi-main-ctn .fi-ac-action-button.fi-color-primary,
                        html body .fi-main-ctn .fi-ac-btn-action.fi-color-primary,
                        html body .fi-main-ctn .fi-page-actions button.fi-color-primary,
                        html body .fi-main-ctn .fi-page-header-actions .fi-btn.fi-color-primary,
                        html body .fi-form-actions button.fi-color-primary,
                        html body .fi-modal .fi-btn.fi-color-primary {
                            background-color: ' . e($buttonColor) . ' !important;
                            color: ' . e($buttonTextColor) . ' !important;
                            width: auto !important;
                            display: inline-flex !important;
                            padding: 0.5rem 1.2rem !important;
                            border-radius: 0.5rem !important;
                            transition: all 0.2s ease !important;
                            box-shadow: 0 4px 12px ' . e($buttonColor) . '30 !important;
                        }

                        html body .fi-main-ctn .fi-btn.fi-color-primary:hover,
                        html body .fi-main-ctn .fi-ac-action-button.fi-color-primary:hover,
                        html body .fi-main-ctn .fi-ac-btn-action.fi-color-primary:hover,
                        html body .fi-main-ctn .fi-page-header-actions .fi-btn.fi-color-primary:hover,
                        html body .fi-modal .fi-btn.fi-color-primary:hover {
                            transform: translateY(-1px) !important;
                            box-shadow: 0 6px 16px ' . e($buttonColor) . '50 !important;
                            background-color: ' . e($buttonColor) . ' !important;
                        }

                        html body .fi-main-ctn .fi-btn.fi-color-primary *,
                        html body .fi-main-ctn .fi-form-actions button.fi-color-primary *,
                        html body .fi-main-ctn .fi-page-header-actions .fi-btn.fi-color-primary * {
                            color: ' . e($buttonTextColor) . ' !important;
                        }

                        /* Tombol non-primary (danger/success/gray/warning/info) tetap dikasih
                           bentuk konsisten (radius, padding, shadow) tapi TIDAK dipaksa warnanya */
                        html body .fi-main-ctn .fi-btn:not(.fi-color-primary),
                        html body .fi-form-actions button:not(.fi-color-primary) {
                            border-radius: 0.5rem !important;
                            transition: all 0.2s ease !important;
                        }

                        html body .fi-main-ctn .fi-btn:not(.fi-color-primary):hover,
                        html body .fi-form-actions button:not(.fi-color-primary):hover {
                            transform: translateY(-1px) !important;
                        }

                        /* TABS NAVIGATION */
                        html body .fi-tabs-item.fi-active,
                        html body button.fi-tabs-item[aria-selected="true"],
                        html body .fi-tabs-item.fi-active *,
                        html body button.fi-tabs-item[aria-selected="true"] * {
                            color: ' . e($buttonColor) . ' !important;
                            border-color: ' . e($buttonColor) . ' !important;
                        }

                        html body .fi-ta-pagination-list .fi-active,
                        html body .fi-ta-pagination-list .fi-active button,
                        html body .fi-ta-pagination-list .fi-active span {
                            background-color: ' . e($buttonColor) . ' !important;
                            color: ' . e($buttonTextColor) . ' !important;
                            border-radius: 0.5rem !important;
                        }

                        html body .fi-logo, html body a .fi-logo, html body .fi-sidebar-header .fi-logo {
                            background: transparent !important;
                            box-shadow: none !important;
                        }

                        /* BURGER ICON */
                        html body .fi-topbar-open-sidebar-btn,
                        html body .fi-topbar-open-sidebar-btn svg {
                            color: ' . e($burgerColor) . ' !important;
                            stroke: ' . e($burgerColor) . ' !important;
                            fill: none !important;
                        }

                        @media (max-width: 1024px) {
                            html body .custom-brand-text { color: ' . e($fontColor) . ' !important; }
                            html body .fi-simple-main .custom-brand-text { color: ' . e($loginTextColor) . ' !important; }
                        }

                        html body .custom-brand-text,
                        html body .fi-topbar .custom-brand-text,
                        html body .fi-sidebar-header .custom-brand-text,
                        html body div.custom-brand-text,
                        html body span.custom-brand-text,
                        html body .fi-sidebar-nav,
                        html body .fi-sidebar-nav a,
                        html body .fi-sidebar-nav span,
                        html body .fi-sidebar-nav button,
                        html body .fi-sidebar-nav div,
                        html body .fi-sidebar .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-group-label {
                        font-family: "' . e($fontFamily) . '", sans-serif !important;
                            color: ' . e($fontColor) . ' !important; 
                        }

                        /* STANDBY STATE UNTUK MENU TIDAK AKTIF */
                        html body .fi-sidebar .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-label,
                        html body .fi-sidebar .fi-sidebar-group-label,
                        html body .fi-sidebar .fi-sidebar-item:not(.fi-active) svg {
                            color: ' . e($fontColor) . ' !important;
                            stroke: ' . e($fontColor) . ' !important;
                        }

                        .fi-header-heading { color: ' . e($contentTextColor) . ' !important; font-weight: 700 !important; letter-spacing: -0.01em !important; }
                        .fi-breadcrumbs .fi-breadcrumbs-item,
                        .fi-breadcrumbs .fi-breadcrumbs-item a,
                        .fi-breadcrumbs .fi-breadcrumbs-item span {
                            color: ' . e($contentMutedColor) . ' !important;
                        }

                        ' . (
                            $headerStyle === 'glass'
                                ? '.fi-topbar { background: ' . e($headerColor) . '80 !important; backdrop-filter: blur(15px) !important; -webkit-backdrop-filter: blur(15px) !important; border-bottom: 1px solid rgba(255,255,255,.25) !important; box-shadow: none !important; } .fi-topbar nav, .fi-topbar-ctn { background: transparent !important; }'
                                : '.fi-topbar { background: ' . e($headerColor) . ' !important; background-color: ' . e($headerColor) . ' !important; box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important; }'
                        ) . '

                        /* ============================================================ */
                        /* TOMBOL KUSTOM — WARNA TETAP (TIDAK IKUT WARNA NAV/PRIMARY)    */
                        /* ============================================================ */
                        .custom-draft-btn,
                        .custom-submit-btn,
                        .custom-cancel-btn,
                        .custom-edit-btn,
                        .fi-form-actions .custom-draft-btn,
                        .fi-form-actions .custom-submit-btn,
                        .fi-form-actions .custom-cancel-btn,
                        .fi-form-actions .custom-edit-btn,
                        button.custom-draft-btn,
                        button.custom-submit-btn,
                        button.custom-cancel-btn,
                        button.custom-edit-btn {
                            background-image: none !important;
                            background: none !important;
                            background-color: initial !important;
                        }

                        .custom-draft-btn,
                        .fi-form-actions .custom-draft-btn,
                        button.custom-draft-btn {
                            background-color: #dc2626 !important;
                            color: #ffffff !important;
                            border: none !important;
                            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3) !important;
                            transition: all 0.2s ease !important;
                        }
                        .custom-draft-btn:hover,
                        .fi-form-actions .custom-draft-btn:hover,
                        button.custom-draft-btn:hover {
                            background-color: #b91c1c !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4) !important;
                        }
                        .custom-draft-btn *,
                        .fi-form-actions .custom-draft-btn *,
                        button.custom-draft-btn * {
                            color: #ffffff !important;
                        }

                        .custom-cancel-btn,
                        .fi-form-actions .custom-cancel-btn,
                        button.custom-cancel-btn {
                            background-color: #ffffff !important;
                            color: #374151 !important;
                            border: 1px solid #d1d5db !important;
                            box-shadow: none !important;
                            transition: all 0.2s ease !important;
                        }
                        .custom-cancel-btn:hover,
                        .fi-form-actions .custom-cancel-btn:hover,
                        button.custom-cancel-btn:hover {
                            background-color: #f9fafb !important;
                            border-color: #9ca3af !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
                        }
                        .custom-cancel-btn *,
                        .fi-form-actions .custom-cancel-btn *,
                        button.custom-cancel-btn * {
                            color: #374151 !important;
                        }

                        .custom-edit-btn,
                        .fi-form-actions .custom-edit-btn,
                        button.custom-edit-btn {
                            background-color: #eab308 !important;
                            color: #000000 !important;
                            border: none !important;
                            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.3) !important;
                            transition: all 0.2s ease !important;
                        }
                        .custom-edit-btn:hover,
                        .fi-form-actions .custom-edit-btn:hover,
                        button.custom-edit-btn:hover {
                            background-color: #ca8a04 !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(234, 179, 8, 0.4) !important;
                        }
                        .custom-edit-btn *,
                        .fi-form-actions .custom-edit-btn *,
                        button.custom-edit-btn * {
                            color: #000000 !important;
                        }

                        .custom-submit-btn,
                        .fi-form-actions .custom-submit-btn,
                        button.custom-submit-btn {
                            background-color: #16a34a !important;
                            color: #ffffff !important;
                            border: none !important;
                            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3) !important;
                            transition: all 0.2s ease !important;
                        }
                        .custom-submit-btn:hover,
                        .fi-form-actions .custom-submit-btn:hover,
                        button.custom-submit-btn:hover {
                            background-color: #15803d !important;
                            transform: translateY(-2px) !important;
                            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.4) !important;
                        }
                        .custom-submit-btn *,
                        .fi-form-actions .custom-submit-btn *,
                        button.custom-submit-btn * {
                            color: #ffffff !important;
                        }

                        @media (max-width: 640px) {
                            .custom-draft-btn,
                            .custom-submit-btn,
                            .custom-cancel-btn,
                            .custom-edit-btn,
                            .fi-form-actions .custom-draft-btn,
                            .fi-form-actions .custom-submit-btn,
                            .fi-form-actions .custom-cancel-btn,
                            .fi-form-actions .custom-edit-btn {
                                width: 100% !important;
                                justify-content: center !important;
                                margin-bottom: 0.5rem !important;
                            }
                        }

                        /* TOMBOL SIGN OUT SIDEBAR */
                        html body .custom-sidebar-signout-btn{background-color:#dc2626!important;color:#fff!important;border:none!important;font-size:.8rem!important;padding:.45rem .75rem!important;box-shadow:0 4px 12px rgba(220,38,38,.3)!important;transition:all .15s ease!important}
                        html body .custom-sidebar-signout-btn:hover{background-color:#b91c1c!important;box-shadow:0 6px 16px rgba(220,38,38,.4)!important}
                        html body .custom-sidebar-signout-btn *{color:#fff!important;stroke:#fff!important}
                        html body .custom-sidebar-signout-btn svg{width:1rem!important;height:1rem!important}

                        /* ============================================================ */
                        /* DROPDOWN MENU PROFIL
                        /* ============================================================ */
                        .custom-user-menu-trigger{border-radius:9999px;padding:2px;transition:box-shadow .2s ease}
                        .custom-user-menu-trigger:hover{box-shadow:0 0 0 4px rgba(0,0,0,.06)}
                        html body .custom-user-menu-list{width:16rem;border-radius:1.25rem!important;overflow:hidden!important;border:1px solid rgba(0,0,0,.06)!important;box-shadow:0 20px 40px rgba(0,0,0,.16),0 4px 10px rgba(0,0,0,.06)!important}
                        .custom-user-menu-header{padding:1rem 1.25rem}
                        .custom-user-menu-identity{display:flex;align-items:center;gap:.75rem}
                        .custom-user-menu-name-email{flex:1;min-width:0;overflow:hidden}
                        .custom-user-menu-eyebrow{margin:0 0 2px;font-size:.625rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#94a3b8}
                        .custom-user-menu-name-email h4{margin:0;font-size:.875rem;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
                        .custom-user-menu-name-email p{margin:2px 0 0;font-size:.75rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
                        .custom-user-menu-badges{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.75rem;padding-top:.65rem;border-top:1px solid rgba(0,0,0,.06)}
                        html body .custom-badge-pill{border-radius:9999px!important;padding:.15rem .65rem!important;font-weight:600!important;font-size:.7rem!important}
                        html body .custom-badge-pill:hover{transform:translateY(-1px)}

                        html body .custom-signout-item,
                        html body .custom-signout-item>*{
                            background-color:#dc2626!important;color:#fff!important;width:100%!important;
                            display:flex!important;align-items:center!important;gap:.4rem!important;
                            font-weight:600!important;font-size:.7rem!important;padding:.3rem .6rem!important;
                            border-radius:.5rem!important;transition:background-color .15s ease!important;
                        }
                        html body .custom-signout-item{margin:.25rem .35rem!important;overflow:hidden!important}
                        html body .custom-signout-item:hover,
                        html body .custom-signout-item:hover>*{background-color:#b91c1c!important}
                        html body .custom-signout-item svg{color:#fff!important;stroke:#fff!important;width:.9rem!important;height:.9rem!important}

                        /* ============================================================ */
                        /*  BRAND LOGIN  */
                        /* ============================================================ */

                        .fi-simple-main header {
                            display: flex !important;
                            justify-content: center !important;
                            width: 100% !important;
                            margin-bottom: 0.5rem !important;
                            padding: 0 !important;
                        }

                        .fi-simple-main header > a {
                            display: flex !important;
                            flex-direction: row !important;
                            flex-wrap: nowrap !important;
                            align-items: center !important;
                            justify-content: center !important;
                            gap: 0.5rem !important;
                            text-decoration: none !important;
                            width: auto !important;
                            max-width: 100% !important;
                            padding: 0 !important;
                        }

                        .fi-simple-main .custom-brand-container {
                            display: flex !important;
                            flex-direction: row !important;
                            flex-wrap: nowrap !important;
                            align-items: center !important;
                            justify-content: center !important;
                            gap: 0.5rem !important;
                            width: auto !important;
                            margin: 0 auto !important;
                        }

                        .fi-simple-main .custom-brand-logo {
                            flex-shrink: 0 !important;
                            max-height: 65px !important;
                            width: 65px !important;
                            height: 65px !important;
                            display: block !important;
                        }

                        .fi-simple-main .custom-brand-text {
                            flex: 1 1 auto !important;
                            white-space: normal !important;
                            word-break: break-word !important;
                            overflow-wrap: break-word !important;
                            font-size: 1.25rem !important; /* fallback */
                            text-align: left !important;
                            line-height: 1.4 !important; /* lebih lega */
                            max-width: 100% !important;
                            min-width: 0 !important;
                            width: auto !important;
                            display: block !important;
                        }

                        /* Desktop */
                        @media (min-width: 641px) {
                            .fi-simple-main .custom-brand-logo {
                                width: 50px !important;
                                height: 50px !important;
                                max-height: 50px !important;
                            }
                            .fi-simple-main .custom-brand-text {
                                font-size: ' . e($desktopFontSize) . ' !important;
                                font-weight: 900 !important;
                            }
                            .fi-simple-main header {
                                margin-bottom: 0.5rem !important;
                            }
                        }

                        /* Mobile */
                        @media (max-width: 640px) {
                            .fi-simple-main .custom-brand-logo {
                                width: 46px !important;
                                height: 46px !important;
                                max-height: 46px !important;
                            }
                            .fi-simple-main .custom-brand-text {
                                font-size: ' . e($mobileFontSize) . ' !important;
                            }
                            .fi-simple-main header {
                                margin-bottom: 0.2rem !important;
                            }
                            .fi-simple-main form .fi-fo-component-ctn,
                            .fi-simple-main .fi-form > .grid {
                                gap: 0.75rem !important;
                            }
                            .fi-simple-main .fi-fo-field-wrp {
                                margin-bottom: 0 !important;
                            }
                            .fi-simple-main .fi-fo-field-wrp-label {
                                margin-bottom: 0.25rem !important;
                            }
                            .fi-simple-main .fi-checkbox-label {
                                margin-bottom: 0 !important;
                            }
                            .fi-simple-main form button[type="submit"] {
                                margin-top: 0.5rem !important;
                            }
                        }
                    </style>
                ')
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
            ->authMiddleware([Authenticate::class])
            ->plugins([FilamentShieldPlugin::make()]);
    }
}