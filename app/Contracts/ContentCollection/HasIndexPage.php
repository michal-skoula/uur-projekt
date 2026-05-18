<?php

namespace App\Contracts\ContentCollection;

interface HasIndexPage extends HasController
{
    /**
     * @return string Slug identifying the collection, e.g. 'contacts' or 'services'.
     */
    public static function getIndexSlug(): string;

    /**
     * @return string Method name used to display the index page from the provided Controller.
     *
     * @see getController The Controller.
     */
    public static function getControllerIndexResolverMethodName(): string;
}
