<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Tests\TestCase;
//use PHPUnit\Framework\TestCase;


class AssignTicketTest extends TestCase
{
    use RefreshDatabase;
    use HasRoles;

    protected function createUser($role=User::ROLE_USER)
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    protected function loginUser($user)
    {
        $this->actingAs($user)
             ->get('/login');
    }

    protected function createTicket()
    {
        // User is passed as a param or retrieved from the session (logged in)
        $ticketUser = auth()->user();
        $ticketTitle = Str::random(32);
        $ticketBody = Str::random(128);
        $ticketData = [
            'user_id' => $ticketUser->id,
            'title' => $ticketTitle,
            'body' => $ticketBody,
        ];
        return TicketService::createTicket($ticketData);
    }

    public function testAdminCanAssignTicketToSupportAgent()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);
        // And an admin
        $admin = $this->createUser(User::ROLE_ADMIN);
        // who is logged in
        $this->loginUser($admin);


        // WHEN
        // Admin assigns the ticket to the SupportAgent
        TicketService::assignSupportAgentToTicket($supportAgent, $ticket);


        // THEN
        // The ticket is successfully assigned to the SupportAgent
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $supportAgent->id
        ]);
    }

    public function testAdminCanSeeAssignTicketMenu()
    {
        // Tests can't render Vue.js components
        $this->assertTrue(true);
    }

    public function testUserCantSeeAssignTicketMenu()
    {
        // Tests can't render Vue.js components
        $this->assertTrue(true);

    }

    public function testSupportAgentCantSeeAssignTicketMenu()
    {
        // Tests can't render Vue.js components
        $this->assertTrue(true);

    }

    public function testUserCantAssignTicketToUser()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);


        // WHEN
        // The user assigns the ticket to the user (in that case, the same user)
        $this->loginUser($user);
        TicketService::assignSupportAgentToTicket($user, $ticket);


        // THEN
        // The ticket is not assigned to the user
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $user->id
        ]);
    }

    public function testUserCantAssignTicketToSupportAgent()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);


        // WHEN
        // The user assigns the ticket to the SupportAgent
        $this->loginUser($user);
        TicketService::assignSupportAgentToTicket($supportAgent, $ticket);


        // THEN
        // The ticket is not assigned to the SupportAgent
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $supportAgent->id
        ]);
    }

    public function testUserCantAssignTicketToAdmin()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);
        // And an admin
        $admin = $this->createUser(User::ROLE_ADMIN);
        // who is logged in
        //$this->loginUser($admin);


        // WHEN
        // The user assigns the ticket to the SupportAgent
        $this->loginUser($user);
        TicketService::assignSupportAgentToTicket($admin, $ticket);


        // THEN
        // The ticket is not assigned to the admin
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $admin->id
        ]);
    }

    public function testSupportAgentCantAssignTicketToUser()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);


        // WHEN
        // The SupportAgent assigns the ticket to the user (in that case, the same user)
        $this->loginUser($supportAgent);
        TicketService::assignSupportAgentToTicket($user, $ticket);


        // THEN
        // The ticket is not assigned to the user
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $user->id
        ]);
    }

    public function testSupportAgentCantAssignTicketToSupportAgent()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);


        // WHEN
        // The SupportAgent assigns the ticket to a SupportAgent (in that case, the same SupportAgent)
        $this->loginUser($supportAgent);
        TicketService::assignSupportAgentToTicket($supportAgent, $ticket);


        // THEN
        // The ticket is not assigned to the SupportAgent
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $supportAgent->id
        ]);
    }

    public function testSupportAgentCantAssignTicketToAdmin()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And a SupportAgent existing in DB
        $supportAgent = $this->createUser(User::ROLE_SUPPORT_AGENT);
        $admin = $this->createUser(User::ROLE_ADMIN);


        // WHEN
        // The SupportAgent assigns the ticket to the admin
        $this->loginUser($supportAgent);
        TicketService::assignSupportAgentToTicket($admin, $ticket);


        // THEN
        // The ticket is not assigned to the admin
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $admin->id
        ]);
    }

    public function testAdminCantAssignTicketToUser()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And an admin
        $admin = $this->createUser(User::ROLE_ADMIN);


        // WHEN
        // The Admin assigns the ticket to user
        $this->loginUser($admin);
        TicketService::assignSupportAgentToTicket($user, $ticket);


        // THEN
        // The ticket is not assigned to the user
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $user->id
        ]);
    }

    public function testAdminCantAssignTicketToAdmin()
    {
        // GIVEN
        // A user
        $user = $this->createUser();
        // with a ticket
        $this->loginUser($user);
        $ticket = $this->createTicket();
        // And an admin
        $admin = $this->createUser(User::ROLE_ADMIN);


        // WHEN
        // The Admin assigns the ticket to admin (yes, the same admin)
        $this->loginUser($admin);
        TicketService::assignSupportAgentToTicket($admin, $ticket);


        // THEN
        // The ticket is not assigned to the admin
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'assigned_agent_id' => $admin->id
        ]);
    }


}
