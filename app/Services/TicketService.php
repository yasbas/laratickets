<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

class TicketService {

    public static function getTickets(User $user)
    {
        $canSeeAllTickets = $user->hasRole(User::ROLE_ADMIN) || $user->hasRole(User::ROLE_SUPPORT_AGENT);

        // YADO: Make it get user's tickets only if role is USER
        return $canSeeAllTickets ? Ticket::all() : $user->tickets()->orderBy('updated_at')->get();
    }

    public static function createTicket(array $ticketData)
    {
        $ticket = null;
        if (auth()->user()->hasRole(User::ROLE_USER)) {
            $ticket = Ticket::create($ticketData);
        }

        return $ticket;
    }

    public static function addTicketReply(Ticket $ticket, string $reply)
    {
        if (
            // All users can create ticket replies to own tickets
            $ticket->user->id == auth()->user()->id ||
            // Admin users can create replies to all tickets
            auth()->user()->hasRole(User::ROLE_ADMIN) ||
            // Assigned to ticket SupportAgents can create replies to the ticket
            (isset($ticket->assignedSupportAgent) && auth()->user()->id == $ticket->assignedSupportAgent->id)

        ) {

            return TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'body' => $reply,
            ]);
        } else {
            return null;
        }
    }

    public static function makeTicketReply(Ticket $ticket, string $reply)
    {
        // All users can make ticket replies to own tickets
        // Admin users can make replies to all tickets
        if (($ticket->user->id == auth()->user()->id ||
             auth()->user()->hasRole(User::ROLE_ADMIN))
        ) {

            return TicketReply::make([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->user()->id,
                'body' => $reply,
            ]);
        } else {
            return null;
        }
    }

    public static function assignSupportAgentToTicket(User $supportAgent, Ticket $ticket)
    {
        // Only admin can assign tickets, currently
        if (auth()->user()->hasRole(User::ROLE_ADMIN) &&
            $supportAgent->hasRole(User::ROLE_SUPPORT_AGENT)
        ) {
            $ticket->assigned_agent_id = $supportAgent->id;
            $ticket->save();
        }
    }

}
