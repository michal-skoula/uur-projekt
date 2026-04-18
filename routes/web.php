<?php

use App\Http\Controllers\PageBuilderController;
use Illuminate\Support\Facades\Route;

Route::get('/testing', function () {
    \App\Services\SitemapTreeBuilderService::handle();
});

Route::get('/{path?}', PageBuilderController::class)
    ->name('page-builder.show');
