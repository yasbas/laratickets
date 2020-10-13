<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'parent_id', 'title', 'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Ticket::class, 'parent_id', 'id');
    }

    public static function createTicket($ticketData)
    {
        // Only users can create tickets (admins can't)
        if (auth()->user()->hasRole(User::ROLE_USER)) {
            return self::create($ticketData);
        }
    }

    public static function addTicketReply(Ticket $ticket, array $ticketData)
    {
        // All users can create ticket replies
        // But only to own tickets
        if (($ticket->user->id == auth()->user()->id ||
             auth()->user()->hasRole(User::ROLE_ADMIN))
        ) {

            return self::create($ticketData);
        } else {
            return null;
        }
    }

    public static function makeTicketReply(Ticket $ticket, array $ticketData)
    {
        // All users can create ticket replies
        // But only to own tickets
        if (($ticket->user->id == auth()->user()->id ||
             auth()->user()->hasRole(User::ROLE_ADMIN))
        )
        {

            return self::make($ticketData);
        } else {
            return null;
        }
    }
}
