@extends('layout')

@section('content')
<div class="row">
    <div class="col-8">
    @forelse ($posts as $post)
        <p>
            <h3>
                @if($post->trashed())
                    <del>
                @endif
                <a class="{{ $post->trashed() ? 'text-muted' : '' }}"
                    href="{{ route('posts.show', ['post' => $post->id]) }}">{{ $post->title }}</a>
                @if($post->trashed())
                    </del>
                @endif
            </h3>
            <p class="muted">
                Added {{ $post->created_at->diffForHumans() }}
                by {{ $post->user->name}}
            </p>

            @if($post->comments_count)
                <p>{{ $post->comments_count }} comments</p>
            @else
                <p>No comments yet!</p>
            @endif

            @auth
                @can('update', $post)
                <a href="{{ route('posts.edit', ['post' => $post->id] )}}"
                    class="btn btn-primary">
                    Edit
                </a>
                @endcan
            @endauth

            {{-- @cannot('delete', $post)
                <p>You can't delete this post.</p>
            @endcannot --}}

            @auth
                @if (!$post->trashed())
                    @can('delete', $post)
                    <form method="POST" class="fm-inline"  action="{{ route('posts.destroy', ['post' => $post->id] )}}">
                        @csrf
                        @method('DELETE')

                        <input type="submit" value="Delete!" class="btn btn-primary" >
                    </form>
                    @endcan
                @endif
            @endauth
        </p>
    @empty
        <p>No blog posts yet!</p>
    @endforelse ($posts as $post)
    </div>
    <div class="col-4">
        <div class="container">
            <div class="row">
                <div class="card" style="width: 100%;">
                    <div class="card-body">
                        <h5 class="card-title">Most Commented</h5>
                        <p class="card-subtitle mb-2 text-muted">What people are currently talking about</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($mostCommented as $post)
                            <li class="list-group-item">
                                <a href="{{ route('posts.show', ['post' => $post->id]) }}">
                                        {{ $post->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="card" style="width: 100%;">
                    <div class="card-body">
                        <h5 class="card-title">Most Active</h5>
                        <p class="card-subtitle mb-2 text-muted">User with most posts written</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($mostActive as $user)
                            <li class="list-group-item">
                                {{ $user->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="card" style="width: 100%;">
                    <div class="card-body">
                        <h5 class="card-title">Most Active Last Month</h5>
                        <p class="card-subtitle mb-2 text-muted">User with most posts written</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach ($mostActiveLastMonth as $user)
                            <li class="list-group-item">
                                {{ $user->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
