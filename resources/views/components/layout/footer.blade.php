@php
    use App\Filament\Components\LinkInput;
    use App\Settings\ContactSettings;
    use App\Settings\GeneralSettings;

    $generalSettings = app(GeneralSettings::class);
    $contactSettings = app(ContactSettings::class);
    $errorReport = LinkInput::resolve($contactSettings->errorReportButton['link'] ?? null);
@endphp

<footer id="colophon" class="section bg-slate-950 md:rounded-t-[3rem] rounded-t-3xl lg:pb-4!" role="contentinfo">
    <div class="max-content-width">

        {{-- Title, socials, description --}}
        <div class="lg:mb-12 mb-8">
            <div class="mb-2">
                <p class="h2">{!! nl2br(e($generalSettings->name)) !!}</p>

                <div class="flex gap-1 mt-2">
                    @foreach ($contactSettings->socials as $social)
                        <a
                            href="{{ $social['url'] }}"
                            class="first-of-type:pl-0 p-2 text-gray hover:text-accent-mint transition-colors"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['name'] }}"
                        >
                            <i class="{{ $social['icon'] }} text-2xl"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <p class="max-w-[50ch]">{!! nl2br(e($generalSettings->description)) !!}</p>
        </div>

        {{-- Navigation columns --}}
        <div class="w-full flex gap-12 flex-wrap">
            @foreach ($contactSettings->footerNav as $column)
                <div class="flex flex-col gap-2">
                    <h3 class="h6 font-medium mx-1 mt-1 mb-1.5">{{ $column['heading'] }}</h3>
                    <ul class="m-0 list-none">
                        @foreach ($column['items'] as $item)
                            @php $navLink = LinkInput::resolve($item['item']['link'] ?? null); @endphp
                            <li class="list-none m-0 mb-0.5">
                                <a
                                    href="{{ $navLink['url'] }}"
                                    target="{{ $navLink['target'] }}"
                                    class="block p-1 hover:text-accent-mint hover:underline transition-colors duration-300 ease-in-out"
                                >
                                    {{ $item['item']['text'] ?? '' }}
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
    <div class="max-w-7xl mx-auto p-6 flex flex-wrap gap-x-8 gap-y-2 items-center justify-between text-base">
        <p>&copy; {{ $generalSettings->name }}, {{ date('Y') }}</p>
        <div class="flex flex-wrap gap-x-6 gap-y-2">
            @if (!empty($contactSettings->errorReportButton['text']))
                <a href="{{ $errorReport['url'] }}" target="{{ $errorReport['target'] }}" class="underline!">
                    {{ $contactSettings->errorReportButton['text'] }}
                </a>
            @endif
            <p>
                Web vytvořil
                <a href="https://skoula.com" target="_blank" rel="nofollow" class="underline! text-accent-yellow">
                    Michal Škoula
                </a>.
            </p>
        </div>
    </div>
</section>
