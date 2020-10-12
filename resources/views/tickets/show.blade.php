@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h2>{{ __('Ticket: ') }}{{ $ticket->title }}</h2></div>


                    <div class="card-body">
                        [Ticket Action Buttons Here (Reply | Note | Customer Note)]
                        <hr>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @foreach($ticketReplies as $reply)
                            @include('tickets.ticket-reply', ['reply' => $reply])
                        @endforeach

                        @include('tickets.ticket-reply', ['reply' => $ticket])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
