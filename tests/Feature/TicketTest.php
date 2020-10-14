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

    protected function getRandomUser()
    {
        return User::inRandomOrder()->role(User::ROLE_USER)->first();
    }

    protected function getRandomAdminUser()
    {
        return User::inRandomOrder()->role(User::ROLE_ADMIN)->first();
    }

    protected function loginUser($user)
    {
        $this->actingAs($user)
            ->get('/login');
    }

    protected function createTicket()
    {
        $user = auth()->user();
        $ticketTitle = Str::random(32);
        $ticketBody = Str::random(128);
        $ticketData = [
            'user_id' => $user->id,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ];

        //return Ticket::create($ticketData);
        return TicketService::createTicket($ticketData);
    }

    protected function addTicketReply(Ticket $ticket)
    {
        $reply = Str::random(128);

        return TicketService::addTicketReply($ticket, $reply);
    }

    protected function makeTicketReply(Ticket $ticket)
    {
        $reply = Str::random(128);

        return TicketService::makeTicketReply($ticket, $reply);
    }

    public function testUserCanSeeOnlyOwnTickets()
    {
        // GIVEN
        // A (non-admin) user is logged in
        $user = $this->getRandomUser();
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

    public function testAdminCanSeeAllTicketsButCantOwnATicket()
    {
        // GIVEN
        // An admin user is logged in
        $admin = $this->getRandomAdminUser();
        $this->loginUser($admin);
        $this->assertAuthenticatedAs($admin);
        $this->assertFalse($admin->hasRole(User::ROLE_USER));
        $this->assertTrue($admin->hasRole(User::ROLE_ADMIN));


        // WHEN
        // Load tickets list
        $tickets = TicketService::getTickets($admin);

        // THEN
        // All tickets are loaded
        $this->assertEquals($tickets->count(), Ticket::count());

        $it = $this;
        $tickets->each(function ($ticket) use ($it, $admin) {
            // And all tickets belong to users
            $it->assertTrue($ticket->user->hasRole(User::ROLE_USER));
            // And no ticket belongs to admin
            $it->assertFalse($ticket->user->hasRole(User::ROLE_ADMIN));
        });
    }

    public function testUserCanCreateTicket()
    {
        // GIVEN
        // Logged in user, non-admin
        $user = $this->getRandomUser();
        $this->loginUser($user);


        // WHEN
        // Creating ticket
        $ticket = $this->createTicket();


        // THEN
        // Ticket is created successfully
        $this->assertEquals(
            1,
            $user->tickets()
                ->where('title', $ticket->title)
                ->where('body', $ticket->body)
                ->count()
        );
    }

    public function testAdminCantCreateTicket()
    {
        // GIVEN
        // Logged in admin user
        $admin = $this->getRandomAdminUser();
        $this->loginUser($admin);

        // WHEN
        // Creating a ticket
        $ticket = $this->createTicket();

        // THEN
        // Ticket is not created
        $this->assertNull($ticket);
    }

    public function testNotLoggedInUserCantViewATicketAndIsRedirectedToLogin()
    {
        // GIVEN
        // A user
        $user = $this->getRandomUser();
        $this->loginUser($user);
        // having a ticket
        $ticket = $this->createTicket();
        // And user is not logged in
        auth()->logout();

        // WHEN
        // User tries to view the ticket
        $response = $this->get('/ticket/'.$ticket->id);

        // THEN
        // User sees 404 Not Found page
        $response->assertRedirect('/login');
    }

    public function testLoggedInUserCanViewOwnTicket()
    {
        // GIVEN
        // A user
        $user = $this->getRandomUser();
        $this->loginUser($user);
        // having a ticket
        $ticket = $this->createTicket();

        // WHEN
        // User tries to view the ticket
        $response = $this->get('/ticket/'.$ticket->id);

        // THEN
        // User sees 404 Not Found page
        $response->assertStatus(200);
        $response->assertSee($ticket->title);
    }

    public function testAdminCanAddReplyToATicket()
    {
        // GIVEN
        // There are some tickets created by regular users
        $user = $this->getRandomUser();
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And logged in as admin user
        $admin = $this->getRandomAdminUser();
        $this->loginUser($admin);


        // WHEN
        // Admin creates a reply to a ticket
        $ticketReply = $this->addTicketReply($ticket);


        // THEN
        // The reply is successfully created in database
        $this->assertDatabaseHas('ticket_replies', [
            'id' => $ticketReply->id,
            'user_id' => $admin->id,
            'body' => $ticketReply->body,
        ]);
    }

    public function testUserCanAddReplyToOwnTicket()
    {
        // GIVEN
        // There are some tickets created by regular users
        $user = $this->getRandomUser();
        $this->loginUser($user);
        $ticket = $this->createTicket();


        // WHEN
        // User creates a reply to the ticket
        $ticketReply = $this->addTicketReply($ticket);


        // THEN
        // The reply is successfully created in database
        $this->assertDatabaseHas('ticket_replies', [
            'id' => $ticketReply->id,
            'user_id' => $user->id,
            'body' => $ticketReply->body,
        ]);
    }

    public function testUserCantAddReplyToNotOwnTicket()
    {
        // GIVEN
        // There are some tickets created by regular users
        $user1 = $this->getRandomUser();
        $this->loginUser($user1);
        $user1Ticket = $this->createTicket();

        $user2 = $this->getRandomUser();
        $this->loginUser($user2);



        // WHEN
        // Another User tries to create a reply to the ticket
        $ticketReply = $this->addTicketReply($user1Ticket);


        // THEN
        // The reply is not created
        $this->assertNull($ticketReply);
    }

    public function testUserCanPostTicketReply()
    {
        // GIVEN
        // A user has created a ticket
        $user = $this->getRandomUser();
        $this->loginUser($user);
        $ticket = $this->createTicket();


        // WHEN
        // User try to post a ticket reply via the web form
        //$this->addTicketReply($ticket, $user);
        $ticketReply = $this->makeTicketReply($ticket);
        $this->post('/ticket/'.$ticket->id, $ticketReply->toArray());


        // THEN
        // The reply is successfully added to ticket
        $this->get('/ticket/'.$ticket->id)
            ->assertSee($ticketReply->body);
    }
}
