@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h2>{{ __('Ticket: ') }}{{ $ticket->title }}</h2></div>


                    <div class="card-body">
                        @if(auth()->user()->hasRole(\App\Models\User::ROLE_ADMIN) || auth()->user()->id == $ticket->user->id || (auth()->user()->hasRole(\App\Models\User::ROLE_SUPPORT_AGENT) && $ticket->assignedSupportAgent && auth()->user()->id == $ticket->assignedSupportAgent->id))
                            <form action="/tickets/{{ $ticket->id }}" method="POST" >
                                [Ticket Action Buttons Here (Reply | Note | Customer Note)]
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <textarea name="body" id="body" class="form-control" placeholder="Write your reply..." rows="5"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Reply</button>
                            </form>
                            <hr>

                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                        @endif

                        @foreach($ticketReplies as $reply)
                            @include('tickets.ticket-reply', ['reply' => $reply])
                        @endforeach

                        @include('tickets.ticket-reply', ['reply' => $ticket])
                    </div>
                </div>
            </div>
            @hasanyrole('support_agent|admin')
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Actions</div>
                    <div class="card-body">
                        @role('admin')
                        <assign-support-agent-dropdown
                            :ticket-id="{{ $ticket->id }}"
                            :assigned-support-agent-id="{{ $ticket->assignedSupportAgent->id ?? -1 }}"
                        ></assign-support-agent-dropdown>
                        @endrole

                    </div>
                </div>
            </div>
            @endhasanyrole
        </div>
    </div>
@endsection
