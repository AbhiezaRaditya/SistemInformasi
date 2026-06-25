<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.button_color', '#2563eb');
    }

    public function down(): void
    {
        $this->migrator->delete('general.button_color');
    }
};