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
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600&display=swap" rel="stylesheet" />
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
            <div class="logo">
                <a href="{{ route('home.index') }}" class="flex items-center justify-start gap-1">
                    <img src="{{ asset('assets/media/logo/onedrive.svg') }}" alt="{{ config('app.name') }}"
                        class="w-[48px] h-[48px]" />
                    <span class="text-[22px] leading-7 hidden lg:block font-medium">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>
            <div class="flex items-center">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button"
                        class="flex items-center justify-center w-[40px] h-[40px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                        <i class="fa-regular fa-circle-info text-[20px]"></i>
                    </div>
                    <ul tabindex="0"
                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[1] w-52 p-2 shadow-md">
                        <li><a>Item 1</a></li>
                        <li><a>Item 2</a></li>
                    </ul>
                </div>
                @include('layouts.partial.themeTongle')
                <div class="drawer drawer-end lg:hidden w-fit">
                    <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />
                    <div
                        class="flex items-center justify-center drawer-content w-[40px] h-[40px] rounded-full cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                        <!-- Page content here -->
                        <label for="my-drawer-4" class="drawer-button">
                            <i class="text-2xl fa-solid fa-bars"></i>
                        </label>
                    </div>
                    <div class="drawer-side">
                        <label for="my-drawer-4" aria-label="close sidebar" class="drawer-overlay"></label>
                        <ul class="min-h-full p-4 menu bg-base-200 text-base-content w-80">
                            <!-- Sidebar content here -->
                            <li><a>Sidebar Item 1</a></li>
                            <li><a>Sidebar Item 2</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="content-wrapper" class="flex w-full gap-2 px-4 grow">
        <div id="sidebar" class="hidden lg:block w-[256px]">
            sidebar
        </div>
        <div id="content"
            class="rounded-[1rem] dark:bg-[#131314] bg-white grow p-3 h-full w-full overflow-auto text-[#1f1f1f] dark:text-[#e3e3e3]">
            {{-- <div class="max-w-full max-h-full overflow-auto"> --}}
            @yield('content')
            {{-- </div> --}}
        </div>
    </div>
    <div id="footer" class="h-[32px] flex justify-center items-center">
        <span class="text-[#444746] dark:text-[#c4c7c5] text-xs">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
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
        // Set max height of content
        const content = $('#content');
        // content height = window height - header height - footer height
        content.css('max-height', $(window).height() - $('#header').height() - $('#footer').height() - 24);
    </script>
    @stack('footerStyles')
    @stack('footerScripts')
</body>

</html>
