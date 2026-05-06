<?php

use App\Http\Controllers\PageBuilderController;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Route;

Route::get('/testing', function () {
    dd(app(SitemapService::class)->fullSitemap());
});

Route::get('/{path?}', PageBuilderController::class)
    ->name('page-builder.show');
