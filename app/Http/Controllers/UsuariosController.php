<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'usuarios.manage');
        $users = User::orderBy('name', 'asc')->paginate(10);
        return view('pages.usuarios.index', [
            'title' => 'Usuários',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'usuarios.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'group_id' => ['nullable', 'integer', 'exists:user_groups,id'],
        ]);

        User::create($data);
        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $usuario)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'usuarios.manage');
        $groups = \App\Models\UserGroup::orderBy('name','asc')->get();
        return view('pages.usuarios.edit', [
            'title' => 'Editar Usuário',
            'user' => $usuario,
            'groups' => $groups,
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'usuarios.manage');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:6'],
            'group_id' => ['nullable', 'integer', 'exists:user_groups,id'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $usuario->fill($data);
        $usuario->save();
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $usuario)
    {
        \Illuminate\Support\Facades\Gate::authorize('use-feature', 'usuarios.manage');
        User::destroy($usuario->id);
        return redirect()->route('usuarios.index')->with('success', 'Usuário removido.');
    }
}
