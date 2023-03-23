<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAttribute;
use App\Models\UserRoleRelation;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userID = rand(1, 99);

        $user = new User;
        $user->id = $userID;
        $user->username = env('ADMIN_USERNAME','posuser');
        $user->password = Hash::make( env('ADMIN_PASSWORD','rocks22'));
        $user->email = env('ADMIN_EMAIL','test@test.com');
        $user->author = $userID;
        $user->active = true; // first user active by default;
        $user->save();

        $userattribute = new UserAttribute;
        $userattribute->user_id = $userID;
        $userattribute->save();

        $userRoleRelatioin = new UserRoleRelation();
        $userRoleRelatioin->role_id = 2;
        $userRoleRelatioin->user_id = $userID;
        $userRoleRelatioin->save();
    }
}
