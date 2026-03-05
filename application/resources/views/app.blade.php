<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes

        <!-- Theme initialization script - prevents FOUC -->
        <script>
            (function() {
                const hasUserPreference = localStorage.getItem('user-theme-preference') === 'true';
                let isDarkMode;
                
                if (hasUserPreference) {
                    // User has manually set a preference
                    const storedTheme = localStorage.getItem('dark-mode');
                    isDarkMode = storedTheme === 'true';
                } else {
                    // Use system preference
                    isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                
                if (isDarkMode) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @production
            {{vite_production_assets()}}
        @else
            @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
        @endproduction

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
