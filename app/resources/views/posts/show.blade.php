@extends('layout')

@section('content')
    <h1>
        {{ $post->title }}

        @if ((new Carbon\Carbon())->diffInMinutes($post->created_at) < 20)
        <x-badge>
            New Post!!!!!
        </x-badge>
    </h1>
@endif

    <p> {{ $post->content }}</p>

    <p>Added {{ $post->created_at->diffForHumans() }}</p>

    <p>Currently read by {{ $counter }} people</p>
    <h4>Comments</h4>

    @forelse ($post->comments as $comment)
        <p>
            {{ $comment->content }},
        </p>
        <p class="text-muted">
            added {{ $comment->created_at->diffForHumans() }}
        </p>
    @empty
        <p>No comments yet!</p>
    @endforelse
@endsection

