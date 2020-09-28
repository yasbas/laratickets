<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // Create Admin/Support Agent user
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@asd.com',
            'password' => bcrypt('123456'),
        ]);
        $adminUser->assignRole(User::ROLE_ADMIN);

        // Create normal users
        //factory(App\User::class, 50)->create()->each(function ($user) {
        User::factory()->times(50)->create()->each(function ($user) {
            $user->assignRole(User::ROLE_USER);
        });
    }
}
