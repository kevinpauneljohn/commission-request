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
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'business administrator']);
        Role::create(['name' => 'sales director']);
        Role::create(['name' => 'sales administrator']);
        Role::create(['name' => 'finance administrator']);

        //user
        Permission::create(['name' => 'view user'])->assignRole(['business administrator','sales administrator']);
        Permission::create(['name' => 'add user'])->assignRole(['sales administrator']);
        Permission::create(['name' => 'edit user'])->assignRole(['sales administrator']);
        Permission::create(['name' => 'delete user'])->assignRole(['sales administrator']);
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
        Permission::create(['name' => 'view request'])->assignRole(['sales director','business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'add request'])->assignRole(['sales director','business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'edit request'])->assignRole(['sales director','business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'delete request'])->assignRole(['sales director','business administrator','finance administrator','sales administrator']);
        //end permissions

        //Tasks
        Permission::create(['name' => 'view task'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'add task'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'edit task'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'delete task'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'assign task'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'update task status'])->assignRole(['business administrator','finance administrator','sales administrator']);
        //end Tasks

        //action taken
        Permission::create(['name' => 'view action taken'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'add action taken'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'edit action taken'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'delete action taken'])->assignRole(['business administrator','finance administrator','sales administrator']);
        //end action taken

        //finding
        Permission::create(['name' => 'view finding'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'add finding'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'edit finding'])->assignRole(['business administrator','finance administrator','sales administrator']);
        Permission::create(['name' => 'delete finding'])->assignRole(['business administrator','finance administrator','sales administrator']);
        //end finding

        //automation
        Permission::create(['name' => 'view automation'])->assignRole(['sales administrator']);
        Permission::create(['name' => 'add automation'])->assignRole(['sales administrator']);
        Permission::create(['name' => 'edit automation'])->assignRole(['sales administrator']);
        Permission::create(['name' => 'delete automation'])->assignRole(['sales administrator']);
        //end finding

        //automation
        Permission::create(['name' => 'view commission voucher'])->assignRole(['sales administrator','finance administrator']);
        Permission::create(['name' => 'add commission voucher'])->assignRole(['sales administrator','finance administrator']);
        Permission::create(['name' => 'edit commission voucher'])->assignRole(['sales administrator','finance administrator']);
        Permission::create(['name' => 'delete commission voucher'])->assignRole(['sales administrator','finance administrator']);
        //end finding


        Permission::create(['name' => 'use multi task'])->assignRole(['sales administrator','business administrator','finance administrator',]);
    }
}
