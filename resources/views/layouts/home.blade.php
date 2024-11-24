<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @yield('title') - {{ config('app.name') }}
    </title>
    <!-- Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/logo/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/media/logo/apple-touch-icon.png') }}">

    <!-- Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    {{-- <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600&display=swap" rel="stylesheet" /> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css">

    <!-- Global Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @include('layouts.partial.themeMode')

    <!-- Custom Styles / Scripts -->
    @stack('headerStyles')
    @stack('headerScripts')
</head>

<body
    class="font-sans antialiased bg-[#f8fafd] dark:bg-[#1b1b1b] flex flex-col justify-center items-center w-screen h-screen">
    <div id="header" class="w-full p-2 h-[64px] text-[#444746] dark:text-[#c4c7c5]">
        <div class="flex justify-between">
            <div class="logo min-w-[50px]">
                <a href="{{ route('home.index') }}" class="flex items-center justify-start gap-1">
                    <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}"
                        class="w-[48px] h-[48px]" />
                    <span class="text-[22px] leading-7 hidden lg:block font-medium">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>
            <div class="flex items-center">
                @include('layouts.partial.search')
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button"
                        class="flex items-center justify-center w-[40px] h-[40px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                        <i class="fa-regular fa-language text-[20px]"></i>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[9999] w-52 p-2 shadow-md">
                        @foreach (['en' => __('en'), 'vi' => __('vi')] as $key => $lang)
                            <li><a href="{{ route('language', $key) }}">{{ $lang }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button"
                        class="flex items-center justify-center w-[40px] h-[40px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                        {{-- <i class="fa-regular fa-circle-info text-[20px]"></i> --}}
                        <i class="fa-solid fa-address-card text-[20px]"></i>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[9999] w-52 p-2 shadow-md">
                        @foreach (config('onedrive.social') as $key => $s)
                            <li>
                                <a class="capitalize" href="{{ $s['url'] }}" target="_blank">
                                    <i class="{{ $s['icon'] }} text-[18px]"></i>
                                    {{ $key }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @include('layouts.partial.themeTongle')
            </div>
        </div>
    </div>
    <div id="content-wrapper" class="flex w-full gap-3 px-4 grow">
        <div id="content"
            class="rounded-[1rem] dark:bg-[#131314] bg-white grow p-3 h-full w-full text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col">
            {{-- <div class="max-w-full max-h-full overflow-auto"> --}}
            <div class="max-w-full overflow-x-auto breadcrumbs text-[16px] lg:text-[24px] mb-3 p-0 min-h-9">
                <ul class="">
                    <li class="breadcrumbs-item">
                        <a href="{{ route('home.index') }}" class="decoration-transparent">{{ __('Home') }}</a>
                    </li>
                    @foreach ($data['breadcrumbs'] as $b)
                        <li class="breadcrumbs-item">
                            <a href="{{ route('home.path', $b['path']) }}"
                                class="decoration-transparent">{{ $b['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @yield('content')
            {{-- </div> --}}
        </div>
        @include('layouts.partial.sidebar')
    </div>
    <div id="footer" class="h-[32px] flex justify-center items-center">
        <span class="text-[#444746] dark:text-[#c4c7c5] text-xs">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. Made with ❤️ by PontaDev
        </span>
    </div>
    <!-- Custom Styles / Scripts -->
    @session('error')
        <dialog id="errorModal" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button
                        class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2 text-[#444746] dark:text-[#c4c7c5]">✕</button>
                </form>
                <h3 class="text-lg font-bold text-red-400">
                    {{ __('Opps! Something went wrong') }}
                </h3>
                <p class="py-4 text-[#444746] dark:text-[#c4c7c5] ">
                    {{ session('error') }}
                </p>
            </div>
        </dialog>
        <script>
            errorModal.showModal()
        </script>
    @endsession
    <script type="module">
        function setMaxHeight() {
            // Set max height
            const content = $('#content');
            const sidebar = $('#sidebar');
            // content height = window height - header height - footer height
            let maxContentHeight = $(window).height() - $('#header').height() - $('#footer').height() - 24;
            content.css('max-height', maxContentHeight);
            sidebar.css('max-height', maxContentHeight);
        }
        setMaxHeight();
        $(window).resize(function() {
            setMaxHeight();
        });
    </script>
    <script type="module" src="{{ asset('assets/js/home/search.js') }}"></script>
    @stack('footerStyles')
    @stack('footerScripts')
</body>

</html>
