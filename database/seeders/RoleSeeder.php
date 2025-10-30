<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Ejecutar el seeder
     */
    public function run()
    {
        // Limpiar cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('=== INICIANDO CREACIÓN DE ROLES Y PERMISOS ===');

        // ========== CREAR PERMISOS ==========
        $this->command->info('Creando permisos...');

        $permissions = [
            [
                'name' => 'manage users',
                'description' => 'Permite gestionar usuarios del sistema'
            ],
            [
                'name' => 'manage events', 
                'description' => 'Permite gestionar todos los eventos del sistema'
            ],
            [
                'name' => 'manage roles',
                'description' => 'Permite gestionar roles y permisos del sistema'
            ],
            [
                'name' => 'create admin events',
                'description' => 'Permite crear eventos globales para todos los usuarios'
            ]
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
            $this->command->info("Permiso creado: {$permissionData['name']} - {$permissionData['description']}");
        }

        // ========== CREAR ROLES ==========
        $this->command->info(PHP_EOL . 'Creando roles...');

        $roles = [
            [
                'name' => 'super-admin',
                'description' => 'Super Administrador - Acceso completo al sistema',
                'permissions' => ['manage users', 'manage events', 'manage roles', 'create admin events']
            ],
            [
                'name' => 'admin',
                'description' => 'Administrador - Gestión de usuarios y eventos',
                'permissions' => ['manage users', 'manage events', 'create admin events']
            ],
            [
                'name' => 'user', 
                'description' => 'Usuario - Acceso básico a eventos personales',
                'permissions' => []
            ]
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Asignar permisos al rol
            if (!empty($roleData['permissions'])) {
                $role->syncPermissions($roleData['permissions']);
            }

            $this->command->info("Rol creado: {$roleData['name']} - {$roleData['description']}");
            if (!empty($roleData['permissions'])) {
                $this->command->info("  Permisos asignados: " . implode(', ', $roleData['permissions']));
            }
        }

        // ========== CREAR USUARIOS DE PRUEBA ==========
        $this->command->info(PHP_EOL . 'Creando usuarios de prueba...');

        // Super Administrador
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@agenda.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super-admin');
        $this->command->info("✅ Super Admin creado: superadmin@agenda.com / password");

        // Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@agenda.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        $this->command->info("✅ Admin creado: admin@agenda.com / password");

        // Usuarios normales
        $users = [
            [
                'name' => 'Usuario Uno',
                'email' => 'usuario1@agenda.com',
            ],
            [
                'name' => 'Usuario Dos', 
                'email' => 'usuario2@agenda.com',
            ],
            [
                'name' => 'Usuario Tres',
                'email' => 'usuario3@agenda.com',
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');
            $this->command->info("✅ Usuario creado: {$userData['email']} / password");
        }

        // ========== RESUMEN FINAL ==========
        $this->command->info(PHP_EOL . '=== RESUMEN DE CREACIÓN ===');
        $this->command->info("Total permisos creados: " . Permission::count());
        $this->command->info("Total roles creados: " . Role::count());
        $this->command->info("Total usuarios creados: " . User::count());

        $this->command->info(PHP_EOL . '=== CREDENCIALES DE ACCESO ===');
        $this->command->info("Super Administrador: superadmin@agenda.com / password");
        $this->command->info("Administrador: admin@agenda.com / password"); 
        $this->command->info("Usuario normal: usuario1@agenda.com / password");
        $this->command->info("Usuario normal: usuario2@agenda.com / password");
        $this->command->info("Usuario normal: usuario3@agenda.com / password");

        $this->command->info(PHP_EOL . '✅ Seeder de roles y permisos ejecutado exitosamente!');
    }
}