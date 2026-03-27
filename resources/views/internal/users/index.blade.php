@extends('internal.layouts.app')

@section('title', 'Usuários | Painel Etika')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-slate-900">Usuários internos</h1>
            <p class="text-sm text-slate-600">Gerencie a equipe de acesso e permissões.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('etika.dashboard') }}" class="btn-secondary">Voltar ao painel principal</a>
            <a href="{{ route('etika.users.profile.edit') }}" class="btn-secondary">Meu perfil</a>
            <a href="{{ route('etika.users.create') }}" class="btn">Criar usuário</a>
        </div>
    </div>

    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">E-mail</th>
                    <th class="px-4 py-3 text-left">Cargo</th>
                    <th class="px-4 py-3 text-left">Último login</th>
                    <th class="px-4 py-3 text-left">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ ucfirst($user->role) }}</td>
                        <td class="px-4 py-3">{{ optional($user->last_login_at)->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('etika.users.edit', $user) }}" class="btn-secondary">Editar</a>
                            @if(auth('internal')->id() !== $user->id)
                                <form action="{{ route('etika.users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger" onclick="return confirm('Excluir este usuário?');">Excluir</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500">Nenhum usuário criado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
