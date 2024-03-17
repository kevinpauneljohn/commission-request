<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function addUser(array $userData)
    {
        $user = new User([
            'firstname' => $userData['firstname'],
            'middlename' => $userData['middlename'],
            'lastname' => $userData['lastname'],
            'date_of_birth' => $userData['date_of_birth'],
            'mobile_number' => $userData['mobile_number'],
            'email' => $userData['email'],
            'username' => $userData['username'],
            'password' => $userData['password'],
            'role' => array($userData['role'])
        ]);
        $user->assignRole($userData['role']);
        if($user->save()){
            return true;
        }
        return false;
    }

    public function updateUser(array $userData, $user_id): bool
    {
        $user = User::findOrFail($user_id);
        $user->firstname = $userData['firstname'];
        $user->middlename = $userData['middlename'];
        $user->lastname = $userData['lastname'];
        $user->date_of_birth = $userData['date_of_birth'];
        $user->mobile_number = $userData['mobile_number'];
        $user->email = $userData['email'];
        $user->role = array($userData['role']);

        if($user->isDirty())
        {
            $user->syncRoles($userData['role']);
            $user->save();
            return true;
        }
        return false;
    }

}
