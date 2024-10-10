<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:100,200,300,400,600,700,800,900&display=swap" rel="stylesheet" />

        {{-- FA6 --}}
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/aquawolf04/font-awesome-pro@5cd1511/css/all.css"> --}}

        <!-- Vite -->
        @vite('resources/js/app.js')
    </head>
    <body class="font-sans antialiased dark:bg-neutral-800 dark:text-white/50 h-screen max-h-screen">
        <div id="app" class="h-screen max-h-screen"></div>
    </body>
</html>