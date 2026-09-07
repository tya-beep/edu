@extends('layouts.gurunewapp')

@section('content')

<div class="container">

    <h2>Welcome {{ Auth::guard('guru_new')->user()->guruName }}</h2>

    <div class="card shadow mt-4">

        <div class="card-body">

            <h4>Teacher Inbox</h4>

            <p>You have received documents from HR.</p>

            <a href="{{ route('gurunew.inbox') }}"
               class="btn btn-primary">

                Open Inbox

            </a>

        </div>

    </div>

</div>

@endsection