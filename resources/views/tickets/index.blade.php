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

{{--                    {{ __('You are logged in!') }}--}}
{{--                    @role('admin')--}}
{{--                        {{ __('As admin') }}--}}
{{--                    @endrole--}}
{{--                    @role('user')--}}
{{--                        {{ __('As user') }}--}}
{{--                    @endrole--}}

                    @foreach($tickets as $ticket)
                        <p>
                            <a href="/ticket/{{ $ticket->id }}">
                                {{ $ticket->title }}
                            </a>
                        </p>
                    @endforeach


                </div>
            </div>
        </div>
    </div>
</div>
@endsection