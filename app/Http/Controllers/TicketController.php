<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use App\Models\Ticket;
use Illuminate\Contracts\Support\Renderable as RenderableAlias;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use function Psy\debug;

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
}
