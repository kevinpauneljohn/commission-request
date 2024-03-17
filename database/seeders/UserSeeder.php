<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User([
            'firstname' => 'john kevin',
            'lastname' => 'paunel',
            'email' => 'johnkevinpaunel@gmail.com',
            'username' => 'kevinpauneljohn',
            'password' => 123
        ]);

        $user->assignRole('super admin');
        $user->save();

        $user2 = new User([
            'firstname' => 'john',
            'lastname' => 'doe',
            'email' => 'john@gmail.com',
            'username' => 'john',
            'password' => 123
        ]);

        $user2->assignRole('sales director');
        $user2->save();

        $user3 = new User([
            'firstname' => 'hello',
            'lastname' => 'world',
            'email' => 'hello@gmail.com',
            'username' => 'hello',
            'password' => 123
        ]);

        $user3->assignRole('business admin');
        $user3->save();

        $admin = new User([
            'firstname' => 'admin',
            'lastname' => 'admin',
            'email' => 'admin@gmail.com',
            'username' => 'hello',
            'password' => 123
        ]);

        $admin->assignRole('super admin');
        $admin->save();

    }
}
