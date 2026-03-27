<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Painel Etika')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen flex-col">
        <header class="text-white" style="background:#660000; color:#ffffff;">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-4">
                <div class="text-lg font-semibold">Painel Etika</div>
                @auth('internal')
                    <div class="flex items-center gap-4 text-sm">
                        <a href="{{ route('etika.users.profile.edit') }}" class="text-sm underline">Meu perfil</a>
                        @if(auth('internal')->user()->isAdmin())
                            <a href="{{ route('etika.users.index') }}" class="text-sm underline">Usuários</a>
                        @endif
                        <span>{{ auth('internal')->user()->name }}</span>
                        <form method="POST" action="{{ route('etika.logout') }}">
                            @csrf
                            <button type="submit" class="btn-secondary px-4 py-2 text-sm">Sair</button>
                        </form>
                    </div>
                @endauth
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto w-full max-w-6xl px-6 py-10">
                @if(session('status'))
                    <div class="alert alert-success mb-6">{{ session('status') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error mb-6">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error mb-6">
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
                &copy; {{ now()->year }} Painel Etika
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
