<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Page;
use App\Settings\ContactSettings;
use App\Settings\GeneralSettings;
use App\Settings\NavMenuSettings;
use App\Settings\PopupSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WebsiteSeeder extends Seeder
{
    private const string DISK = 'public';

    private const string ASSETS_PATH = __DIR__.'/assets';

    public function run(): void
    {
        $mediaIds = $this->seedMedia();
        $pageIds = $this->seedPages($mediaIds);
        $this->seedNews($mediaIds);
        $this->seedSettings($mediaIds, $pageIds);
    }

    // -------------------------------------------------------------------------
    // Media
    // -------------------------------------------------------------------------

    /**
     * @return array<string, int|string|null>
     */
    private function seedMedia(): array
    {
        $files = [
            'logo' => ['logo.png', 'dcpp', 'Logo DCPP'],
            'favicon' => ['favicon.png', 'dcpp', 'Favicon DCPP'],
            'hero_bg' => ['hero-bg.jpg', 'dcpp', 'Hero pozadí'],
            'hero_video' => ['hero-bg.mp4', 'dcpp', 'Hero video'],
            'popup' => ['popup.png', 'dcpp', 'Plakát DCPP'],
            'about_1' => ['about/highlight-1.jpg', 'dcpp/about', 'Fotka DCPP 1'],
            'about_2' => ['about/highlight-2.jpg', 'dcpp/about', 'Fotka DCPP 2'],
            'about_3' => ['about/highlight-3.jpg', 'dcpp/about', 'Fotka DCPP 3'],
            'about_4' => ['about/highlight-4.jpg', 'dcpp/about', 'Fotka DCPP 4'],
            'gallery_1' => ['gallery/img-1.jpg', 'dcpp/gallery', 'Galerie DCPP 1'],
            'gallery_2' => ['gallery/img-2.jpg', 'dcpp/gallery', 'Galerie DCPP 2'],
            'gallery_3' => ['gallery/img-3.jpg', 'dcpp/gallery', 'Galerie DCPP 3'],
            'gallery_4' => ['gallery/img-4.jpg', 'dcpp/gallery', 'Galerie DCPP 4'],
            'gallery_5' => ['gallery/img-5.jpg', 'dcpp/gallery', 'Galerie DCPP 5'],
            'gallery_6' => ['gallery/img-6.jpg', 'dcpp/gallery', 'Galerie DCPP 6'],
            'gallery_7' => ['gallery/img-7.jpg', 'dcpp/gallery', 'Galerie DCPP 7'],
            'gallery_8' => ['gallery/img-8.jpg', 'dcpp/gallery', 'Galerie DCPP 8'],
            'gallery_9' => ['gallery/img-9.jpg', 'dcpp/gallery', 'Galerie DCPP 9'],
            'gallery_10' => ['gallery/img-10.jpg', 'dcpp/gallery', 'Galerie DCPP 10'],
            'gallery_11' => ['gallery/img-11.jpg', 'dcpp/gallery', 'Galerie DCPP 11'],
            'news_1' => ['news/article-1.png', 'dcpp/news', 'Aktualita 1'],
            'news_2' => ['news/article-2.png', 'dcpp/news', 'Aktualita 2'],
            'news_3' => ['news/article-3.jpg', 'dcpp/news', 'Aktualita 3'],
            'timetable_img' => ['timetable/jizni-predmesti.png', 'dcpp/timetable', 'Rozvrh – Jižní předměstí'],
            'timetable_pdf' => ['timetable/jizni-predmesti.pdf', 'dcpp/timetable', 'Rozvrh DCPP 2025/26'],
        ];

        $ids = [];

        foreach ($files as $key => [$assetRelPath, $storageDir, $title]) {
            $ids[$key] = $this->importMedia($assetRelPath, $storageDir, $title)?->id;
        }

        return $ids;
    }

    private function importMedia(string $assetRelPath, string $storageDir, string $title): ?Media
    {
        $sourcePath = self::ASSETS_PATH.'/'.$assetRelPath;

        if (! file_exists($sourcePath)) {
            $this->command->warn("Asset not found, skipping: {$assetRelPath}");

            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $name = pathinfo($sourcePath, PATHINFO_FILENAME);
        $storagePath = $storageDir.'/'.basename($sourcePath);

        $contents = file_get_contents($sourcePath);

        if ($contents === false) {
            $this->command->warn("Could not read asset file, skipping: {$assetRelPath}");

            return null;
        }

        Storage::disk(self::DISK)->put($storagePath, $contents);

        [$width, $height] = $this->imageDimensions($sourcePath, $ext);

        return Media::create([
            'disk' => self::DISK,
            'directory' => $storageDir,
            'visibility' => 'public',
            'name' => $name,
            'path' => $storagePath,
            'width' => $width,
            'height' => $height,
            'size' => filesize($sourcePath),
            'type' => $this->mimeType($sourcePath, $ext),
            'ext' => $ext,
            'alt' => $title,
            'title' => $title,
            'description' => '',
            'caption' => '',
            'exif' => [],
            'curations' => [],
        ]);
    }

    /**
     * @return array{int, int}
     */
    private function imageDimensions(string $path, string $ext): array
    {
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $size = @getimagesize($path);
            if ($size) {
                return [$size[0], $size[1]];
            }
        }

        return [0, 0];
    }

    private function mimeType(string $path, string $ext): string
    {
        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'pdf' => 'application/pdf',
            default => mime_content_type($path) ?: 'application/octet-stream',
        };
    }

    // -------------------------------------------------------------------------
    // Pages
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, int|string|null>  $m  Media IDs keyed by name
     * @return array<string, int>
     */
    private function seedPages(array $m): array
    {
        $home = Page::create([
            'title' => 'Domů',
            'slug' => '',
            'is_published' => true,
            'content' => [
                $this->heroSection($m),
                $this->newsSectionContent(),
                $this->danceSection(),
            ],
        ]);

        $about = Page::create([
            'title' => 'O nás',
            'slug' => 'o-nas',
            'is_published' => true,
            'content' => [
                $this->aboutSection($m),
                $this->gallerySection($m),
            ],
        ]);

        $pricing = Page::create([
            'title' => 'Ceník',
            'slug' => 'cenik',
            'is_published' => true,
            'content' => [
                $this->pricingSection(),
            ],
        ]);

        $news = Page::create([
            'title' => 'Aktuality',
            'slug' => 'aktuality',
            'is_published' => true,
            'content' => [
                $this->newsSectionContent(),
            ],
        ]);

        $contact = Page::create([
            'title' => 'Kontakt',
            'slug' => 'kontakt',
            'is_published' => true,
            'content' => [
                $this->contactTextSection(),
                $this->mapSection(),
            ],
        ]);

        $schedule = Page::create([
            'title' => 'Rozvrh',
            'slug' => 'rozvrh',
            'is_published' => true,
            'content' => [
                $this->timetableSection($m),
            ],
        ]);

        return [
            'home' => $home->id,
            'about' => $about->id,
            'pricing' => $pricing->id,
            'news' => $news->id,
            'contact' => $contact->id,
            'schedule' => $schedule->id,
        ];
    }

    /**
     * @param  array<string, int|string|null>  $m
     * @return array{type: string, data: array<string, mixed>}
     */
    private function heroSection(array $m): array
    {
        return [
            'type' => 'hero',
            'data' => [
                'title' => 'Dance center Petry Parvoničové',
                'description' => 'Taneční centrum v Plzni pod vedením choreografky Petry Parvoničové',
                'bubble' => 'Tančit může každý!',
                'background' => [
                    'img' => $m['hero_bg'],
                    'video' => $m['hero_video'],
                ],
                'button_primary' => [
                    'text' => 'Přihláška',
                    'link' => ['type' => 'external', 'url' => 'https://forms.gle/placeholder'],
                ],
                'button_secondary' => [
                    'text' => 'O nás',
                    'link' => ['type' => 'local', 'url' => '/o-nas'],
                ],
            ],
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    private function newsSectionContent(): array
    {
        return [
            'type' => 'news',
            'data' => [
                'tagline' => 'Aktuality',
                'title' => 'Co se děje v DCPP?',
                'button_text' => 'Všechny aktuality',
            ],
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    private function danceSection(): array
    {
        return [
            'type' => 'dance',
            'data' => [
                'heading' => 'Tanec je vášeň, hudba je rytmus',
                'motto' => 'Tančit může každý',
                'dance_styles' => [
                    'Taneční průprava od 3 let',
                    'Jazz Musical',
                    'Modern Dance',
                    'Street Dance',
                    'Pilates',
                    'Jazz Dance',
                    'Modern Jazz',
                    'Akrobacie',
                    'Jóga',
                    'Jazz pro maminky a tatínky',
                ],
                'text_left' => '<p>Jsme Taneční centrum v Plzni, které v roce 2015 založila choreografka a tanečnice <strong>Petra Parvoničová</strong>. Nabízíme pestré taneční styly pro děti, juniory i dospělé.</p><p>Lekce tance jsou vedeny profesionálními lektory, kteří mají individuální přístup k tanečníkům a vedou je k lásce k tanci a pohybu jako životnímu stylu.</p>',
                'text_right' => '<p>Centrum pořádá dvakrát ročně vystoupení na <strong>Novém divadle v Plzni</strong>, kde mají naši tanečníci možnost předvést svoji práci publiku.</p><p>Bez ohledu na věk nebo zkušenosti – <strong>u nás může tančit každý!</strong></p>',
            ],
        ];
    }

    /**
     * @param  array<string, int|string|null>  $m
     * @return array{type: string, data: array<string, mixed>}
     */
    private function aboutSection(array $m): array
    {
        return [
            'type' => 'about',
            'data' => [
                'tagline' => 'O DCPP',
                'title' => 'Výuka tance v DCPP',
                'description' => '<p>Jsme Taneční centrum v Plzni, které založila choreografka a tanečnice Petra Parvoničová. Nabízíme pestré taneční styly pro děti, juniory i dospělé.</p><p>Lekce tance jsou vedeny profesionálními lektory, kteří mají individuální přístup k tanečníkům, pochopení a snahu vést všechny tanečníky k lásce k tanci a pohybu jako životnímu stylu.</p><ul><li>Taneční průprava od 3 let</li><li>Jazz Musical, Modern Dance, Street Dance</li><li>Pilates, Jóga, Akrobacie</li><li>Jazz pro maminky a tatínky</li></ul>',
                'bubble' => 'U nás může tančit každý!',
                'button_primary' => [
                    'text' => 'Rozvrh',
                    'link' => ['type' => 'local', 'url' => '/rozvrh'],
                ],
                'button_secondary' => [
                    'text' => 'Ceník',
                    'link' => ['type' => 'local', 'url' => '/cenik'],
                ],
                'gallery' => array_values(array_filter([
                    $m['about_1'],
                    $m['about_2'],
                    $m['about_3'],
                    $m['about_4'],
                ])),
            ],
        ];
    }

    /**
     * @param  array<string, int|string|null>  $m
     * @return array{type: string, data: array<string, mixed>}
     */
    private function gallerySection(array $m): array
    {
        $galleryIds = array_values(array_filter([
            $m['gallery_1'], $m['gallery_2'], $m['gallery_3'],
            $m['gallery_4'], $m['gallery_5'], $m['gallery_6'],
            $m['gallery_7'], $m['gallery_8'], $m['gallery_9'],
            $m['gallery_10'], $m['gallery_11'],
        ]));

        return [
            'type' => 'gallery',
            'data' => [
                'heading' => 'Fotogalerie',
                'description' => 'Záběry z našich lekcí a vystoupení',
                'gallery' => $galleryIds,
            ],
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    private function pricingSection(): array
    {
        return [
            'type' => 'text',
            'data' => [
                'tagline' => 'Ceník',
                'heading' => 'Ceny kurzů 2025/2026',
                'body' => '<p>Cena <strong>1 kurzu (15 lekcí v pololetí)</strong> je <strong>3 200 Kč</strong>.</p><h2>Děti</h2><ul><li><strong>Tygříci 1</strong> (3+) – Taneční přípravka, začátečníci</li><li><strong>Tygříci 2</strong> (4+) – Taneční přípravka, pokročilí</li><li><strong>Tygříci 3</strong> (4+) – Taneční přípravka, začátečníci</li><li><strong>Tygříci 4</strong> (5+) – Taneční přípravka, začátečníci</li><li><strong>Šelmičky 1</strong> (5+) – Taneční přípravka, pokročilí</li><li><strong>Šelmičky 2</strong> (6+) – Taneční přípravka, začátečníci</li><li><strong>Gepardi 1</strong> (7+) – Taneční přípravka, pokročilí</li><li><strong>Gepardi 2</strong> (10+) – Modern Jazz pro děti</li><li><strong>Levharti 1</strong> (10+) – Modern Jazz pro děti</li><li><strong>Levharti 2</strong> (9+) – Jazz Dance pro děti</li><li><strong>Panteři 1</strong> (10+) – Jazz Dance pro děti</li><li><strong>Panteři 2</strong> (10+) – Street Dance</li></ul><h2>Junioři a dospělí</h2><ul><li>Modern Dance, Modern Jazz, Akrobacie, Street Dance – různé úrovně (10–17+)</li><li>Jazz Musical, Street Dance, Jazz Dance/Workout, Modern Dance (17–25+)</li></ul><h2>Podmínky a slevy</h2><ul><li>Sleva 5 % při přihlášení do 2 a více kurzů</li><li>Sleva se nevztahuje na sourozence</li><li>Uhrazené kurzovné se nevrací</li><li>Jednotlivá lekce je možná pouze po domluvě s lektorkou Peťou</li></ul>',
            ],
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    private function contactTextSection(): array
    {
        return [
            'type' => 'text',
            'data' => [
                'tagline' => 'Kontakt',
                'heading' => 'Kontaktujte nás',
                'body' => '<p><strong>E-mail:</strong> <a href="mailto:infodcpp@gmail.com">infodcpp@gmail.com</a></p><p><strong>Adresa studia:</strong> Lukavická 2792/3, Plzeň 3 – Jižní předměstí, 301 00</p><p><strong>Fakturační adresa:</strong> Rabštejnská 1579/33, 323 00 Plzeň – Bolevec</p><p><strong>IČO:</strong> 08532681 (DCPP s.r.o.)</p><h2>Sociální sítě</h2><p>Sledujte nás na Facebooku, Instagramu a TikToku pro nejnovější novinky a videa z našich lekcí.</p>',
            ],
        ];
    }

    /**
     * @return array{type: string, data: array<string, mixed>}
     */
    private function mapSection(): array
    {
        return [
            'type' => 'map',
            'data' => [
                'title' => 'Kde nás najdete',
                'text' => '<p>Studio DCPP najdete na adrese <strong>Lukavická 2792/3, Plzeň 3 – Jižní předměstí</strong>. MHD zastávka Lukavická.</p>',
                'map_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2566.0!2d13.392!3d49.738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zTHVrYXZpY2vDoSAyNzkyLzMsIFBsemXFiA!5e0!3m2!1scs!2scz!4v1234567890',
            ],
        ];
    }

    /**
     * @param  array<string, int|string|null>  $m
     * @return array{type: string, data: array<string, mixed>}
     */
    private function timetableSection(array $m): array
    {
        return [
            'type' => 'timetable',
            'data' => [
                'general' => [
                    'title' => 'Rozvrh 2025/2026',
                    'text' => '<p><strong>Zimní pololetí:</strong> 29. 9. 2025 – 6. 2. 2026</p><p><strong>Letní pololetí:</strong> 16. 2. 2026 – 15. 6. 2026</p><p>Každé pololetí obsahuje <strong>15 lekcí</strong>. Vystoupení se konají na <strong>Novém divadle v Plzni</strong>.</p>',
                ],
                'signup' => [
                    'title' => 'Přihlašování',
                    'text' => '<p>Přihlášky na taneční lekce pro rok 2025/2026 jsou otevřeny. Vyplňte přihlášku online nebo nás kontaktujte na <a href="mailto:infodcpp@gmail.com">infodcpp@gmail.com</a>.</p>',
                    'button' => [
                        'text' => 'Přihlásit se',
                        'link' => ['type' => 'external', 'url' => 'https://forms.gle/placeholder'],
                    ],
                ],
                'timetable_selector' => [
                    'title' => 'Výběr pobočky',
                    'detail' => 'Klikněte na pobočku pro zobrazení rozvrhu',
                    'timetables' => [
                        [
                            'name' => 'DCPP Jižní předměstí',
                            'img' => $m['timetable_img'],
                            'pdf' => $m['timetable_pdf'],
                        ],
                    ],
                ],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // News
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, int|string|null>  $m
     */
    private function seedNews(array $m): void
    {
        News::create([
            'title' => 'Akce a prázdniny v DCPP 2025/26',
            'slug' => 'akce-a-prazdniny-v-dcpp-2025-26',
            'excerpt' => 'Plány akcí a prázdnin v DCPP na taneční rok 2025/26',
            'content' => '<p>Připravili jsme pro vás přehled všech plánovaných akcí a prázdnin v DCPP pro taneční rok 2025/26.</p><h2>Podzim 2025</h2><ul><li><strong>9. 9. 2025</strong> – Elfetex Brno</li><li><strong>27. 9. 2025</strong> – Premiéra muzikálu „Divotvorný hrnec"</li><li><strong>29. 9. 2025</strong> – Zahájení sezóny a přihlašování</li><li><strong>27.–29. 10. 2025</strong> – Podzimní prázdniny (lekce neprobíhají)</li><li><strong>1. 11. 2025</strong> – Stezka odvahy</li><li><strong>6. 12. 2025</strong> – Mikulášská lekce</li><li><strong>15.–17. 12. 2025</strong> – Vánoční lekce</li><li><strong>18. 12. 2025 – 2. 1. 2026</strong> – Vánoční prázdniny</li></ul><h2>Jaro 2026</h2><ul><li><strong>6. 2. 2026</strong> – Pololetní vystoupení na Novém divadle</li><li><strong>2.–6. 3. 2026</strong> – Jarní prázdniny (lekce neprobíhají)</li><li><strong>2.–6. 4. 2026</strong> – Velikonoční prázdniny (lekce neprobíhají)</li><li><strong>28. 4. 2026</strong> – Čarodějnice</li><li><strong>15. 6. 2026</strong> – Závěrečné vystoupení na Novém divadle</li></ul><h2>Léto 2026</h2><ul><li><strong>5.–12. 7. 2026</strong> – Italský taneční camp</li><li><strong>17.–21. 8. 2026</strong> – Příměstský tábor DCPP</li></ul>',
            'thumbnail' => $m['news_1'] ? (string) $m['news_1'] : null,
            'author' => 'Michal Škoula',
            'published_at' => '2025-10-05 10:00:00',
        ]);

        News::create([
            'title' => 'Nový web DCPP',
            'slug' => 'novy-web-dcpp',
            'excerpt' => 'DCPP má konečně webové stránky! Nyní můžete najít přihlašování na taneční lekce, rozvrhy, kontaktní informace a aktuality přehledně na jednom místě.',
            'content' => '<p>DCPP má konečně vlastní webové stránky! Po dlouhé době máme místo, kde najdete vše na jednom místě.</p><h2>Co najdete na novém webu</h2><ul><li><strong>Přihlašování</strong> na taneční lekce online přes jednoduché formuláře</li><li><strong>Rozvrhy</strong> pro všechny pobočky ke stažení i prohlížení</li><li><strong>Ceník</strong> kurzů přehledně na jednom místě</li><li><strong>Aktuality</strong> – novinky z DCPP, fotky z akcí a vystoupení</li><li><strong>Kontaktní informace</strong> a mapa studia</li></ul><p>Web jsme vytvořili na moderní platformě postaveně na technologiích Laravel a Filament. Budeme web průběžně vylepšovat a doplňovat obsah.</p><p>Pokud narazíte na chybu nebo máte nápad na vylepšení, dejte nám vědět přes odkaz „Nahlásit chybu" v patičce stránky.</p>',
            'thumbnail' => $m['news_2'] ? (string) $m['news_2'] : null,
            'author' => 'Michal Škoula',
            'published_at' => '2025-10-05 09:00:00',
        ]);

        News::create([
            'title' => 'Zahajujeme taneční sezónu 2025/2026',
            'slug' => 'zahajujeme-tanecni-sezonu-2025-2026',
            'excerpt' => 'Dne 29. 9. jsme v DCPP zahájili novou taneční sezónu 2025/26. Přihlašování je otevřeno!',
            'content' => '<p>Dne 29. září 2025 jsme v Dance centru Petry Parvoničové zahájili novou taneční sezónu 2025/2026. Těšíme se na všechny naše tanečníky – staré i nové!</p><h2>Otevřené kurzy</h2><ul><li><strong>Pondělí 18:00–19:00</strong> – Modern Jazz (12+)</li><li><strong>Úterý 16:00–17:00</strong> – Modern Jazz (10+)</li><li><strong>Úterý 17:00–18:00</strong> – Street Dance (10+)</li><li><strong>Čtvrtek 15:45–16:30</strong> – Taneční přípravka (3+)</li><li><strong>Pátek 18:00–19:00</strong> – Akrobacie (13+)</li></ul><p>Lekce probíhají ve studiu DCPP na adrese <strong>Lukavická 3, Plzeň</strong>.</p><h2>Jak se přihlásit</h2><p>Přihlášky přijímáme přes online formulář nebo e-mailem na <a href="mailto:infodcpp@gmail.com">infodcpp@gmail.com</a>. Počet míst je omezený, takže neváhejte!</p><p><strong>Tančit může každý!!!</strong></p>',
            'thumbnail' => $m['news_3'] ? (string) $m['news_3'] : null,
            'author' => 'Michal Škoula',
            'published_at' => '2025-09-29 08:00:00',
        ]);
    }

    // -------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------

    /**
     * @param  array<string, int|string|null>  $m
     * @param  array<string, int>  $pageIds
     */
    private function seedSettings(array $m, array $pageIds): void
    {
        $this->seedGeneralSettings($m);
        $this->seedNavMenuSettings($pageIds);
        $this->seedContactSettings($pageIds);
        $this->seedPopupSettings($m);
    }

    /**
     * @param  array<string, int|string|null>  $m
     */
    private function seedGeneralSettings(array $m): void
    {
        $settings = app(GeneralSettings::class);
        $settings->name = 'Dance center Petry Parvoničové';
        $settings->description = 'Taneční centrum v Plzni pod vedením choreografky Petry Parvoničové';
        $settings->logo = $m['logo'] ? (string) $m['logo'] : null;
        $settings->faviconLight = $m['favicon'] ? (string) $m['favicon'] : null;
        $settings->faviconDark = $m['favicon'] ? (string) $m['favicon'] : null;
        $settings->save();
    }

    /**
     * @param  array<string, int>  $pageIds
     */
    private function seedNavMenuSettings(array $pageIds): void
    {
        $settings = app(NavMenuSettings::class);
        $settings->structure = [
            ['collection' => 'pages', 'id' => $pageIds['home'], 'children' => []],
            ['collection' => 'pages', 'id' => $pageIds['about'], 'children' => [
                ['collection' => 'pages', 'id' => $pageIds['news'], 'children' => []],
                ['collection' => 'pages', 'id' => $pageIds['contact'], 'children' => []],
            ]],
            ['collection' => 'pages', 'id' => $pageIds['pricing'], 'children' => []],
            ['collection' => 'pages', 'id' => $pageIds['schedule'], 'children' => []],
        ];
        $settings->button_primary = [
            'text' => 'Přihláška',
            'link' => ['type' => 'external', 'url' => 'https://forms.gle/placeholder'],
        ];
        $settings->button_secondary = [
            'text' => 'Mám otázku',
            'link' => ['type' => 'external', 'url' => 'mailto:infodcpp@gmail.com'],
        ];
        $settings->save();
    }

    /**
     * @param  array<string, int>  $pageIds
     */
    private function seedContactSettings(array $pageIds): void
    {
        $settings = app(ContactSettings::class);
        $settings->socials = [
            ['icon' => 'fab fa-facebook-f', 'name' => 'Facebook', 'url' => 'https://facebook.com/dcpp'],
            ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'url' => 'https://instagram.com/dcpp'],
            ['icon' => 'fab fa-tiktok', 'name' => 'TikTok', 'url' => 'https://tiktok.com/@dcpp'],
            ['icon' => 'fab fa-whatsapp', 'name' => 'WhatsApp', 'url' => 'https://wa.me/420123456789'],
        ];
        $settings->footerNav = [
            [
                'heading' => 'Navigace',
                'items' => [
                    ['item' => ['text' => 'Domů', 'link' => ['type' => 'page', 'url' => $pageIds['home']]]],
                    ['item' => ['text' => 'O nás', 'link' => ['type' => 'page', 'url' => $pageIds['about']]]],
                    ['item' => ['text' => 'Ceník', 'link' => ['type' => 'page', 'url' => $pageIds['pricing']]]],
                    ['item' => ['text' => 'Aktuality', 'link' => ['type' => 'page', 'url' => $pageIds['news']]]],
                    ['item' => ['text' => 'Kontakt', 'link' => ['type' => 'page', 'url' => $pageIds['contact']]]],
                    ['item' => ['text' => 'Rozvrh', 'link' => ['type' => 'page', 'url' => $pageIds['schedule']]]],
                ],
            ],
            [
                'heading' => 'Kontakt',
                'items' => [
                    ['item' => ['text' => 'infodcpp@gmail.com', 'link' => ['type' => 'external', 'url' => 'mailto:infodcpp@gmail.com']]],
                    ['item' => ['text' => 'Lukavická 2792/3, Plzeň', 'link' => ['type' => 'external', 'url' => 'https://maps.google.com/?q=Lukavická+2792/3,+Plzeň']]],
                ],
            ],
        ];
        $settings->errorReportButton = [
            'text' => 'Nahlásit chybu',
            'link' => ['type' => 'external', 'url' => 'mailto:infodcpp@gmail.com?subject=Nahlášení chyby na webu DCPP'],
        ];
        $settings->save();
    }

    /**
     * @param  array<string, int|string|null>  $m
     */
    private function seedPopupSettings(array $m): void
    {
        $settings = app(PopupSettings::class);
        $settings->enabled = true;
        $settings->stripeEnabled = true;
        $settings->stripeText = 'Otevřeli jsme přihlašování na lekce tance pro rok 2025/2026';
        $settings->stripeCta = [
            'text' => 'Přihlásit se',
            'link' => ['type' => 'external', 'url' => 'https://forms.gle/placeholder'],
        ];
        $settings->popupEnabled = true;
        $settings->popupImage = $m['popup'] ? (string) $m['popup'] : null;
        $settings->popupHeading = 'Přihlašování 2025/2026 je otevřeno!';
        $settings->popupContent = '<p>Právě jsme otevřeli přihlašování na taneční lekce pro rok 2025/2026. Nenechte si ujít místo – kapacita kurzů je omezená!</p><p>Přihlaste se ještě dnes a začněte tančit s námi.</p>';
        $settings->popupCta = [
            'text' => 'Přihlásit se',
            'link' => ['type' => 'external', 'url' => 'https://forms.gle/placeholder'],
        ];
        $settings->save();
    }
}
