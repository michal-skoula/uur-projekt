@php
    use App\Filament\Components\LinkInput;
    use App\Settings\PopupSettings;
    use Illuminate\Support\Facades\Storage;

    $settings = app(PopupSettings::class);

    if (! $settings->enabled) {
        return;
    }

    $showStripe = $settings->stripeEnabled;
    $showPopup  = $settings->popupEnabled;

    if (! $showStripe && ! $showPopup) {
        return;
    }

    $stripeCta = $settings->stripeCta ? LinkInput::resolve($settings->stripeCta['link'] ?? null) : null;
    $popupCta  = $settings->popupCta  ? LinkInput::resolve($settings->popupCta['link']  ?? null) : null;
@endphp

<div
    x-data="{
        stripeVisible: false,
        popupVisible: false,
        init() {
            if (localStorage.getItem('dcpp_popup_dismissed')) return;
            this.stripeVisible = @json($showStripe);
            if (@json($showPopup)) {
                setTimeout(() => {
                    if (! localStorage.getItem('dcpp_popup_dismissed')) {
                        this.popupVisible = true;
                    }
                }, 10000);
            }
        },
        dismiss() {
            localStorage.setItem('dcpp_popup_dismissed', '1');
            this.stripeVisible = false;
            this.popupVisible  = false;
        }
    }"
>

    {{-- Announcement stripe --}}
    @if ($showStripe)
        <div
            class="sticky top-0 inset-x-0 z-50 flex items-center justify-between gap-4 px-6 py-3 bg-white border-b border-zinc-200 text-primary-darkest"
            x-show="stripeVisible"
            x-cloak
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-full"
        >
            <div class="flex flex-wrap items-center gap-x-6 gap-y-1 w-full">
                <p class="text-sm text-primary-darker">{{ $settings->stripeText }}</p>

                @if ($stripeCta && ! empty($settings->stripeCta['text']))
                    <a
                        href="{{ $stripeCta['url'] }}"
                        target="{{ $stripeCta['target'] }}"
                        class="text-sm font-medium text-accent-ocean underline underline-offset-2 decoration-accent-ocean hover:text-accent-teal hover:decoration-accent-teal transition-colors"
                    >
                        {{ $settings->stripeCta['text'] }}
                    </a>
                @endif
            </div>

            <button
                @click="dismiss()"
                class="shrink-0 p-1 rounded-full hover:bg-zinc-100 transition-colors cursor-pointer"
                aria-label="Zavřít oznámení"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-5 text-primary-darker">
                    <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Modal popup --}}
    @if ($showPopup)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            x-show="popupVisible"
            x-cloak
            @keydown.escape.window="dismiss()"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/70 backdrop-blur-sm"
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="dismiss()"
                aria-hidden="true"
            ></div>

            {{-- Panel --}}
            <div
                class="relative z-10 w-full max-w-lg bg-primary-darker rounded-2xl overflow-hidden shadow-2xl"
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition duration-200 ease-in"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <button
                    @click="dismiss()"
                    class="absolute top-4 right-4 p-1 z-10 rounded-full hover:bg-primary-dark transition-colors cursor-pointer"
                    aria-label="Zavřít"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-5 text-gray">
                        <path d="M480-424 284-228q-11 11-28 11t-28-11q-11-11-11-28t11-28l196-196-196-196q-11-11-11-28t11-28q11-11 28-11t28 11l196 196 196-196q11-11 28-11t28 11q11 11 11 28t-11 28L536-480l196 196q11 11 11 28t-11 28q-11 11-28 11t-28-11L480-424Z"/>
                    </svg>
                </button>

                @if ($settings->popupImage)
                    <img
                        src="{{ Storage::url($settings->popupImage) }}"
                        alt=""
                        class="w-full aspect-video object-cover"
                        aria-hidden="true"
                    >
                @endif

                <div class="p-6 flex flex-col gap-4">
                    @if ($settings->popupHeading)
                        <h2 class="h4">{{ $settings->popupHeading }}</h2>
                    @endif

                    @if ($settings->popupContent)
                        <div class="text-sm text-gray leading-relaxed [&_ul]:list-disc [&_ul]:ml-4 [&_a]:text-accent-teal [&_a]:underline">
                            {!! $settings->popupContent !!}
                        </div>
                    @endif

                    @if ($popupCta && ! empty($settings->popupCta['text']))
                        <a
                            href="{{ $popupCta['url'] }}"
                            target="{{ $popupCta['target'] }}"
                            class="btn-primary self-start"
                        >
                            {{ $settings->popupCta['text'] }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
