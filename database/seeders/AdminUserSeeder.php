<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $newUser=new User();
        $newUser->first_name="admin";
        $newUser->email="admin@yopmail.com";
        $newUser->password=Hash::make('123456');
        $newUser->role="1";
        $newUser->is_verified="1";
        $newUser->save();
    }
}
