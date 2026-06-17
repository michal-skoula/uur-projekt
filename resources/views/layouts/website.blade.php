@php
    use App\Settings\GeneralSettings;
    use Awcodes\Curator\Models\Media;

    $settings = app(GeneralSettings::class);
    $logoUrl = Media::find($settings->logo)?->url;
    $faviconUrl = Media::find($settings->getFaviconForDarkMode())?->url;
@endphp
    <!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $settings->name }}</title>
    @if ($logoUrl)
        <link rel="logo" href="{{ $logoUrl }}" alt="{{ $settings->name }}">
    @endif
    @if ($faviconUrl)
        <link rel="icon" href="{{ $faviconUrl }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/filament.js'])
    @livewireStyles
</head>
<body class="bg-primary-darkest text-gray antialiased">

{{--
Conditional rendering handled inside the actual component via
Settings class and alpinejs
--}}
<x-popup-banner/>
<x-layout.header/>

<main id="content">
    @yield('content')
</main>

<x-layout.footer/>
<script src="https://kit.fontawesome.com/0e273c52d3.js" crossorigin="anonymous"></script>
@livewireScripts
</body>
</html>
