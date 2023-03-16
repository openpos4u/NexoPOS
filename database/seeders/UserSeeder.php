<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
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
        $user->username = env('ADMIN_USERNAME');
        $user->password = Hash::make( env('ADMIN_PASSWORD'));
        $user->email = env('ADMIN_EMAIL');
        $user->author = $userID;
        $user->active = true; // first user active by default;
        $user->save();


    }
}
