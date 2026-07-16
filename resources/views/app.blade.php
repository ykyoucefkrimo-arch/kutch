<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="app-url" content="{{ rtrim(config('app.url'), '/') }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('logo.png') }}" type="image/png" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        {{-- Header boutique (fixed, style kutch.dz) — uniquement sur les pages publiques,
             jamais sur l'admin qui a sa propre navigation (AdminLayout.vue) --}}
        @unless(request()->is('admin') || request()->is('admin/*'))
            @include('partials.header')
        @endunless
        @inertia
    </body>
</html>
