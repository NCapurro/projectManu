<!DOCTYPE html>
<!--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">-->
    <html lang="es">
    <head>
        
        <meta charset="utf-8">
        <meta name="google" content="notranslate">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title inertia>{{ config('app.name', 'Laravel') }}</title>


        <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $meta_title ?? 'Manu Horazzi - Stand Up' }}">
    <meta property="og:description" content="{{ $meta_description ?? 'Conseguí tus entradas para los próximos shows.' }}">
    <meta property="og:image" content="{{ $meta_image ?? asset('img/default.jpg') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta_title ?? 'Manu Horazzi - Stand Up' }}">
    <meta name="twitter:description" content="{{ $meta_description ?? 'Conseguí tus entradas para los próximos shows.' }}">
    <meta name="twitter:image" content="{{ $meta_image ?? asset('img/default.jpg') }}">
    
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
