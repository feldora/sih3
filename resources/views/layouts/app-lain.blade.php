<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="sih3-modern">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Loader -->
    <div id="loader" role="status" aria-live="polite" class="fixed inset-0 flex items-center justify-center bg-white z-50">

        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500"></div>
    </div>

    <div class="fixed top-0 left-0 w-full z-40">
        @include('partials.header')
    </div>

    <main class="">
        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
    </script>
    @stack('scripts')

</body>

</html>
