<?php

return [
    'navigation_label' => 'Popup',
    'page_title' => 'Nastavení Popupu',

    'general' => [
        'title' => 'Zobrazení',
        'description' => 'Globální přepínač pro celý systém reklamních sdělení. Pokud je vypnuto, nezobrazí se ani lišta, ani modální okno.',
        'enabled' => [
            'label' => 'Reklamní sdělení',
            'helper_text' => 'Zapnout / Vypnout zobrazování reklamních sdělení',
        ],
    ],

    'stripe' => [
        'title' => 'Oznamovací pruh',
        'description' => 'Tenký pruh připnutý ke spodní části každé stránky. Vhodné pro krátká a časově omezená oznámení, jako jsou otevřené přihlášky nebo nadcházející události.',
        'enabled' => [
            'label' => 'Zobrazovat pruh',
        ],
        'text' => [
            'label' => 'Zpráva',
            'placeholder' => 'např. Otevřeli jsme přihlašování na lekce tance pro rok 2025/2026.',
        ],
        'cta' => [
            'title' => 'Tlačítko výzvy k akci',
        ],
    ],

    'popup' => [
        'title' => 'Modální okno',
        'description' => 'Okno, které se zobrazí poté, co návštěvník posune stránku dolů. Podporuje obrázek, nadpis a formátovaný text — vhodné pro akce nebo oznámení, která potřebují více prostoru.',
        'enabled' => [
            'label' => 'Zobrazovat modální okno',
        ],
        'image' => [
            'label' => 'Obrázek',
            'hint' => 'Zobrazuje se v horní části okna. Doporučený tvar fotky: čtvercové / více na výšku.',
        ],
        'heading' => [
            'label' => 'Nadpis',
        ],
        'content' => [
            'label' => 'Obsah',
        ],
        'cta' => [
            'title' => 'Tlačítko výzvy k akci',
        ],
    ],
];
