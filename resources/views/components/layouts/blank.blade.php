<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Chat</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased h-screen w-screen overflow-hidden bg-gray-900">
{{ $slot }}
</body>
</html>
