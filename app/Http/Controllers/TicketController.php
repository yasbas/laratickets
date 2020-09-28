<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use App\Ticket;
use App\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $tickets = TicketService::getTickets($user);

        return view('home', [
            'tickets' => $tickets,
        ]);
    }
}
