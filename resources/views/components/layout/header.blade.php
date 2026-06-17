@php
    use App\Filament\Components\LinkInput;
    use App\Models\Page;
    use App\Services\PageBuilderService;
    use App\Settings\NavMenuSettings;

    $menuSettings = app(NavMenuSettings::class);
    $menuStructure = $menuSettings->structure ?? [];

    $allIds = PageBuilderService::buildCollectionListsFromTree($menuStructure);
    $pages = Page::whereIn('id', $allIds)->get()->keyBy('id');

    $btnPrimary   = $menuSettings->button_primary;
    $btnSecondary = $menuSettings->button_secondary;

    $btnPrimaryLink   = LinkInput::resolve($btnPrimary['link'] ?? null);
    $btnSecondaryLink = LinkInput::resolve($btnSecondary['link'] ?? null);
@endphp

<header
    id="header"
    class="sticky top-0 z-30 transition-transform duration-300 ease-in-out motion-reduce:transition-none"
    x-data="{
        mobileOpen: false,
        hidden: false,
        lastY: 0,
        onScroll() {
            const y = window.scrollY;
            this.hidden = y > this.lastY && y > 80;
            this.lastY = y;
        }
    }"
    x-init="window.addEventListener('scroll', () => onScroll(), { passive: true })"
    :class="{ '-translate-y-full': hidden }"
>

    {{-- Skip navigation --}}
    <a
        href="#content"
        class="pointer-events-none absolute top-4 left-4 z-50 bg-primary-darker rounded-lg p-3 opacity-0 border border-primary-outline focus:opacity-100 focus:pointer-events-auto"
    >
        Přeskočit na obsah
    </a>

    {{-- Main navbar --}}
    <nav id="navbar" class="dcpp-nav-navbar">

        {{-- Logo --}}
        <div>
            <a href="/" class="h6 text-white">
                {{ config('app.name') }}
            </a>
        </div>

        {{-- Desktop right side --}}
        <div class="flex gap-4 items-center">

            {{-- Desktop nav menu --}}
            <ul class="dcpp-nav-parent-ul">
                <x-layout.menu-items :items="$menuStructure" :pages="$pages" :mobile="false"/>
            </ul>

            {{-- Desktop CTA buttons --}}
            @if ($btnPrimary || $btnSecondary)
                <div class="nav:flex hidden gap-4">
                    @if ($btnPrimary)
                        <a href="{{ $btnPrimaryLink['url'] }}" target="{{ $btnPrimaryLink['target'] }}"
                           class="btn-primary">
                            {{ $btnPrimary['text'] ?? '' }}
                        </a>
                    @endif
                    @if ($btnSecondary)
                        <a href="{{ $btnSecondaryLink['url'] }}" target="{{ $btnSecondaryLink['target'] }}"
                           class="btn-secondary">
                            {{ $btnSecondary['text'] ?? '' }}
                        </a>
                    @endif
                </div>
            @endif

            {{-- Mobile hamburger --}}
            <button
                class="dcpp-nav-open-btn"
                @click="mobileOpen = true"
                :aria-expanded="mobileOpen.toString()"
                aria-controls="slideover"
                aria-label="Otevřít navigaci"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"
                     class="size-7 text-white">
                    <path
                        d="M160-240q-17 0-28.5-11.5T120-280q0-17 11.5-28.5T160-320h640q17 0 28.5 11.5T840-280q0 17-11.5 28.5T800-240H160Zm0-200q-17 0-28.5-11.5T120-480q0-17 11.5-28.5T160-520h640q17 0 28.5 11.5T840-480q0 17-11.5 28.5T800-440H160Zm0-200q-17 0-28.5-11.5T120-680q0-17 11.5-28.5T160-720h640q17 0 28.5 11.5T840-680q0 17-11.5 28.5T800-640H160Z"/>
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile backdrop --}}
    <div
        id="backdrop"
        class="fixed inset-0 z-40 bg-black transition-opacity duration-200 ease-in-out"
        x-show="mobileOpen"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-70"
        x-transition:leave-start="opacity-70"
        x-transition:leave-end="opacity-0"
        @click="mobileOpen = false"
        aria-hidden="true"
    ></div>

    {{-- Mobile slideover --}}
    <nav
        id="slideover"
        class="dcpp-nav-slideover-menu"
        x-show="mobileOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        x-effect="mobileOpen ? document.body.style.overflow = 'hidden' : document.body.style.overflow = ''"
        @keydown.escape.window="mobileOpen = false"
        :inert="!mobileOpen"
    >
        <div class="w-full flex flex-col gap-y-6 pb-4">

            {{-- Close button --}}
            <button
                class="dcpp-nav-close-btn"
                @click="mobileOpen = false"
                aria-label="Zavřít navigaci"
                aria-controls="slideover"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor"
                     class="size-7 text-white">
                    <path
                        d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
                </svg>
            </button>

            {{-- Mobile CTA buttons --}}
            @if ($btnPrimary || $btnSecondary)
                <div class="flex gap-2 justify-end">
                    @if ($btnSecondary)
                        <a href="{{ $btnSecondaryLink['url'] }}" target="{{ $btnSecondaryLink['target'] }}"
                           class="btn-secondary">
                            {{ $btnSecondary['text'] ?? '' }}
                        </a>
                    @endif
                    @if ($btnPrimary)
                        <a href="{{ $btnPrimaryLink['url'] }}" target="{{ $btnPrimaryLink['target'] }}"
                           class="btn-primary">
                            {{ $btnPrimary['text'] ?? '' }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Mobile nav menu --}}
        <div class="h-full overflow-y-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <ul class="dcpp-nav-sidebar-parent-menu">
                <x-layout.menu-items :items="$menuStructure" :pages="$pages" :mobile="true"/>
            </ul>
        </div>
    </nav>

</header>
