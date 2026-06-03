<?php

use App\Providers\AppServiceProvider;
use App\Providers\CmsServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\PageBuilderServiceProvider;

return [
    AppServiceProvider::class,
    CmsServiceProvider::class,
    AdminPanelProvider::class,
    PageBuilderServiceProvider::class,
];
