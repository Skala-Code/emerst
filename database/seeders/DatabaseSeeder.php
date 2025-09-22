<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'advogado']);
        Role::firstOrCreate(['name' => 'colaborador']);

        // Create super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@emerst.com',
            'password' => Hash::make('admin123'),
        ]);
        $user->assignRole('super_admin');

        // Run our deadline management seeder
        $this->call([
            DeadlineManagementSeeder::class,
        ]);
    }
}
