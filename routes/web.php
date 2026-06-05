<?php

use App\Http\Controllers\CmsPageController;
use App\Http\Controllers\NewsController;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Route;

Route::get('/testing', function () {
    dd(app(SitemapService::class)->fullSitemap());
});

Route::middleware('collection.accessible:news')->group(function () {
    Route::get('/aktuality', [NewsController::class, 'index'])->name('news.index');
    Route::get('/aktuality/{news:slug}', [NewsController::class, 'show'])->name('news.show');
});

Route::get('/{path?}', CmsPageController::class)
    ->where('path', '.*'); // RegExp for getting slashes

// Route::get('/{path?}', PageBuilderController::class)
//    ->name('page-builder.show');
