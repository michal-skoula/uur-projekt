<?php

use App\Models\News;
use App\Models\Page;

return [
    'collections' => [
        'pages' => Page::class,
        'news' => News::class,
        // @collections-end [DO NOT TOUCH]
    ],

    'disabled' => [
        //
    ],

    'deprecated' => [
        //
    ],
];
