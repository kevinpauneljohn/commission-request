<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::create(['name' => 'super admin']);
        $financeAdmin = Role::create(['name' => 'business admin']);
        $requestReviewer = Role::create(['name' => 'request reviewer']);
        $admin = Role::create(['name' => 'admin']);
        $salesDirector = Role::create(['name' => 'sales director']);

        //user
        Permission::create(['name' => 'view user'])->assignRole(['admin','business admin']);
        Permission::create(['name' => 'add user'])->assignRole(['admin']);
        Permission::create(['name' => 'edit user'])->assignRole(['admin',]);
        Permission::create(['name' => 'delete user'])->assignRole(['admin']);
        //end user

        //Roles
        Permission::create(['name' => 'view role']);
        Permission::create(['name' => 'add role']);
        Permission::create(['name' => 'edit role']);
        Permission::create(['name' => 'delete role']);
        //end roles

        //Permissions
        Permission::create(['name' => 'view permission']);
        Permission::create(['name' => 'add permission']);
        Permission::create(['name' => 'edit permission']);
        Permission::create(['name' => 'delete permission']);
        //end permissions

        //Permissions
        Permission::create(['name' => 'view request'])->assignRole(['sales director','business admin']);
        Permission::create(['name' => 'add request'])->assignRole(['sales director']);
        Permission::create(['name' => 'edit request'])->assignRole(['sales director']);
        Permission::create(['name' => 'delete request'])->assignRole(['sales director']);
        //end permissions

        //Tasks
        Permission::create(['name' => 'view task'])->assignRole(['business admin']);
        Permission::create(['name' => 'add task'])->assignRole(['business admin']);
        Permission::create(['name' => 'edit task'])->assignRole(['business admin']);
        Permission::create(['name' => 'delete task'])->assignRole(['business admin']);
        Permission::create(['name' => 'assign task'])->assignRole(['business admin']);
        Permission::create(['name' => 'update task status'])->assignRole(['business admin']);
        //end Tasks

        //action taken
        Permission::create(['name' => 'view action taken'])->assignRole(['business admin']);
        Permission::create(['name' => 'add action taken'])->assignRole(['business admin']);
        Permission::create(['name' => 'edit action taken'])->assignRole(['business admin']);
        Permission::create(['name' => 'delete action taken'])->assignRole(['business admin']);
        //end action taken

        //finding
        Permission::create(['name' => 'view finding'])->assignRole(['business admin']);
        Permission::create(['name' => 'add finding'])->assignRole(['business admin']);
        Permission::create(['name' => 'edit finding'])->assignRole(['business admin']);
        Permission::create(['name' => 'delete finding'])->assignRole(['business admin']);
        //end finding
    }
}
