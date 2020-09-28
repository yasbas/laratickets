<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id', 'parent_id', 'title', 'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function createTicket($ticketData)
    {
        // Only users can create tickets (admins can't)
        if (auth()->user()->hasRole(User::ROLE_USER)) {
            self::create($ticketData);
        }
    }
}
