<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Company permissions
            'view_companies',
            'create_companies',
            'edit_companies',
            'delete_companies',

            // Office permissions
            'view_offices',
            'create_offices',
            'edit_offices',
            'delete_offices',

            // Lawyer permissions
            'view_lawyers',
            'create_lawyers',
            'edit_lawyers',
            'delete_lawyers',

            // Process permissions
            'view_processes',
            'create_processes',
            'edit_processes',
            'delete_processes',
            'view_own_processes',
            'edit_own_processes',

            // Service Order permissions
            'view_service_orders',
            'create_service_orders',
            'edit_service_orders',
            'delete_service_orders',
            'view_own_service_orders',
            'edit_own_service_orders',

            // Custom Fields permissions
            'view_custom_fields',
            'create_custom_fields',
            'edit_custom_fields',
            'delete_custom_fields',

            // Custom Tabs permissions
            'view_custom_tabs',
            'create_custom_tabs',
            'edit_custom_tabs',
            'delete_custom_tabs',

            // Document permissions
            'view_documents',
            'create_documents',
            'edit_documents',
            'delete_documents',
            'download_documents',

            // User management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',

            // Advanced permissions
            'access_admin_panel',
            'view_analytics',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. Super Admin - Full access
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Admin - Full business access except user management
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view_companies', 'create_companies', 'edit_companies', 'delete_companies',
            'view_offices', 'create_offices', 'edit_offices', 'delete_offices',
            'view_lawyers', 'create_lawyers', 'edit_lawyers', 'delete_lawyers',
            'view_processes', 'create_processes', 'edit_processes', 'delete_processes',
            'view_service_orders', 'create_service_orders', 'edit_service_orders', 'delete_service_orders',
            'view_custom_fields', 'create_custom_fields', 'edit_custom_fields', 'delete_custom_fields',
            'view_custom_tabs', 'create_custom_tabs', 'edit_custom_tabs', 'delete_custom_tabs',
            'view_documents', 'create_documents', 'edit_documents', 'delete_documents', 'download_documents',
            'access_admin_panel', 'view_analytics', 'export_data',
        ]);

        // 3. Advogado - Can manage processes and service orders
        $advogado = Role::create(['name' => 'advogado']);
        $advogado->givePermissionTo([
            'view_companies', 'view_offices', 'view_lawyers',
            'view_processes', 'create_processes', 'edit_processes',
            'view_own_processes', 'edit_own_processes',
            'view_service_orders', 'create_service_orders', 'edit_service_orders',
            'view_own_service_orders', 'edit_own_service_orders',
            'view_documents', 'create_documents', 'edit_documents', 'download_documents',
            'access_admin_panel',
        ]);

        // 4. Colaborador - Limited access, mainly viewing and basic editing
        $colaborador = Role::create(['name' => 'colaborador']);
        $colaborador->givePermissionTo([
            'view_companies', 'view_offices', 'view_lawyers',
            'view_processes', 'view_own_processes',
            'view_service_orders', 'edit_service_orders', 'view_own_service_orders', 'edit_own_service_orders',
            'view_documents', 'create_documents', 'download_documents',
            'access_admin_panel',
        ]);

        // 5. Cliente - Very limited access, mainly viewing
        $cliente = Role::create(['name' => 'cliente']);
        $cliente->givePermissionTo([
            'view_own_processes',
            'view_own_service_orders',
            'view_documents', 'download_documents',
        ]);

        // Assign role to existing admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }

        // Create some sample users with different roles
        $advogadoUser = User::create([
            'name' => 'Dr. Advogado',
            'email' => 'advogado@exemplo.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $advogadoUser->assignRole('advogado');

        $colaboradorUser = User::create([
            'name' => 'Colaborador Silva',
            'email' => 'colaborador@exemplo.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $colaboradorUser->assignRole('colaborador');

        $clienteUser = User::create([
            'name' => 'Cliente Santos',
            'email' => 'cliente@exemplo.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $clienteUser->assignRole('cliente');
    }
}
