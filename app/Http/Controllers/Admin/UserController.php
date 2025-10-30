<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
{
    if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
        return redirect()->route('agenda.calendar')
            ->with('error', 'No tienes permisos para acceder a esta sección.');
    }

    $users = User::withCount('events')->with('roles')->latest()->get();
    $roles = Role::all();
    return view('admin.users.index', compact('users', 'roles'));
}

    public function create()
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole($request->role);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name'
        ]);

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->password) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);
            $user->syncRoles([$request->role]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->hasRole(['super-admin', 'admin'])) {
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        try {
            if ($user->id === auth()->id()) {
                return back()->with('error', 'No puedes eliminar tu propio usuario.');
            }

            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}