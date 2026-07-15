<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.nav_color', '#ffffff');
    }

    public function down(): void
    {
        $this->migrator->delete('general.nav_color');
    }
};