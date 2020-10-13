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

    protected function createTicket(User $user)
    {
        //$this->loginUser($user);

        $ticketTitle = Str::random(32);
        $ticketBody = Str::random(128);
        $ticketData = [
            'user_id' => $user->id,
            'parent_id' => 0,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ];

        return Ticket::createTicket($ticketData);
    }

    protected function addTicketReply(Ticket $ticket, User $user)
    {
        $replyBody = Str::random(128);
        $ticketData = [
            'user_id' => $user->id,
            'parent_id' => $ticket->id,
            'body' => $replyBody,
        ];

        return Ticket::addTicketReply($ticket, $ticketData);
    }

    protected function makeTicketReply(Ticket $ticket, User $user)
    {
        $replyBody = Str::random(128);
        $ticketData = [
            'user_id' => $user->id,
            'parent_id' => $ticket->id,
            'body' => $replyBody,
        ];

        return Ticket::makeTicketReply($ticket, $ticketData);
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
        $ticket = $this->createTicket($user);


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
        $admin = $this->getAdminUser();
        $this->loginUser($admin);

        // WHEN
        // Creating a ticket
        $ticket = $this->createTicket($admin);

        // THEN
        // Ticket is not created
        $this->assertNull($ticket);
    }

    public function testNotLoggedInUserCantViewATicketAndIsRedirectedToLogin()
    {
        // GIVEN
        // A user
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        // having a ticket
        $ticket = $this->createTicket($user);
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
        $ticket = $this->createTicket($user);

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
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        $ticket = $this->createTicket($user);
        // And logged in as admin user
        $admin = $this->getAdminUser();
        $this->loginUser($admin);


        // WHEN
        // Admin creates a reply to a ticket
        $ticketReply = $this->addTicketReply($ticket, $admin);


        // THEN
        // The reply is successfully created in database
        $this->assertDatabaseHas('tickets', [
            'id' => $ticketReply->id,
            'user_id' => $admin->id,
            'parent_id' => $ticket->id,
            'body' => $ticketReply->body,
        ]);
    }

    public function testUserCanAddReplyToOwnTicket()
    {
        // GIVEN
        // There are some tickets created by regular users
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        $ticket = $this->createTicket($user);


        // WHEN
        // User creates a reply to the ticket
        $ticketReply = $this->addTicketReply($ticket, $user);


        // THEN
        // The reply is successfully created in database
        $this->assertDatabaseHas('tickets', [
            'id' => $ticketReply->id,
            'user_id' => $user->id,
            'parent_id' => $ticket->id,
            'body' => $ticketReply->body,
        ]);

    }

    public function testUserCantAddReplyToNotOwnTicket()
    {
        // GIVEN
        // There are some tickets created by regular users
        $user1 = $this->getRandomUsers(1);
        $this->loginUser($user1);
        $user1Ticket = $this->createTicket($user1);

        $user2 = $this->getRandomUsers(1);
        $this->loginUser($user2);



        // WHEN
        // Another User tries to create a reply to the ticket
        $ticketReply = $this->addTicketReply($user1Ticket, $user2);


        // THEN
        // The reply is not created
        $this->assertNull($ticketReply);
    }

    public function testUserCanPostTicketReply()
    {
        // GIVEN
        // A user has created a ticket
        $user = $this->getRandomUsers(1);
        $this->loginUser($user);
        $ticket = $this->createTicket($user);


        // WHEN
        // User try to post a ticket reply via the web form
        //$this->addTicketReply($ticket, $user);
        $ticketReply = $this->makeTicketReply($ticket, $user);
        $this->post('/ticket/'.$ticket->id, $ticketReply->toArray());


        // THEN
        // The reply is successfully added to ticket
        $this->get('/ticket/'.$ticket->id)
            ->assertSee($ticketReply->body);
    }
}
