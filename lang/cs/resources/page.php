<?php

return [
    'label' => 'stránka',
    'plural_label' => 'stránky',
    'navigation_label' => 'Stránky',
    'home_prefix' => '',
    'sections' => [
        'configuration' => 'Nastavení stránky',
    ],
    'fields' => [
        'title' => 'Název',
        'slug' => 'URL slug',
        'slug_placeholder' => '<Homepage>',
        'slug_helper' => 'Jedinečný identifikátor použitý v adrese URL. Ponechte prázdné, aby se stránka stala domovskou.',
        'url' => 'Adresa URL',
        'parent' => 'Nadřazená stránka',
        'parent_placeholder' => 'Stránka nejvyšší úrovně',
        'parent_helper' => 'Umístění stránky v hierarchii webu.',
        'status' => 'Stav',
    ],
    'actions' => [
        'visit' => 'Zobrazit',
    ],
];
