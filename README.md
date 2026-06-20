# Semestrální práce KIV/UUR - Modulární Redakční Systém (CMS)

V semestrální práci jsem si dal za cíl vytvořit modulární redakční systém (CMS), který můžu použít ve vlastním podnikání a nabídnout klientům lepší UX oproti existujícím systémům. 

Vytvořený v PHP, frameworku **Laravel 13** s administračním rozhraním v **Filament 5**
(Livewire, Alpine.js, Tailwind CSS). Aplikace umožňuje správu obsahu webu skrze
skladačku sekcí (*page builder*), hierarchický editor navigace, modulární
nastavení webu a obsahové kolekce. Cílem práce bylo vytvořit administrační
prostředí s důrazem na použitelnost a uživatelský prožitek (UI/UX), které je
srozumitelné i pro klienta bez technických znalostí.

> [!warning] **Scope projektu** 
> Součástí projektu je také z větší části dodělaný frontend k webu [dcpp.cz](https://dcpp.cz), který na tento systém převádím, je tu však hlavně pro kompletnost. Prosím zaměřit se s hodnocením výhradně na CMS část. 

---

## 1. Požadavky

Pro běh aplikace je možné zvolit jednu ze dvou cest:

- **Docker (Laravel Sail)** — postačuje nainstalovaný Docker; veškeré závislosti
  (PHP 8.4, PostgreSQL 18, Node.js) běží v kontejnerech.
- **Lokální prostředí (Composer)** — vyžaduje lokálně PHP 8.4 nebo 8.5, Composer,
  Node.js (npm) a běžící databázi PostgreSQL.

---

## 2. Použití — přístup do administrace

1. Spusťte aplikaci dle kapitoly 3 (Docker) nebo 4 (Composer).
2. Otevřete v prohlížeči adresu administrace:
   - Docker (Sail): **http://localhost/admin**
   - Lokální server (`composer dev`): **http://localhost:8000/admin**
3. Přihlaste se předvyplněnými údaji (viz kapitola 5) a potvrďte formulář.

Veřejnou část webu (vykreslenou *page builderem*) najdete na kořenové adrese
aplikace (`/`).

---

## 3. Instalace přes Docker (Laravel Sail)

Doporučený postup, který nevyžaduje žádné lokálně instalované závislosti kromě
Dockeru.

```bash
# 1. Příprava konfigurace prostředí
cp .env.example .env

# 2. Buildnutí Docker Compose
composer require laravel/sail --dev
./vendor/bin/sail build --no-cache

# 3. Spuštění přes Docker Compose (aplikace + PostgreSQL)
./vendor/bin/sail up -d

# 4. Setup projektu a seed dat
./vendor/bin/sail composer setup
./vendor/bin/sail php artisan migrate:fresh --seed --force
./vendor/bin/sail php artisan config:cache # V pripade 403:Forbidden potreba refreshnout .env
```

Po spuštění je aplikace dostupná na adrese **http://localhost**.

---

## 4. Instalace přes Composer (lokální prostředí)

Postup pro běh nad lokálně nainstalovaným PHP a databází. V repozitáři je
připraven souhrnný skript `composer setup`, který provede většinu kroků
najednou.

```bash

# 2. Souhrnná instalace (install, klíč, migrace, storage:link, build frontendu
composer setup

# Upravte v .env přístup k databázi (DB_HOST, DB_PORT, DB_DATABASE,
# DB_USERNAME, DB_PASSWORD) dle vašeho lokálního PostgreSQL serveru.
cat .env

# 3. Naplnění databáze ukázkovými daty (viz. kapitola 5)
php artisan db:seed
```

Vývojový server je následně možné spustit pomocí:

```bash
composer dev   # paralelně: php artisan serve, logy (pail) a vite
```

---

## 5. Seedování dat a přihlášení

Naplnění databáze ukázkovým obsahem zajišťuje příkaz:

```bash
php artisan migrate --seed      # při čisté instalaci
# nebo
php artisan db:seed             # nad již zmigrovanou databází
```

`DatabaseSeeder` postupně vytvoří:

- **administrátorský účet**,
- ukázkový obsah webu — stránky, sekce *page builderu*, navigaci a média
  (`WebsiteSeeder`),
- ukázková analytická data pro nástěnku (`AnalyticsSeeder`).

**Přihlašovací údaje** do administrace:

| Pole   | Hodnota                  |
|--------|--------------------------|
| E-mail | `admin@example.com`      |
| Heslo  | `PlsPlsChciJednicku1`    |

> Pro pohodlí hodnocení jsou tyto údaje na přihlašovací obrazovce
> **předvyplněny**, takže stačí potvrdit formulář.

---

## 6. Struktura projektu — kde co hledat

Aplikace dodržuje standardní adresářovou strukturu Laravelu; níže je přehled
míst, kam byla umístěna logika specifická pro tuto práci.

### Administrace (Filament)

| Cesta                                               | Obsah                                                                          |
|-----------------------------------------------------|--------------------------------------------------------------------------------|
| `app/Providers/Filament/`                           | Konfigurace administračního panelu (cesta `/admin`, barvy, pluginy, branding). |
| `app/Filament/Resources/`                           | Filament resources — `Pages` (správa stránek) a `News` (aktuality).            |
| `app/Filament/Pages/`                               | Vlastní stránky panelu — `CustomDashboard`, `CustomLoginPage`.                 |
| `app/Filament/Pages/Settings/`                      | Stránky nastavení webu (obecné, kontakty, navigace, vyskakovací okno).         |
| `app/Filament/Widgets/`                             | Widgety nástěnky — analytika, grafy, rychlé akce, editor navigace.             |
| `app/Filament/PageBuilder/Sections/`                | Definice (formulářová schémata) jednotlivých sekcí *page builderu*.            |
| `app/Filament/Components/`, `app/Filament/Actions/` | Sdílené komponenty a akce (mj. vyhledávací *spotlight*).                       |

### Doménová a aplikační logika

| Cesta                   | Obsah                                                                           |
|-------------------------|---------------------------------------------------------------------------------|
| `app/Models/`           | Modely — `Page`, `News`, `Analytics`, `User`.                                   |
| `app/Settings/`         | Třídy nastavení (Spatie Settings) — branding, kontakty, navigace, popup.        |
| `app/Services/`         | Služby — `PageBuilderService`, `SitemapService`, `AnalyticsService`.            |
| `app/Http/Controllers/` | Veřejná část webu — `CmsPageController` (vykreslení stránek), `NewsController`. |

### Frontend (veřejný web)

| Cesta                                                     | Obsah                                                                |
|-----------------------------------------------------------|----------------------------------------------------------------------|
| `resources/views/page-builder/sections/`                  | Blade šablony pro vykreslení jednotlivých sekcí.                     |
| `resources/views/layouts/`, `resources/views/components/` | Layouty a sdílené komponenty webu.                                   |
| `routes/web.php`                                          | Routování veřejného webu (catch-all směřuje na `CmsPageController`). |

### Konfigurace

| Cesta                                  | Obsah                                                            |
|----------------------------------------|------------------------------------------------------------------|
| `config/page-builder.php`              | Registr dostupných sekcí *page builderu*.                        |
| `config/content-collections.php`       | Definice obsahových kolekcí (např. aktuality).                   |
| `config/curator.php`                   | Konfigurace správce médií (Filament Curator).                    |
| `config/filament-search-spotlight.php` | Konfigurace globálního vyhledávání.                              |
| `database/seeders/`                    | Seedery včetně ukázkových mediálních souborů (`seeders/assets`). |

---

## 7. Přehled implementovaných funkcí

- **Page builder** — skladání stránek z vývojářem definovaných sekcí (Hero,
  O nás, Tanec, Galerie, Mapa, Aktuality, Text, Rozvrh). Uživatel sekce volně
  přidává, řadí (přetahováním) a nastavuje jejich obsah přes přehledné
  formuláře, aniž by mohl rozbít zamýšlený design.
- **Editor navigace** — drag-and-drop sestavení hierarchického hlavního menu
  webu přímo z nástěnky.
- **Modulární nastavení webu** — oddělené, přehledně členěné stránky nastavení
  (branding a logo, kontakty, navigace, vyskakovací okno) s infrastrukturou pro
  snadné přidávání dalších sekcí.
- **Nástěnka (dashboard)** — analytické přehledy (souhrnné statistiky, graf
  návštěvnosti, nejnavštěvovanější stránky) a panel rychlých akcí pro
  nejčastější úkony.
- **Globální vyhledávání (spotlight)** — vlastní vyhledávací rozhraní, které
  prohledává nejen obsah, ale i moduly CMS; přístupné klávesovou zkratkou
  i myší.
- **Správa médií** — knihovna souborů postavená na Filament Curator
  (v panelu jako „Soubory").
- **Obsahové kolekce** — modelově řízený obsah nad rámec *page builderu*
  (aktuality dostupné na veřejné cestě `/aktuality`).
- **Vizuální identita administrace** — vynucený tmavý režim, vlastní barevné
  schéma a branding (logo/favicon) řízený přes nastavení.
- **Responzivita** — primární zaměření na desktop, plná funkčnost i na menších
  obrazovkách.
