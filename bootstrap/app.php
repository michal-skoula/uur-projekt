<?php

use App\Http\Middleware\EnsureCollectionIsAccessible;
use Filament\Facades\Filament;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'collection.accessible' => EnsureCollectionIsAccessible::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render a dedicated, Filament-styled error page for the admin panel.
        // Public requests fall through (return nothing) to Laravel's default
        // resolution, which renders the frontend `errors.*` views.
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if (! $request->is('admin', 'admin/*')) {
                return null;
            }

            // An unmatched admin route (e.g. a 404) never reaches the panel
            // middleware, so the current panel must be set for the Filament
            // layout to resolve its theme, branding, and dashboard URL.
            Filament::setCurrentPanel('admin');

            $status = $e->getStatusCode();
            $view = View::exists("errors.filament.{$status}")
                ? "errors.filament.{$status}"
                : 'errors.filament.'.($status < 500 ? '4xx' : '5xx');

            return response()->view($view, ['exception' => $e], $status, $e->getHeaders());
        });
    })->create();
