<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class NavMenuBuilderWidget extends Widget
{
    protected string $view = 'filament.widgets.nav-menu-builder-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isDiscovered = false;
}
