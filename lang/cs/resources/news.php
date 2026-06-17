<?php

return [
    'label' => 'aktualita',
    'plural_label' => 'aktuality',
    'navigation_label' => 'Aktuality',
    'sections' => [
        'basic' => [
            'title' => 'Vlastnosti příspěvku',
            'description' => 'Podrobnosti a metadata příspěvku.',
        ],
        'content' => [
            'title' => 'Obsah',
            'description' => 'Krátký perex do výpisu a hlavní text článku.',
        ],
        'media' => [
            'title' => 'Náhledový obrázek',
            'description' => 'Obrázek zobrazený ve výpisu aktualit a v náhledu při sdílení odkazu na sociálních sítích.',
        ],
        'publishing' => [
            'title' => 'Viditelnost',
            'description' => 'Kdo článek napsal a odkdy je veřejně viditelný na webu.',
        ],
    ],
    'fields' => [
        'title' => 'Nadpis',
        'slug' => 'URL slug',
        'slug_helper' => 'Jedinečný identifikátor použitý v adrese aktuality. Vyplní se automaticky podle nadpisu.',
        'excerpt' => 'Perex',
        'excerpt_helper' => 'Krátké shrnutí v jedné až dvou větách, které se zobrazí ve výpisu aktualit.',
        'content' => 'Obsah',
        'thumbnail' => 'Náhledový obrázek',
        'author' => 'Autor',
        'published_at' => 'Datum publikace',
        'published_at_helper' => 'Aktualita se na webu zobrazí od tohoto data a času.',
    ],
];
