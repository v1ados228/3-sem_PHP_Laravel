@extends('layout')
@section('content')

@if (session()->has('message'))
<div class="alert alert-success" role="alert">    
    {{session('message')}}
</div>
@endif

    <div class="card" style="width: 100%;">
        <div class="card-body">
            <h5 class="card-title text-center">{{$article->title}}</h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">{{$article->date_public}}</h6>
            <p class="card-text">{{$article->text}}</p>
            <div class="btn-toolbar mt-3" role="toolbar">
                <a href="/article/{{$article->id}}/edit" class="btn btn-primary me-3">Edit article</a>
                <form action="/article/{{$article->id}}" method="post">
                @METHOD("DELETE")
                @CSRF
                <button type="submit" class="btn btn-warning">Delete article</button>
            </form>
            </div>
        </div>
    </div>
@endsection