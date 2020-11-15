@extends('layout')

@section('content')
<div class="row">
    <div class="col-12">
    @foreach ($users as $user)
        <p>{{$user->name}}</p>
    @endforeach
    </div>

</div>
@endsection
