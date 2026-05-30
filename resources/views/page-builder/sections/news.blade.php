<section id="news" class="section bg-primary-darkest text-white">
    <div class="max-content-width">

        <div class="w-full text-center">
            @if ($tagline)
                <p class="sm:text-2xl text-xl font-semibold font-serif tracking-wider text-gradient-secondary">
                    {{ $tagline }}
                </p>
            @endif
            <h2>{{ $title }}</h2>
        </div>

        @if ($posts)
            <div class="mt-8 grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-8 mx-auto">
                @foreach ($posts as $post)
                    <article class="rounded-xl border border-primary-outline overflow-clip bg-primary-darker h-full flex flex-col">
                        <a href="{{ route('news.show', $post['slug']) }}" tabindex="-1" class="block w-full aspect-[16/10] overflow-clip bg-white">
                            @if ($post['thumbnail'])
                                <img
                                    loading="lazy"
                                    src="{{ $post['thumbnail'] }}"
                                    class="h-full w-full object-contain hover:scale-110 transition-all ease-in-out duration-300"
                                    alt="{{ $post['title'] }}"
                                >
                            @else
                                <div class="h-full w-full bg-primary-dark"></div>
                            @endif
                        </a>
                        <div class="p-6 py-8 flex flex-col gap-8 flex-1">
                            <div>
                                <div class="mb-1 flex gap-2 text-gray">
                                    <div class="flex gap-0.5 items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-4">
                                            <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-40q0-17 11.5-28.5T280-880q17 0 28.5 11.5T320-840v40h320v-40q0-17 11.5-28.5T680-880q17 0 28.5 11.5T720-840v40h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
                                        </svg>
                                        <time datetime="{{ $post['date'] }}" class="text-sm">{{ $post['date'] }}</time>
                                    </div>
                                    @if ($post['author'])
                                        <div class="flex gap-0.5 items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="currentColor" class="size-4">
                                                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-240v-32q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v32q0 33-23.5 56.5T720-160H240q-33 0-56.5-23.5T160-240Zm80 0h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/>
                                            </svg>
                                            <p class="text-sm">{{ $post['author'] }}</p>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="h4 mb-1.5">{{ $post['title'] }}</h3>
                                @if ($post['excerpt'])
                                    <p class="text-gray">{{ $post['excerpt'] }}</p>
                                @endif
                            </div>
                            <div class="mt-auto">
                                <a
                                    href="{{ route('news.show', $post['slug']) }}"
                                    class="px-6 py-3 btn-primary motion-safe:hover:px-8 motion-safe:focus:px-8"
                                >
                                    Zjistit více
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                <a
                    href="{{ route('news.index') }}"
                    class="btn-secondary px-10 py-4.5 motion-safe:hover:px-13 motion-safe:focus:px-13"
                >
                    {{ $buttonText }}
                </a>
            </div>
        @else
            <p class="text-center text-gray mt-8">Žádné články nenalezeny.</p>
        @endif

    </div>
</section>
