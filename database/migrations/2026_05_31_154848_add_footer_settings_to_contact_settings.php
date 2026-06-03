<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('contact', function (): void {
            $this->migrator->add('contact.footerNav', []);
            $this->migrator->add('contact.errorReportButton', []);
        });
    }
};
