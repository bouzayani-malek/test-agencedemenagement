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
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        // Permission categories 
        Permission::create(['name' => 'view categories']);
        Permission::create(['name' => 'create categories']);
        Permission::create(['name' => 'update categories']);
        Permission::create(['name' => 'delete categories']);
        // Permission events 
        Permission::create(['name' => 'view events']);
        Permission::create(['name' => 'create events']);
        Permission::create(['name' => 'update events']);
        Permission::create(['name' => 'delete events']);
        // Permission Réservation 
        Permission::create(['name' => 'view reservations']);
        Permission::create(['name' => 'create reservations']);
        Permission::create(['name' => 'update reservations']);

        // Permission::create(['name' => 'view reservations']);
        // Permission::create(['name' => 'manage users']);

        $adminRole->givePermissionTo([
            'view categories',
            'create categories', 
            'update categories', 
            'delete categories', 
            'view events',
            'create events', 
            'update events', 
            'delete events',
            'view reservations',
            'create reservations', 
            'update reservations'
        ]);
        $userRole->givePermissionTo([
            'view reservations',
            'create reservations', 
            'update reservations',
            'view categories',
            'view events',

        ]);
        $user = User::create([
            'name' => 'malek',
            'email' => 'bouzayani@gmail.com',
            'password' => Hash::make('malek#123'), 
        ]);

        $user->assignRole('admin');

        $user = User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user#123'), 
        ]);

        $user->assignRole('user');
    }
}
