<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\InternalUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InternalUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:internal');
    }

    private function ensureAdmin(): void
    {
        $current = Auth::guard('internal')->user();

        if (!$current || !$current->isAdmin()) {
            abort(403);
        }
    }

    public function index(): View
    {
        $this->ensureAdmin();

        $users = InternalUser::orderBy('name')->paginate(25);

        return view('internal.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->ensureAdmin();

        return view('internal.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:internal_users,email'],
            'role' => ['required', Rule::in([InternalUser::ROLE_ADMIN, InternalUser::ROLE_COLLABORATOR])],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->letters()->numbers()->symbols(), 'confirmed'],
        ]);

        InternalUser::create($data);

        return redirect()->route('etika.users.index')->with('status', 'Usuário criado com sucesso.');
    }

    public function edit(InternalUser $user): View
    {
        $this->ensureAdmin();

        return view('internal.users.edit', compact('user'));
    }

    public function update(Request $request, InternalUser $user): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('internal_users')->ignore($user->id)],
            'role' => ['required', Rule::in([InternalUser::ROLE_ADMIN, InternalUser::ROLE_COLLABORATOR])],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->letters()->numbers()->symbols(), 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('etika.users.index')->with('status', 'Usuário atualizado com sucesso.');
    }

    public function destroy(InternalUser $user): RedirectResponse
    {
        $this->ensureAdmin();

        if (Auth::guard('internal')->id() === $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        $user->delete();

        return redirect()->route('etika.users.index')->with('status', 'Usuário removido com sucesso.');
    }

    public function profileEdit(): View
    {
        $user = Auth::guard('internal')->user();

        return view('internal.users.profile', compact('user'));
    }

    public function profileUpdate(Request $request): RedirectResponse
    {
        $user = Auth::guard('internal')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('internal_users')->ignore($user->id)],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', Password::min(8)->mixedCase()->letters()->numbers()->symbols(), 'confirmed'],
        ]);

        if (!empty($data['password'])) {
            if (empty($data['current_password']) || !Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Senha atual incorreta.'])->withInput();
            }

            $user->password = $data['password'];
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ])->save();

        return back()->with('status', 'Perfil atualizado com sucesso.');
    }
}
