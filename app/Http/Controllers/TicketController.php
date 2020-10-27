<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TicketService;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'supportAgents' => User::role(User::ROLE_SUPPORT_AGENT)->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'min:2', 'max:255'],
            'body' => ['required', 'min:2'],
        ]);

        TicketService::createTicket(
            array_merge(
                $request->toArray(),
                [
                    'user_id' => auth()->user()->id,
                ]
            )

        );

        return redirect()->route('tickets.index');
    }

    public function storeReply(Ticket $ticket)
    {
        request()->validate([
            'body' => ['required', 'min:2'],
        ]);

        if ($ticket) {
            TicketService::addTicketReply(
                $ticket,
                request()->body
            );
        } else {
            // YADO: Log and throw an exception
            Log::error('Ticket not found, id: '.$ticket->id);
        }


        return redirect()->route('tickets.show', [
            'ticket' => $ticket->id
        ]);
    }

    // YADO: After MVP, move this to API
    public function getSupportAgents()
    {
        return User::role(User::ROLE_SUPPORT_AGENT)
                   ->orderBy('name')
                   ->select(['id', 'name'])
                   ->get()
                   ->toJson();
    }

    // YADO: After MVP, move this to API
    public function assignSupportAgents(Ticket $ticket, User $supportAgent)
    {
        TicketService::assignSupportAgentToTicket($supportAgent, $ticket);

        return 'OK';
    }
}
