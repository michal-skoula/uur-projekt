<?php

namespace App\Contracts\ContentCollection;

use Illuminate\Database\Eloquent\Model;

abstract class ContentCollection
{
    /**
     * @return class-string<Model> The model class being represented by this ContentCollection.
     */
    abstract public static function getModel(): string;

    /**
     * @return string|int Column in the table which uniquely identifies a given `ContentCollection`
     *                    item. Can be its ID or any other more human readable uniquely constrained column.
     */
    abstract public static function getUniquelyIdentifyingColumnName(): int|string;
}
