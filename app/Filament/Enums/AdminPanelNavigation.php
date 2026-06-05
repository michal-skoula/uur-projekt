<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;

enum AdminPanelNavigation implements HasLabel
{
    case General;

    case Website;

    case Settings;

    public function getLabel(): string
    {
        return match ($this) {
            self::General => __('panel-navigation.labels.general'),
            self::Website => __('panel-navigation.labels.website'),
            self::Settings => __('panel-navigation.labels.settings'),
        };
    }
}
