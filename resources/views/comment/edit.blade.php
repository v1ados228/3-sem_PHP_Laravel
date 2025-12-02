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

<ul class="list-group mb-3">
    @foreach($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
    @endforeach
</ul>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Edit Comment</h5>
        <form action="/article/{{$article->id}}/comment/{{$comment->id}}" method="POST">
            @CSRF
            @METHOD('PUT')
            <div class="mb-3">
                <label for="text" class="form-label">Comment Text</label>
                <textarea name="text" id="text" class="form-control" rows="5" required minlength="3" maxlength="500">{{$comment->text}}</textarea>
            </div>
                    <button style="background-color: #0d6efd;" type="submit" class="btn btn-primary">Update</button>
            <a href="/article/{{$article->id}}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

@endsection

