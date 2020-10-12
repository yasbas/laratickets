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
            self::create($ticketData);
        }
    }
}
