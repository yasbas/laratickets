@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create a New Ticket</div>


                    <div class="card-body">
                        <form action="{{ route('tickets.store') }}" method="POST" >
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="text" name="title" id="title" class="form-control @if ($errors->has('title')) is-invalid @endif" placeholder="Title...">
                                @if ($errors->has('title'))<div class="invalid-feedback">{{$errors->first('title')}}</div>@endif
                            </div>
                            <div class="form-group">
                                <textarea name="body" id="body" class="form-control @if ($errors->has('body')) is-invalid @endif" placeholder="Write your ticket..." rows="5"></textarea>
                                @if ($errors->has('body'))<div class="invalid-feedback">{{$errors->first('body')}}</div>@endif
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Ticket</button>
                            <a href="{{ route('tickets.index') }}" class="float-right">Cancel</a>
                        </form>
                        <hr>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
