<?php

return [
    'navigation_label' => 'Obecné',
    'page_title' => 'Obecná nastavení',

    'site_identity' => [
        'title' => 'Identita webu',
        'description' => 'Základní údaje, které identifikují váš web v prohlížeči, vyhledávačích a sdílených odkazech.',
        'name' => [
            'label' => 'Název webu',
        ],
        'description_field' => [
            'label' => 'Popis webu',
            'hint' => 'Zobrazuje se ve výsledcích vyhledávačů jako krátký popis stránky.',
        ],
        'logo' => [
            'label' => 'Logo',
            'hint' => 'Zobrazuje se v záhlaví webu a e-mailových šablonách. Doporučená velikost: 200 × 60 px.',
        ],
    ],

    'favicon' => [
        'title' => 'Favicon',
        'description' => 'Malá ikonka zobrazovaná na záložkách prohlížeče a v záložkách. Nahrajte varianty pro světlé i tmavé téma.',
        'light' => [
            'label' => 'Favicon — světlý režim',
            'hint' => 'Zobrazuje se, pokud návštěvník používá světlé režim (light mode) prohlížeče nebo operačního systému.',
        ],
        'dark' => [
            'label' => 'Favicon — tmavý režim',
            'hint' => 'Zobrazuje se při tmavém režimu (dark mode). Pokud není nahráno, použije se favicon z světlého režimu.',
        ],
    ],
];
