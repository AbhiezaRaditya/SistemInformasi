<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.font_family', 'Nunito');
        $this->migrator->add('general.font_color', '#1e293b');
    }

    public function down(): void
    {
        $this->migrator->delete('general.font_family');
        $this->migrator->delete('general.font_color');
    }
};