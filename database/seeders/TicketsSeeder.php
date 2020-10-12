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
                    // Create ticket
                    $tickets = Ticket::factory()->times(rand(1, 5))->create([
                        'user_id' => $user->id,
                    ]);
                    // Create replies
                    $tickets->each(function ($ticket) use ($faker, $user) {
                        Ticket::factory()->times(rand(1, 5))->create([
                            // Reply is either from the user or the admin (admin id = 1)
                            'user_id' => $faker->randomElement([$user->id ,1]),
                            'parent_id' => $ticket->id,
                        ]);
                    });
                }
            }
        });


    }
}
