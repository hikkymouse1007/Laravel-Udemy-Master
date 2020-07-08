@extends('layout')

@section('content')
<form method="POST" action="{{ route('posts.update', ['post' => $post->id]) }}">
    @csrf
    @method('PUT')

    @include('posts._form')

    <button type="submit" class="btn btn-primary">Update!</button>
</form>
@endsection

