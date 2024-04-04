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
            'email' => 'jhamaicspaunel@gmail.com',
            'username' => 'john',
            'password' => 123
        ]);

        $user2->assignRole('sales director');
        $user2->save();

        $user3 = new User([
            'firstname' => 'Hazel Ry',
            'lastname' => 'world',
            'email' => 'hello@gmail.com',
            'username' => 'hello',
            'password' => 123
        ]);

        $user3->assignRole('business administrator');
        $user3->save();


        $user4 = new User([
            'firstname' => 'jane',
            'lastname' => 'doe',
            'email' => 'jane@gmail.com',
            'username' => 'jane',
            'password' => 123
        ]);

        $user4->assignRole('business administrator');
        $user4->save();

    }
}
