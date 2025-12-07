<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pointify </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Environment variables for React -->
    <script>
        window.ENV = {
            APP_NAME: @json(config('app.name')),
            APP_URL: @json(config('app.url')),
            VITE_API_URL: @json(env('VITE_API_URL', '/api'))
        };
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/ts/app.tsx'])
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div id="app"></div>
</body>

</html>