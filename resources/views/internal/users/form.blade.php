<div class="card space-y-6">
    <form method="POST" action="{{ $action }}" class="space-y-6">
        @csrf
        @if($method === 'PUT')
            @method('PUT')
        @endif

        <div class="space-y-1">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
        </div>

        <div class="space-y-1">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
        </div>

        <div class="space-y-1">
            <label for="role" class="form-label">Perfil</label>
            <select id="role" name="role" class="form-control" required>
                @php
                    $role = old('role', $user->role ?? 'collaborator');
                @endphp
                <option value="{{ App\Models\InternalUser::ROLE_ADMIN }}" @selected($role === App\Models\InternalUser::ROLE_ADMIN)>Admin</option>
                <option value="{{ App\Models\InternalUser::ROLE_COLLABORATOR }}" @selected($role === App\Models\InternalUser::ROLE_COLLABORATOR)>Colaborador</option>
            </select>
        </div>

        <div class="space-y-1">
            <label for="password" class="form-label">Senha</label>
            <input id="password" name="password" type="password" class="form-control" {{ $requirePassword ?? false ? 'required' : '' }}>
            <small class="text-slate-500">{{ $passwordHelpText }}</small>
        </div>

        <div class="space-y-1">
            <label for="password_confirmation" class="form-label">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" {{ $requirePassword ?? false ? 'required' : '' }}>
        </div>

        <button type="submit" class="btn">{{ $buttonLabel }}</button>
    </form>
</div>
