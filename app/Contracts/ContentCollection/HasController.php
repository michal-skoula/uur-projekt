<?php

namespace App\Contracts\ContentCollection;

use App\Http\Controllers\Controller;

interface HasController
{
    /**
     * @returns class-string<Controller>
     */
    public function getController(): string;

    /**
     * @returns string Path prefix before the slug, allowing full path customization.
     */
    public static function getBasePath(): string;
}
