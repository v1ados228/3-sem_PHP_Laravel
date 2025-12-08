@extends('layout')
@section('content')

@if (session()->has('message'))
<div class="alert alert-success" role="alert">    
    {{session('message')}}
</div>
@endif

@if (session()->has('error'))
<div class="alert alert-danger" role="alert">    
    {{session('error')}}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger" role="alert">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

    <div class="card" style="width: 100%;">
        <div class="card-body">
            <h5 class="card-title text-center">{{$article->title}}</h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">{{$article->date_public}}</h6>
            <p class="card-text">{{$article->text}}</p>
            @can('update', $article)
            <div class="btn-toolbar mt-3" role="toolbar">
                <a href="/article/{{$article->id}}/edit" class="btn btn-primary me-3">Edit article</a>
                @can('delete', $article)
                <form action="/article/{{$article->id}}" method="post">
                    @METHOD("DELETE")
                    @CSRF
                    <button type="submit" class="btn btn-outline-danger">Delete article</button>
                </form>
                @endcan
            </div>
            @endcan
        </div>
    </div>

    <!-- Comments -->
    <div class="mt-4">
        <h4>Comments ({{$article->comments->count()}})</h4>
        
        <!-- Form for adding a comment -->
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Add Comment</h5>
                <form action="/article/{{$article->id}}/comment" method="post">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" name="text" rows="3" placeholder="Enter your comment..." required minlength="3" maxlength="500"></textarea>
                    </div>
                    <button style="background-color: #0d6efd;" type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <!-- Comments list -->
        @if($article->comments->count() > 0)
            @foreach($article->comments as $comment)
                <div class="card mb-2">
                    <div class="card-body">
                        <p class="card-text">{{$comment->text}}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <strong>{{$comment->user->name ?? 'Unknown'}}</strong> • {{$comment->created_at->format('d.m.Y H:i')}}
                                </small>
                            </div>
                            @can('update', $comment)
                            <div>
                                <a href="/article/{{$article->id}}/comment/{{$comment->id}}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                @can('delete', $comment)
                                <form action="/article/{{$article->id}}/comment/{{$comment->id}}" method="post" class="d-inline">
                                    @METHOD("DELETE")
                                    @CSRF
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                                @endcan
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">No comments yet.</div>
        @endif
    </div>
@endsection