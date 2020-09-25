<?php

namespace Tests\Feature;

use App\Services\TicketService;
use App\User;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Traits\HasRoles;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    use HasRoles;

    public function setUp() : void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function testUserCanSeeOnlyOwnTickets()
    {
        // GIVEN
        // A (non-admin) user is logged in
        $user = User::inRandomOrder()->where('id', '>', 1)->limit(1)->first();
        $this->post('/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('user'));


        // WHEN
        // Load tickets list
        $tickets = TicketService::getTickets($user);


        // THEN
        // Only user's own tickets are visible
        $it = $this;
        $tickets->each(function ($ticket) use ($it, $user) {
            $it->assertEquals($ticket->user->id, $user->id);
        });
    }
    public function testAdminCanSeeAllTickets()
    {
        // GIVEN
        // An admin user is logged in
        $admin = User::find(1);
        $this->post('/login', [
            'email' => $admin->email,
            'password' => '123456',
        ]);
        $this->assertAuthenticatedAs($admin);
        $this->assertFalse($admin->hasRole('user'));
        $this->assertTrue($admin->hasRole('admin'));


        // WHEN
        // Load tickets list
        $tickets = TicketService::getTickets($admin);

        // THEN
        // Only user's own tickets are visible
        $it = $this;
        $tickets->each(function ($ticket) use ($it, $admin) {
            // Admin don't have own tickets
            $it->assertNotEquals($ticket->user->id, $admin->id);
        });
    }


}
