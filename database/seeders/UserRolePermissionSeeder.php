<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'create role']);
        Permission::create(['name' => 'update role']);
        Permission::create(['name' => 'delete role']);

        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'update permission']);
        Permission::create(['name' => 'delete permission']);

        Permission::create(['name' => 'view user']);
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'update user']);
        Permission::create(['name' => 'delete user']);

        Permission::create(['name' => 'view archived user']);
        Permission::create(['name' => 'create archived user']);
        Permission::create(['name' => 'update archived user']);
        Permission::create(['name' => 'delete archived user']);

        Permission::create(['name' => 'view setting']);
        Permission::create(['name' => 'create setting']);
        Permission::create(['name' => 'update setting']);
        Permission::create(['name' => 'delete setting']);

        Permission::create(['name' => 'view children']);
        Permission::create(['name' => 'create children']);
        Permission::create(['name' => 'update children']);
        Permission::create(['name' => 'delete children']);

        Permission::create(['name' => 'view subject']);
        Permission::create(['name' => 'create subject']);
        Permission::create(['name' => 'update subject']);
        Permission::create(['name' => 'delete subject']);

        Permission::create(['name' => 'view teacher']);
        Permission::create(['name' => 'create teacher']);
        Permission::create(['name' => 'update teacher']);
        Permission::create(['name' => 'delete teacher']);

        Permission::create(['name' => 'view class groups']);
        Permission::create(['name' => 'create class groups']);
        Permission::create(['name' => 'update class groups']);
        Permission::create(['name' => 'delete class groups']);

        Permission::create(['name' => 'view class group schedules']);
        Permission::create(['name' => 'create class group schedules']);
        Permission::create(['name' => 'update class group schedules']);
        Permission::create(['name' => 'delete class group schedules']);

        Permission::create(['name' => 'view attendance']);
        Permission::create(['name' => 'create attendance']);
        Permission::create(['name' => 'update attendance']);
        Permission::create(['name' => 'delete attendance']);

        // Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']); //as super-admin
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $parentRole = Role::create(['name' => 'parent']);
        $studentRole = Role::create(['name' => 'student']);

        // give all permissions to super-admin role.
        $allPermissionNames = Permission::pluck('name')->toArray();

        $superAdminRole->givePermissionTo($allPermissionNames);

        // give permissions to admin role.
        $adminRole->givePermissionTo(['view role']);
        $adminRole->givePermissionTo(['view permission']);
        $adminRole->givePermissionTo(['create user', 'view user', 'update user']);

        // give permissions to parent
        $parentRole->givePermissionTo(['create children', 'view children', 'update children']);
        $parentRole->givePermissionTo(['view subject']);

        // give permissions to teacher
        $teacherRole->givePermissionTo(['view class groups']);
        $teacherRole->givePermissionTo(['create attendance', 'view attendance', 'update attendance', 'delete attendance']);


        // Create User and assign Role to it.

        $superAdminUser = User::firstOrCreate([
                    'email' => 'admin@gmail.com',
                ], [
                    'name' => 'Admin',
                    'email' => 'admin@gmail.com',
                    'username' => 'admin',
                    'password' => Hash::make ('admin123'),
                    'email_verified_at' => now(),

                ]);

        $superAdminUser->assignRole($superAdminRole);

        $superAdminProfile = $superAdminUser->profile()->firstOrCreate([
            'user_id' => $superAdminUser->id,
        ], [
            'user_id' => $superAdminUser->id,
            'first_name' => $superAdminUser->name,
        ]);
    }
}
