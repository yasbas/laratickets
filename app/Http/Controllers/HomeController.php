<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use Illuminate\Http\Request;

class HomeController extends Controller
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
        $isAdmin = $user->hasRole(User::ROLE_ADMIN);
        // YADO: Make it get user's tickets only if role is USER
        $tickets = $isAdmin ? Ticket::all() : $user->tickets()->orderBy('updated_at')->get();

        return view('home', [
            'tickets' => $tickets,
        ]);
    }
}
