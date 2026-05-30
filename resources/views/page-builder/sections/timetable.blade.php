<section id="timetable" class="section bg-primary-darkest text-white">
    <div class="max-content-width">
        <div class="mb-8">
            <h2 class="text-center">Tanec v DCPP</h2>
        </div>

        <div class="grid md:grid-cols-2 grid-cols-1 gap-8">

            {{-- General info --}}
            <div class="p-6 bg-primary-darker border border-primary-outline rounded-xl md:order-1 order-2 h-full">
                <h3 class="h4 mb-4">{{ $general['title'] }}</h3>
                <div class="text-sm post-container-styles">
                    {!! $general['text'] !!}
                </div>
            </div>

            {{-- Signup CTA --}}
            <div class="p-6 bg-accent-yellow/10 border border-accent-yellow/20 rounded-xl order-3">
                <h3 class="h4 mb-4">{{ $signup['title'] }}</h3>
                <div class="mb-6 post-container-styles">
                    {!! $signup['text'] !!}
                </div>
                @if ($signup['button']['text'])
                    <a
                        href="{{ $signup['button']['url'] }}"
                        target="{{ $signup['button']['target'] }}"
                        rel="{{ $signup['button']['target'] === '_blank' ? 'nofollow noopener' : '' }}"
                        class="btn-primary flex items-center"
                    >
                        <span class="w-full text-center">{{ $signup['button']['text'] }}</span>
                    </a>
                @endif
            </div>

            {{-- Timetable selector --}}
            <div
                class="p-6 max-sm:px-3 bg-primary-darker border border-primary-outline rounded-xl md:order-2 order-1 row-span-2 text-center"
                x-data="{ view: 'selection', selected: null }"
            >
                <h3 class="h4 mb-2">{{ $timetableSelector['title'] }}</h3>
                @if ($timetableSelector['detail'])
                    <p class="mb-6">{{ $timetableSelector['detail'] }}</p>
                @endif

                @if ($timetables)
                    {{-- Selection view --}}
                    <div x-show="view === 'selection'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <div class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-6 justify-center">
                            @foreach ($timetables as $i => $timetable)
                                <button
                                    type="button"
                                    class="bg-primary-dark aspect-video relative rounded-lg p-4 cursor-pointer hover:bg-primary ring-0 transition-colors"
                                    :class="{ 'ring-2 ring-accent-yellow': selected === {{ $i }} }"
                                    @click="selected = {{ $i }}; view = 'display'"
                                >
                                    <span class="font-medium text-xl flex items-center justify-center h-full w-full select-none">
                                        {{ $timetable['name'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Display view --}}
                    <div x-show="view === 'display'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                        <div class="flex flex-col items-center gap-6">
                            @foreach ($timetables as $i => $timetable)
                                <div class="w-full" x-show="selected === {{ $i }}">
                                    @if ($timetable['imgUrl'])
                                        <img
                                            src="{{ $timetable['imgUrl'] }}"
                                            alt="{{ $timetable['name'] }}"
                                            class="w-full max-w-2xl mx-auto rounded-lg object-fit"
                                            loading="lazy"
                                        >
                                    @else
                                        <p class="text-gray">Chybí obrázek rozvrhu</p>
                                    @endif

                                    <div class="flex flex-wrap gap-3 mt-4 items-center justify-center w-full">
                                        @if ($timetable['pdfUrl'])
                                            <a
                                                href="{{ $timetable['pdfUrl'] }}"
                                                class="btn-primary no-resize inline-block max-sm:w-full"
                                                download
                                            >
                                                Stáhnout
                                            </a>
                                        @else
                                            <p class="text-gray">Chybí PDF ke stažení</p>
                                        @endif

                                        <button
                                            type="button"
                                            class="btn-outline no-resize max-sm:w-full inline-block"
                                            @click="view = 'selection'"
                                        >
                                            Zpět na výběr
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="font-semibold">Žádné dostupné rozvrhy</p>
                @endif
            </div>

        </div>
    </div>
</section>
