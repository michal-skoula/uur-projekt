<?php

namespace App\Contracts\ContentCollection;

interface HasItemPages extends HasController
{
    /**
     * @return string Slug identifiying the individual item of the collection,
     *                usually a singular variant of `getCollectionSlug()`. E.g. 'contact' or 'service'.
     */
    public static function getItemsSlug(): string;

    /**
     * @return string Method name used to display a given item's page from the provided Controller.
     *
     * @see getController The Controller.
     */
    public static function getControllerItemResolverMethodName(): string;
}
