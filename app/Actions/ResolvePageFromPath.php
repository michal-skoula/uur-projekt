<?php

namespace App\Actions;

use App\Models\Page;

// fixme: i dont like Action pattern, lets move to Service to not compete with filament.
//        also for the walker i want to implement this myself.
class ResolvePageFromPath
{
    public static function handle(?string $path): ?Page
    {
        $slug = $path === '' ? null : $path;

        return Page::query()->where('slug', $slug)->first();
    }
}
