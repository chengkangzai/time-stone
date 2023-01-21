<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

</head>

<body>

    <div class="bg-indigo-900 relative overflow-hidden min-h-screen">
        <div class="inset-0 bg-black opacity-25 absolute"></div>
        <header class="absolute top-0 left-0 right-0 z-20">
            <nav class="container mx-auto px-6 md:px-12 py-4" x-data="{ open: false }">
                <div class="md:flex justify-between items-center">
                    <div class="flex justify-between items-center">
                        <a href="#" class="text-white">
                            <img src="{{ asset('favicon.png') }}" alt="logo" class="h-8">
                        </a>

                        <div class="md:hidden">
                            <button @click="open = !open" class="text-white focus:outline-none">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path x-show="open === false" d="M4 6H20M4 12H20M4 18H20" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path x-show="open === true" d="M6 18L18 6M6 6L18 18" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-2">
                        @guest
                            <a href="{{ route('register') }}"
                                class="block text-sm text-white cursor-pointer px-4 py-2 rounded-2xl hover:bg-indigo-600">{{ __('Register') }}</a>
                            <a href="{{ route('login') }}"
                                class="block text-sm text-white cursor-pointer px-4 py-2 rounded-2xl hover:bg-indigo-600">{{ __('Login') }}</a>
                        @endguest
                        @auth
                            <a href="{{ route('home') }}"
                                class="block text-sm text-white cursor-pointer px-4 py-2 rounded-2xl hover:bg-indigo-600">{{ __('Dashboard') }}
                                &rarr; </a>
                        @endauth
                        {{-- <a class="text-sm uppercase mx-3 text-white cursor-pointer hover:text-indigo-600">About us</a> --}}
                        {{-- <a class="text-sm uppercase mx-3 text-white cursor-pointer hover:text-indigo-600">Contact us</a> --}}
                    </div>
                </div>

                <div x-show="open === true"
                    class="md:hidden flex flex-col w-full z-40 bg-indigo-600 rounded mt-4 py-2 overflow-hidden">
                    @guest
                        <a href="{{ route('register') }}"
                            class="text-sm text-gray-200 py-2 px-2 hover:bg-indigo-500">{{ __('Register') }}</a>
                        <a href="{{ route('login') }}"
                            class="text-sm text-gray-200 py-2 px-2 hover:bg-indigo-500">{{ __('Login') }}</a>
                    @endguest
                    @auth
                        <a href="{{ route('home') }}"
                            class="text-sm text-gray-200 py-2 px-2 hover:bg-indigo-500">{{ __('Dashboard') }}</a>
                    @endauth
                    {{-- <a class="text-sm uppercase text-gray-200 py-2 px-2 hover:bg-indigo-500">About us</a> --}}
                    {{-- <a class="text-sm uppercase text-gray-200 py-2 px-2 hover:bg-indigo-500">Contact us</a> --}}
                </div>
            </nav>
        </header>

        <div class="container mx-auto px-6 md:px-12 relative z-10 items-center min-h-screen grid place-items-center">
            <div class="flex flex-col items-center relative z-10">
                <h1 class="text-center text-4xl sm:text-6xl text-white mt-4 max-w-3xl capitalize">
                    {{ __('Supercharge your campus Life with the TimeStone') }}
                </h1>

                <p class="text-indigo-400 mt-6 text-lg text-center max-w-3xl">
                    {{ __('TimeStone will automatically sync your timetable with your calendar.') }}
                </p>

                @guest
                    <a href="{{ route('register') }}"
                        class="block bg-indigo-500 hover:bg-indigo-400 py-4 px-6 rounded-full text-sm text-white uppercase mt-10 font-bold">
                        {{ __('Get started') }} &rarr;
                    </a>
                @endguest
                @auth
                    <a href="{{ route('home') }}"
                        class="block bg-indigo-500 hover:bg-indigo-400 py-4 px-6 rounded-full text-sm text-white uppercase mt-10 font-bold">
                        {{ __('Dashboard') }} &rarr;
                    </a>
                @endauth
            </div>
        </div>

        <svg class="absolute left-0 bottom-0 w-full h-full" xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 662.41 385.07" preserveAspectRatio="xMinYMax slice">
            <defs />
            <defs>
                <radialGradient id="a" cx="368.69" cy="333.92" r="259.84"
                    gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#d37575" />
                    <stop offset=".99" stop-color="#58448b" stop-opacity="0" />
                </radialGradient>
                <radialGradient id="c" cx="2758.74" cy="334.46" r="259.84"
                    gradientTransform="matrix(-1 0 0 1 3280.52 0)" xlink:href="#a" />
                <clipPath id="b">
                    <path fill="none" d="M0 0h662.41v385.07H0z" />
                </clipPath>
            </defs>
            <g clip-path="url(#b)" style="clip-path:url(#clip-path)">
                <g opacity=".2">
                    <path fill="#58448b"
                        d="M361.11 385.53a16.81 16.81 0 00-23.11-4.32v-.59a38.54 38.54 0 00-58.5-33 26.74 26.74 0 00-45.43-12.15A16.77 16.77 0 00223 331a37.75 37.75 0 00-56.86-44.81 26.64 26.64 0 00-13.06-9.39 38.89 38.89 0 00-28.59-65.28 40.12 40.12 0 00-5.63.4 40 40 0 00-69.66-35.4A26.71 26.71 0 0015.34 169c0-.49.05-1 .05-1.45A45.51 45.51 0 00-30.13 122a45.35 45.35 0 00-20.78 5 26.93 26.93 0 002.06-10.3A26.79 26.79 0 00-75.63 90a26.37 26.37 0 00-13.09 3.43v301.82h449.83z" />
                    <path fill="#48376d"
                        d="M222.53 395.25h-45.64a22.82 22.82 0 1145.64 0zM323 395.25h-66.4a15.71 15.71 0 1128.31-9.73 22.8 22.8 0 0138.09 9.73zM134.15 360.36a27.23 27.23 0 00-12.43 3 18.08 18.08 0 00-31.4-17.06A18.75 18.75 0 0061 331.06a30.41 30.41 0 00.5-5.35A29 29 0 007.49 311a5.85 5.85 0 000-.6A18.77 18.77 0 00-10 291.66 22.83 22.83 0 005.65 270a22.83 22.83 0 00-22.83-22.82 22.72 22.72 0 00-10 2.34 31.25 31.25 0 002.27-11.63 31.25 31.25 0 00-31.25-31.25 31.15 31.15 0 00-16.66 4.84 18.09 18.09 0 00-11.24-6.71 22.75 22.75 0 006.86-16.29 22.78 22.78 0 00-11.5-19.8v226.57h249a26.5 26.5 0 001.1-7.65 27.24 27.24 0 00-27.25-27.24z" />
                    <path fill="#3e3063"
                        d="M80.62 395.25H23.39a29.79 29.79 0 0157.23 0zM7.43 381.31a39.28 39.28 0 01-2.54 13.94h-93.61V271.32a43 43 0 0134 42.07 42.63 42.63 0 01-2.59 14.74h.08a16.78 16.78 0 0116.63 14.69 39.72 39.72 0 018.58-.94 39.44 39.44 0 0139.45 39.43z" />
                    <circle cx="101.05" cy="240.18" r="12.28" fill="#48376d"
                        transform="rotate(-45 101.048 240.185)" />
                    <circle cx="86.62" cy="303.13" r="8.6" fill="#48376d" />
                    <path fill="#48376d"
                        d="M136.23 339a5.75 5.75 0 115.75 5.75 5.75 5.75 0 01-5.75-5.75zM239 373.74a5.74 5.74 0 115.74 5.75 5.74 5.74 0 01-5.74-5.75zM6.61 287.71a5.75 5.75 0 115.74 5.75 5.74 5.74 0 01-5.74-5.75zM124.5 248.67a5.87 5.87 0 115.87 5.87 5.87 5.87 0 01-5.87-5.87z" />
                    <path fill="#58448b" d="M241.25 311a5.55 5.55 0 115.55 5.55 5.55 5.55 0 01-5.55-5.55z" />
                    <circle cx="181.19" cy="251.6" r="9.63" fill="#58448b"
                        transform="rotate(-25.44 181.23 251.633)" />
                    <path fill="#3e3063" d="M19.23 365.39a4.85 4.85 0 114.85 4.85 4.85 4.85 0 01-4.85-4.85z" />
                    <circle cx="95.21" cy="370.24" r="9.53" fill="#3e3063"
                        transform="rotate(-76.66 95.223 370.242)" />
                    <path fill="#ad6475"
                        d="M272.13 353.47a38.5 38.5 0 017.41-5.85 26.68 26.68 0 00-33.08-19.53 26.74 26.74 0 0125.67 25.38zM217.33 316.32a37.6 37.6 0 01-4.54 18 16.72 16.72 0 019.94-3.27h.27a37.75 37.75 0 00-35-51.75h-.81a37.76 37.76 0 0130.14 37.02zM206.78 342.49a37.63 37.63 0 01-26.39 11.56 37.85 37.85 0 0025.67-3.86 16 16 0 01-.18-2.31 16.61 16.61 0 01.9-5.39z" />
                    <path fill="#ad6475"
                        d="M223 331h-.29a16.72 16.72 0 00-9.94 3.27 37.9 37.9 0 01-6 8.19 16.61 16.61 0 00-.9 5.39 16 16 0 00.18 2.31A37.88 37.88 0 00223 331zM138.3 276.3a26.39 26.39 0 016.45-.82 26.8 26.8 0 018.35 1.34 38.89 38.89 0 00-9.24-60.11 38.91 38.91 0 01-5.56 59.59zM105.79 205.87v.88a38.75 38.75 0 0110.56-1.46c1.23 0 2.45.06 3.65.17.11-1.16.17-2.34.17-3.53A40 40 0 0062.52 166c1.08-.09 2.16-.14 3.26-.14a40 40 0 0140.01 40.01z" />
                    <path fill="#ad6475"
                        d="M105.77 206.75a40 40 0 01-1.65 10.56 38.73 38.73 0 0114.76-5.31 39.62 39.62 0 001.1-6.49c-1.2-.11-2.42-.17-3.65-.17a38.75 38.75 0 00-10.56 1.41z" />
                    <path fill="url(#a)"
                        d="M361.11 385.53a16.81 16.81 0 00-23.11-4.32v-.59a38.54 38.54 0 00-58.5-33 26.74 26.74 0 00-45.43-12.15A16.77 16.77 0 00223 331a37.75 37.75 0 00-56.86-44.81 26.64 26.64 0 00-13.06-9.39 38.89 38.89 0 00-28.59-65.28 40.12 40.12 0 00-5.63.4 40 40 0 00-69.66-35.4A26.71 26.71 0 0015.34 169c0-.49.05-1 .05-1.45A45.51 45.51 0 00-30.13 122a45.35 45.35 0 00-20.78 5 26.93 26.93 0 002.06-10.3A26.79 26.79 0 00-75.63 90a26.37 26.37 0 00-13.09 3.43v301.82h449.83z"
                        style="mix-blend-mode:lighten" />
                </g>
                <g opacity=".2">
                    <path fill="#58448b"
                        d="M529.35 386.08a16.8 16.8 0 0123.07-4.33v-.59a38.55 38.55 0 0158.51-33A26.74 26.74 0 01656.34 336a16.77 16.77 0 0111.1-4.45 37.76 37.76 0 0156.86-44.81 26.7 26.7 0 0113.06-9.39 38.9 38.9 0 0134.22-64.87 40 40 0 0169.66-35.41 26.71 26.71 0 0133.88-7.58v-1.45a45.52 45.52 0 0166.3-40.48 26.78 26.78 0 0124.67-37.06 26.37 26.37 0 0113.09 3.43v301.86H529.35z" />
                    <path fill="#48376d"
                        d="M567.43 395.79h66.4a15.7 15.7 0 10-28.31-9.73 22.8 22.8 0 00-38.09 9.73zM651.46 374.29a5.74 5.74 0 10-5.74 5.74 5.74 5.74 0 005.74-5.74z" />
                    <circle cx="643.66" cy="311.51" r="5.55" fill="#58448b" />
                    <path fill="#ad6475"
                        d="M618.33 354a38.58 38.58 0 00-7.41-5.85 26.76 26.76 0 0126-20.5 27.05 27.05 0 017.07 1A26.75 26.75 0 00618.33 354z" />
                    <path fill="url(#c)"
                        d="M529.35 386.08a16.8 16.8 0 0123.07-4.33v-.59a38.55 38.55 0 0158.51-33A26.74 26.74 0 01656.34 336a16.77 16.77 0 0111.1-4.45 37.76 37.76 0 0156.86-44.81 26.7 26.7 0 0113.06-9.39 38.9 38.9 0 0134.22-64.87 40 40 0 0169.66-35.41 26.71 26.71 0 0133.88-7.58v-1.45a45.52 45.52 0 0166.3-40.48 26.78 26.78 0 0124.67-37.06 26.37 26.37 0 0113.09 3.43v301.86H529.35z"
                        style="mix-blend-mode:lighten" />
                </g>
            </g>
        </svg>
    </div>
</body>

</html>
