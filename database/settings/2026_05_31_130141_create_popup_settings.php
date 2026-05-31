<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('popup.enabled', false);
        $this->migrator->add('popup.stripeEnabled', false);
        $this->migrator->add('popup.stripeText', '');
        $this->migrator->add('popup.stripeCta');
        $this->migrator->add('popup.popupEnabled', false);
        $this->migrator->add('popup.popupImage');
        $this->migrator->add('popup.popupHeading');
        $this->migrator->add('popup.popupContent');
        $this->migrator->add('popup.popupCta');
    }
};
