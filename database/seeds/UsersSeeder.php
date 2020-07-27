<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@asd.com',
            'password' => bcrypt('123456'),
        ]);
        $adminUser->assignRole('admin');

        $userUser = User::create([
            'name' => 'User',
            'email' => 'user@asd.com',
            'password' => bcrypt('123456'),
        ]);
        $userUser->assignRole('admin');
    }
}
