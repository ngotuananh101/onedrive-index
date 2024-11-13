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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css">

    <!-- Global Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @include('layouts.partial.themeMode')

    <!-- Custom Styles / Scripts -->
    @stack('headerStyles')
    @stack('headerScripts')
</head>

<body
    class="font-sans antialiased bg-[#f0f4f9] text-[#1f1f1f] flex flex-col justify-center items-center w-screen h-screen dark:bg-[#1e1f20]">
    <div class="w-full flex flex-col grow lg:grow-0 justify-center items-center lg:w-[1040px]">
        <div class=" bg-[#ffffff] flex w-full h-full p-[18px] lg:p-[36px] lg:h-fit lg:rounded-[28px] dark:bg-[#0e0e0e]">
            @yield('body')
        </div>
        <div class="w-full h-[64px] bg-[#ffffff] dark:bg-[#1e1f20] lg:bg-transparent flex justify-between items-center">
            @include('layouts.partial.changeLanguage')
            @include('layouts.partial.themeTongle')
        </div>
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
    @stack('footerStyles')
    @stack('footerScripts')
</body>

</html>
