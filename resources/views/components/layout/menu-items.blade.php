@props([
    'items'  => [],
    'pages'  => collect(),
    'mobile' => false,
])

@foreach ($items as $item)
    @php
        $page = $pages->get($item['id']) ?? null;
        $hasChildren = ! empty($item['children']);
    @endphp

    @if ($page)
        @if ($hasChildren)
            {{-- Item with dropdown --}}
            @if ($mobile)
                {{-- Mobile: submenus always visible, label is non-interactive --}}
                <li class="dcpp-nav-li">
                    <span class="dcpp-nav-dropdown-button">{{ $page->title }}</span>
                    <ul class="dcpp-nav-submenu">
                        <x-layout.menu-items :items="$item['children']" :pages="$pages" :mobile="true" />
                    </ul>
                </li>
            @else
                {{-- Desktop: hover + click toggles dropdown --}}
                <li
                    class="dcpp-nav-li"
                    x-data="{ open: false }"
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                >
                    <button
                        class="dcpp-nav-dropdown-button"
                        @click="open = !open"
                        @keydown.escape.stop="open = false; $el.focus()"
                        :aria-expanded="open.toString()"
                        aria-haspopup="true"
                    >
                        {{ $page->title }}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-5 transition-transform duration-150" :class="{ 'rotate-180': open }">
                            <path d="M459-381 314-526q-3-3-4.5-6.5T308-540q0-8 5.5-14t14.5-6h304q9 0 14.5 6t5.5 14q0 2-6 14L501-381q-5 5-10 7t-11 2q-6 0-11-2t-10-7Z"/>
                        </svg>
                    </button>

                    <ul
                        class="dcpp-nav-submenu"
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        @keydown.escape.stop="open = false; $el.previousElementSibling.focus()"
                    >
                        <x-layout.menu-items :items="$item['children']" :pages="$pages" :mobile="false" />
                    </ul>
                </li>
            @endif
        @else
            {{-- Leaf item --}}
            <li class="dcpp-nav-li">
                <a href="{{ $page->slug ? '/' . $page->slug : '#' }}">{{ $page->title }}</a>
            </li>
        @endif
    @endif
@endforeach
