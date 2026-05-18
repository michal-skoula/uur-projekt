<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class EnsureCollectionIsAccessible
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $slug): Response
    {
        if (in_array($slug, Config::array('content-collections.disabled'), strict: true)) {
            abort(404);
        }

        return $next($request);
    }
}
