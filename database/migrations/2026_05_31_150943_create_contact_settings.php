<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('contact', function (): void {
            $this->migrator->add('contact.socials', [
                ['icon' => 'fab fa-facebook',  'name' => 'Facebook',  'url' => 'https://www.facebook.com/dcpp.cz'],
                ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'url' => 'https://www.instagram.com/dcpp.cz'],
            ]);
        });
    }
};
