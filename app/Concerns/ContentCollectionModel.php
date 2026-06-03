<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

abstract class ContentCollectionModel extends Model implements ContentCollectionItem
{
    //    abstract public static function getIndexPage():
    abstract public function getPermalink(): string;
}
