<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Hanya menambahkan setelan warna default (abu-abu terang bawaan Tailwind)
        $this->migrator->add('general.login_bg_color', '#f3f4f6');
    }
};