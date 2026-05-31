<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-primary-darkest text-gray antialiased">

    {{--
    Conditional rendering handled inside the actual component via
    Settings class and alpinejs
    --}}
    <x-popup-banner />
    <x-layout.header />

    <main id="content">
        @yield('content')
    </main>

    <x-layout.footer />

    @livewireScripts
</body>
</html>
