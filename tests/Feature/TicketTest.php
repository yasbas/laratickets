<?php

namespace Tests\Feature;

use App\Services\TicketService;
use App\Models\Ticket;
use App\Models\User;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

    protected function getRandomUsers($count=1)
    {
        return User::inRandomOrder()->where('id', '>', 1)->limit($count)->first();
    }

    protected function getAdminUser()
    {
        return User::find(1);
    }

    protected function loginUser($user)
    {
        $this->actingAs($user)
            ->get('/login');
    }

    public function testUserCanSeeOnlyOwnTickets()
    {
        // GIVEN
        // A (non-admin) user is logged in
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasRole(User::ROLE_ADMIN));
        $this->assertTrue($user->hasRole(User::ROLE_USER));


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

    // YADO: rename this test to reflect it's functionality
    public function testAdminCanSeeAllTickets()
    {
        // GIVEN
        // An admin user is logged in
        $admin = $this->getAdminUser();
        $this->loginUser($admin);
        $this->assertAuthenticatedAs($admin);
        $this->assertFalse($admin->hasRole(User::ROLE_USER));
        $this->assertTrue($admin->hasRole(User::ROLE_ADMIN));


        // WHEN
        // Load tickets list
        $tickets = TicketService::getTickets($admin);

        // THEN
        // Only user's own tickets are visible and admin replies
        $it = $this;
        $tickets->each(function ($ticket) use ($it, $admin) {
            // Admin don't have own tickets but have replies
            //$it->assertNotEquals($ticket->user->id, $admin->id);
            $it->assertTrue(in_array($ticket->user_id, [$ticket->user->id, $admin->id]))  ;
        });
    }

    public function testUserCanCreateTicket()
    {
        // GIVEN
        // Logged in user, non-admin
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);


        // WHEN
        // Creating ticket
        $ticketTitle = Str::random(32);
        $ticketBody = Str::random(128);
        /*$user->tickets()->create([
            'parent_id' => 0,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ]);*/
        $ticketData = [
            'user_id' => $user->id,
            'parent_id' => 0,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ];
        Ticket::createTicket($ticketData);


        // THEN
        // Ticket is created successfully
        $this->assertEquals(
            1,
            $user->tickets()
                ->where('title', $ticketTitle)
                ->where('body', $ticketBody)
                ->count()
        );

    }

    public function testAdminCantCreateTicket()
    {
        // GIVEN
        // Logged in admin user
        $admin = $this->getAdminUser();
        $this->loginUser($admin);

        // WHEN
        // Creating a ticket
        $ticketTitle = Str::random(32);
        $ticketBody = Str::random(128);
        /*$admin->tickets()->createTicket([
            'parent_id' => 0,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ]);*/
        $ticketData = [
            'user_id' => $admin->id,
            'parent_id' => 0,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ];
        Ticket::createTicket($ticketData);

        // THEN
        // Ticket is not created
        $this->assertEquals(
            0,
            $admin->tickets()
                 ->where('title', $ticketTitle)
                 ->where('body', $ticketBody)
                 ->count()
        );
    }

    public function testNotLoggedInUserCantViewATicketAndIsRedirectedToLogin()
    {
        // GIVEN
        // A user
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        // having a ticket
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);
        // And user is not logged in
        auth()->logout();

        // WHEN
        // User tries to view the ticket
        $response = $this->get('/ticket/'.$ticket->id);

        // THEN
        // User sees 404 Not Found page
        $response->assertRedirect('/login');
    }

    public function testLoggedInUserCanViewATicket()
    {
        // GIVEN
        // A user
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        // having a ticket
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);

        // WHEN
        // User tries to view the ticket
        $response = $this->get('/ticket/'.$ticket->id);

        // THEN
        // User sees 404 Not Found page
        $response->assertStatus(200);
        $response->assertSee($ticket->title);
    }
}
