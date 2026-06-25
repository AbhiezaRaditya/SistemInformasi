<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.background_color', '#dbeafe');
    }

    public function down(): void
    {
        $this->migrator->delete('general.background_color');
    }
};