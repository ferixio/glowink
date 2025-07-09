<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Livewire Styles -->
        @livewireStyles
    </head>

    <body class="">
        <div class="min-h-screen ">
            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
        <script>
            document.documentElement.classList.remove('dark');
            document.body.classList.remove('dark');
        </script>
    </body>

</html>
