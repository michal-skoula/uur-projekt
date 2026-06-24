@php
    use App\Settings\GeneralSettings;
    use Awcodes\Curator\Models\Media;
    use Illuminate\Support\Facades\Lang;

    $code = $exception->getStatusCode();
    $tier = $code < 500 ? '4xx' : '5xx';
    $title = Lang::has("errors.codes.{$code}.title")
        ? __("errors.codes.{$code}.title")
        : __("errors.codes.{$tier}.title");
    $message = Lang::has("errors.codes.{$code}.message")
        ? __("errors.codes.{$code}.message")
        : __("errors.codes.{$tier}.message");

    // Resolve branding defensively: a 5xx may itself be a database/settings
    // failure, so a broken lookup must never break the error page.
    try {
        $settings = app(GeneralSettings::class);
        $siteName = $settings->name;
        $logoUrl = $settings->logo ? Media::find($settings->logo)?->url : null;
    } catch (\Throwable) {
        $siteName = config('app.name');
        $logoUrl = null;
    }
@endphp
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $siteName }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-primary-darkest text-gray antialiased">
    <main class="max-w-xl px-5 text-center">
        @if ($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="mx-auto mb-8 h-16">
        @endif
        <p class="text-gradient-secondary mb-4 font-serif text-7xl md:text-8xl">{{ $code }}</p>
        <h1 class="mb-4">{{ $title }}</h1>
        <p class="mb-8 text-gray">{{ $message }}</p>
        <a href="{{ url('/') }}" class="btn-primary">{{ __('errors.back_home') }}</a>
    </main>
</body>
</html>
