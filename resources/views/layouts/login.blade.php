<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Formulario de Abertura') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-brand text-slate-800">
<main class="flex-1 flex items-center justify-center py-10 px-4">
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
