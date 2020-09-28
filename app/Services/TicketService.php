<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;

class TicketService {

    public static function getTickets(User $user)
    {
        $isAdmin = $user->hasRole(User::ROLE_ADMIN);

        // YADO: Make it get user's tickets only if role is USER
        return $isAdmin ? Ticket::all() : $user->tickets()->orderBy('updated_at')->get();
    }

}
