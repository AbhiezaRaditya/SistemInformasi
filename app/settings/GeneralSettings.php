<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public ?string $site_logo;

    public ?string $sidebar_color;

    public ?string $primary_color;

    public ?string $background_color;

    public ?string $header_style;

    public ?string $header_color;

    public ?string $font_family;

    public ?string $font_color;

    public ?string $button_color;

    public ?string $nav_color;

    public string $login_bg_color;

    public static function group(): string
    {
        return 'general';
    }
}