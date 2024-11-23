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
                        class="dropdown-content menu bg-white dark:bg-[#1e1f20] rounded-box z-[9999] w-52 p-2 shadow-md">
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
                    <div class="drawer-side z-[9999]">
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
    <div id="content-wrapper" class="flex w-full gap-3 px-4 grow">
        <div id="content"
            class="rounded-[1rem] dark:bg-[#131314] bg-white grow p-4 pr-2 h-full w-full text-[#1f1f1f] dark:text-[#e3e3e3] flex flex-col">
            {{-- <div class="max-w-full max-h-full overflow-auto"> --}}
            <div class="max-w-full overflow-hidden breadcrumbs text-[24px] mb-3 p-0 min-h-9">
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
        <div id="sidebar"
            class="hidden lg:flex min-w-[300px] max-w-[300px] rounded-[1rem] dark:bg-[#131314] bg-white h-full text-[#1f1f1f] dark:text-[#e3e3e3] flex-col">
            <div class="flex justify-between p-3 pb-0 sidebar-header">
                <h3 class="flex items-center justify-center text-base font-medium" id="sidebar-title">
                    @if (count($data['breadcrumbs']) == 0)
                        {{ __('Home') }}
                    @else
                        {{ $data['breadcrumbs'][count($data['breadcrumbs']) - 1]['name'] }}
                    @endif
                </h3>
                <div
                    class="w-[30px] h-[30px] rounded-full flex justify-center items-center cursor-pointer hover:bg-[#1f1f1f14] dark:hover:bg-[#e3e3e314]">
                    <i class="fa-duotone fa-regular fa-xmark text-[16px] text-[#1f1f1f] dark:text-[#e3e3e3]"></i>
                </div>
            </div>
            <div class="tab-info grow">
                <div class="flex border-b border-b-[#c7c7c7] dark:border-b-[#444746] mt-5">
                    <div class="w-[50%] flex justify-center">
                        <span class="text-center pb-4 border-b-[3px] font-semibold tab-title active text-[0.875rem]"
                            data-target-id="tab_1">{{ __('Information') }}</span>
                    </div>
                    <div class="w-[50%] flex justify-center">
                        <span class="text-center pb-4 border-b-[3px] font-semibold tab-title text-[0.875rem]"
                            data-target-id="tab_2">{{ __('Activity') }}</span>
                    </div>
                </div>
                <div class="tab-data">
                    <div class="flex gap-4 tab-pane" id="tab_1">
                        <div class="flex flex-col items-center justify-center grow default">
                            <img src="{{ asset('assets/media/svg/empty_state_details_v2.svg') }}" alt=""
                                class="w-[176px]">
                            <span id="info-text" class="text-[0.875rem]">
                                {{ __('Select an item to view details') }}
                            </span>
                        </div>
                        <div class="items-center justify-center hidden loading-container grow min-h-[100px]">
                            <div class="loading"></div>
                        </div>
                        <div class="flex-col hidden main grow text-[#c7c7c7] dark:text-[#444746]">
                            <div
                                class="flex flex-col justify-center w-full p-3 pb-4 border-b border-b-[#c7c7c7] dark:border-b-[#444746] ">
                                <img src="" alt="Preview" class="max-w-full m-auto preview">
                                <div class="flex flex-col justify-center w-full mt-5">
                                    <h2 class="text-base font-medium text-[#1f1f1f] dark:text-[#e3e3e3]">
                                        {{ __('Uploaded by') }}</h2>
                                    <div class="flex items-center justify-start gap-2 mt-2">
                                        <div class="w-[25px] h-[25px] rounded-full">
                                            <img src="{{ asset('assets/media/avatar/photo.jpg') }}" alt="Owner"
                                                class="w-full h-full rounded-full">
                                        </div>
                                        <span
                                            class="text-sm font-medium owner-name text-[#1f1f1f] dark:text-[#050404] opacity-80"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col justify-center w-full p-3 pb-4">
                                <h2 class="text-base font-medium text-[#1f1f1f] dark:text-[#e3e3e3]">
                                    {{ __('Details') }}</h2>
                                <div class="flex items-center justify-between gap-4 mt-3">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hidden tab-pane" id="tab_2">
                        456
                    </div>
                </div>
            </div>
        </div>
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
    @stack('footerStyles')
    @stack('footerScripts')
</body>

</html>
