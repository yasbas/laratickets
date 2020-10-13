<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use App\Models\Ticket;
use Illuminate\Contracts\Support\Renderable as RenderableAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

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
     * @return View
     */
    public function index(): View
    {
        return view('tickets.index', [
            'tickets' => TicketService::getTickets(auth()->user()),
        ]);
    }

    /**
     * @param Ticket $ticket
     *
     * @return View
     */
    public function show(Ticket $ticket): View
    {
        return view('tickets.show',[
            'ticket' => $ticket,
            'ticketReplies' => $ticket->replies()->orderBy('id', 'desc')->get(),
        ]);
    }

    public function store(Ticket $ticket)
    {
        // YADO: Need to have the Ticket data and the reply data.
        // YADO: And currently the form has only the reply data.
        // YADO: So, add the ticket data.
        // YAWARN: Also consider separating Ticket and TicketReply!
        //Ticket::addTicketReply($ticket);
    }
}
