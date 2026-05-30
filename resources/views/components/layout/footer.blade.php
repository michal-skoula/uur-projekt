@php
    $navColumns = [
        [
            'heading' => 'Taneční centrum',
            'rows' => [
                ['title' => 'O nás',        'url' => '/o-nas'],
                ['title' => 'Výuka tance',   'url' => '/vyuka-tance'],
                ['title' => 'Rozvrh hodin', 'url' => '/rozvrh'],
                ['title' => 'Kontakt',      'url' => '/kontakt'],
            ],
        ],
        [
            'heading' => 'Nabídka',
            'rows' => [
                ['title' => 'Kurzy pro děti',    'url' => '/kurzy-deti'],
                ['title' => 'Kurzy pro dospělé', 'url' => '/kurzy-dospeli'],
                ['title' => 'Taneční soustředění', 'url' => '/soustedeni'],
                ['title' => 'Představení',       'url' => '/predstaveni'],
            ],
        ],
        [
            'heading' => 'Informace',
            'rows' => [
                ['title' => 'Aktuality',    'url' => '/aktuality'],
                ['title' => 'Galerie',      'url' => '/galerie'],
                ['title' => 'Ceník',        'url' => '/cenik'],
                ['title' => 'GDPR',         'url' => '/gdpr'],
            ],
        ],
    ];

    $socials = [
        [
            'url'   => 'https://www.facebook.com/dcpp.cz',
            'label' => 'Facebook',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M9 8H6v4h3v12h5V12h3.642L18 8h-4V6.333C14 5.378 14.192 5 15.115 5H18V0h-3.808C10.596 0 9 1.583 9 4.615V8z"/></svg>',
        ],
        [
            'url'   => 'https://www.instagram.com/dcpp.cz',
            'label' => 'Instagram',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>',
        ],
    ];
@endphp

<footer id="colophon" class="section bg-slate-950 md:rounded-t-[3rem] rounded-t-[1.5rem] lg:pb-4!" role="contentinfo">
    <div class="max-content-width">

        {{-- Title, socials, motto --}}
        <div class="lg:mb-12 mb-8">
            <div class="mb-2">
                <p class="h2">Dance Center<br>Petry Parvoničové</p>

                <div class="flex gap-1 mt-2">
                    @foreach ($socials as $social)
                        <a
                            href="{{ $social['url'] }}"
                            class="first-of-type:pl-0 p-2 text-gray hover:text-accent-mint transition-colors"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['label'] }}"
                        >
                            {!! $social['icon'] !!}
                        </a>
                    @endforeach
                </div>
            </div>

            <p class="max-w-[50ch]">
                Tancujeme, protože tanec je naše vášeň.<br>
                Pojď tančit s námi!
            </p>
        </div>

        {{-- Navigation columns --}}
        <div class="w-full flex gap-12 flex-wrap">
            @foreach ($navColumns as $column)
                <div class="flex flex-col gap-2">
                    <h3 class="h6 font-medium mx-1 mt-1 mb-1.5">{{ $column['heading'] }}</h3>
                    <ul class="m-0 list-none">
                        @foreach ($column['rows'] as $row)
                            <li class="list-none m-0 mb-0.5">
                                <a
                                    href="{{ $row['url'] }}"
                                    class="block p-1 hover:text-accent-mint hover:underline transition-colors duration-300 ease-in-out"
                                >
                                    {{ $row['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

    </div>
</footer>

<section id="attribution" class="section py-0! mt-3 bg-primary-darkest">
    <div class="max-w-screen-xl mx-auto p-6 flex flex-wrap gap-x-8 gap-y-2 items-center justify-between text-base">
        <p>&copy; Dance Center Petry Parvoničové, {{ date('Y') }}</p>
        <div class="flex flex-wrap gap-x-6 gap-y-2">
            <a href="mailto:michal@skoula.com?subject=Chyba na webu dcpp.cz" target="_blank" class="underline!">
                Nahlásit chybu
            </a>
            <p>
                Web vytvořil
                <a href="https://skoula.com" target="_blank" rel="nofollow" class="underline! text-accent-yellow">
                    Michal Škoula
                </a>.
            </p>
        </div>
    </div>
</section>
