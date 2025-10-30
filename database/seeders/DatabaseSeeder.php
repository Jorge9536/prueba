<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Primero crear los roles
        $this->call(RoleSeeder::class);

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
    }
}