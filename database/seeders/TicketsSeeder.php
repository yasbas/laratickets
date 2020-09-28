<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class TicketsSeeder extends Seeder
{
    public function run(Faker $faker)
    {
        User::all()->each(function ($user) use ($faker) {
            if ($user->hasRole(User::ROLE_USER)) {
                if ($faker->boolean(75)) {
                    Ticket::factory()->times(rand(1, 5))->create([
                        'user_id' => $user->id,
                    ]);
                }
            }
        });


    }
}
