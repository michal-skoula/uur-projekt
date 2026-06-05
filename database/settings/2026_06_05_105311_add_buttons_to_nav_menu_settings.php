<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('nav_menu.button_primary', null);
        $this->migrator->add('nav_menu.button_secondary', null);
    }
};
