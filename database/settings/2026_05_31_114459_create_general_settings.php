<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.name', 'CMS Skeleton');
        $this->migrator->add('general.description', 'Base scaffolding for creating bespoke websites with the TALL Stack.');
        $this->migrator->add('general.logo');
        $this->migrator->add('general.faviconLight');
        $this->migrator->add('general.faviconDark');
    }
};
