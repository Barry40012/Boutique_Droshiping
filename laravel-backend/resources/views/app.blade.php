<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>{{ config('app.name', 'Boutique') }}</title>
    @vite('resources/js/app.jsx')
    @inertiaHead
</head>
<body class="page">
    @inertia
</body>
</html>

