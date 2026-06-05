@php
    $settings = app(\App\Settings\GeneralSettings::class);
    $logoUrl = \Awcodes\Curator\Models\Media::find($settings->logo)?->url;
    $faviconUrl = \Awcodes\Curator\Models\Media::find($settings->getFaviconForDarkMode())?->url;
@endphp
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $settings->name }}</title>
    @if ($logoUrl)
        <link rel="logo" href="{{ $logoUrl }}">
    @endif
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/filament.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
