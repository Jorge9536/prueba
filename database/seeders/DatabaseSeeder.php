<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'manage users',
            'manage events', 
            'manage roles',
            'create admin events'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Asignar permisos a roles
        $superAdminRole->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo(['manage users', 'manage events', 'create admin events']);

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@agenda.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        // Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@agenda.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Usuarios normales
        $user1 = User::create([
            'name' => 'Usuario Uno',
            'email' => 'usuario1@agenda.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user1->assignRole('user');

        $user2 = User::create([
            'name' => 'Usuario Dos',
            'email' => 'usuario2@agenda.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');

        echo "Usuarios creados:\n";
        echo "Super Admin: superadmin@agenda.com / password\n";
        echo "Admin: admin@agenda.com / password\n";
        echo "Usuario: usuario1@agenda.com / password\n";
        echo "Usuario: usuario2@agenda.com / password\n";
    }
}