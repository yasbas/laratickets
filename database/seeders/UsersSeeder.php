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
        User::factory()->times(150)->create()->each(function ($user) {
            // Random add ADMIN role for like 10% of the users
            //$role = rand(1, 10) == 1 ? User::ROLE_ADMIN : User::ROLE_USER;
            $rolesRandomize = [
                User::ROLE_ADMIN,
                User::ROLE_SUPPORT_AGENT,
                User::ROLE_SUPPORT_AGENT,
                User::ROLE_SUPPORT_AGENT,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
                User::ROLE_USER,
            ];

            $role = $rolesRandomize[rand(0, count($rolesRandomize)-1)];

            $user->assignRole($role);
        });
    }
}
