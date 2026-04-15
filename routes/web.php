<?php

use App\Http\Controllers\PageBuilderController;
use Illuminate\Support\Facades\Route;

Route::get('/{path?}', PageBuilderController::class)
    ->name('page-builder.show');
