@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @role('user')
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create a New Ticket</a>
                    @endrole

                    @foreach($tickets as $ticket)
                        <p>
                            <a href="/tickets/{{ $ticket->id }}">
                                {{ $ticket->title }}
                            </a>
                            <br>
                            @if ($ticket->assignedSupportAgent)
                            <span>Assigned to {{  $ticket->assignedSupportAgent->name }}</span>
                            @endif
                        </p>
                    @endforeach


                </div>
            </div>
        </div>
    </div>
</div>
@endsection
