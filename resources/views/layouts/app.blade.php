<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Formulario de Abertura') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 flex flex-col">
<header class="bg-brand text-white shadow-sm">
    <div class="mx-auto flex w-full max-w-5xl items-center justify-between gap-4 px-6 py-4">
        <a href="{{ route('dashboard') }}" class="brand-logo-wrapper">
            <img src="{{ asset('images/logo-ci.png') }}" alt="Contabilidade para Igrejas" class="brand-logo-img">
            <span class="sr-only">{{ config('app.name', 'Formulario de Abertura') }}</span>
        </a>
        <nav class="flex flex-wrap items-center gap-4 text-sm font-semibold">
            @auth
                <a href="{{ route('dashboard') }}" class="text-white">Pagina inicial</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn-secondary px-4 py-2 text-sm font-semibold">Sair</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white">Entrar</a>
            @endauth
        </nav>
    </div>
</header>
<main class="flex-1 w-full py-10">
    <div class="mx-auto w-full max-w-5xl px-6 space-y-6">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <p class="mb-2 font-semibold">Ops!</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</main>
<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto max-w-6xl px-6 py-6 text-center text-sm text-slate-500">
        &copy; {{ now()->year }} {{ config('app.name', 'Formulario de Abertura') }}
    </div>
</footer>

</body>
<!-- Fallback Alpine.js CDN para garantir funcionamento do modal -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')
</html>
</body>
</html>




