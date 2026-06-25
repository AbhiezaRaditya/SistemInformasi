<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.sidebar_color', '#1e40af');
        $this->migrator->add('general.primary_color', '#2563eb');
    }

    public function down(): void
    {
        $this->migrator->delete('general.sidebar_color');
        $this->migrator->delete('general.primary_color');
    }
};