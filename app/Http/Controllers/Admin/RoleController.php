<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return redirect()->route('agenda.calendar')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol creado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el rol: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'No tienes permisos para realizar esta acción.');
        }

        try {
            $role = Role::findOrFail($id);
            
            if (in_array($role->name, ['super-admin', 'admin', 'user'])) {
                return back()->with('error', 'No puedes eliminar este rol del sistema.');
            }

            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol eliminado exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el rol: ' . $e->getMessage());
        }
    }
}