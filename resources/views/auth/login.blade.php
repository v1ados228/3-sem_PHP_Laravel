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

<div class="card mx-auto" style="max-width: 500px; margin-top: 50px;">
    <div class="card-body">
        <h3 class="card-title text-center mb-4">Authorization</h3>
        
        <ul class="list-group mb-3">
            @foreach($errors->all() as $error)
                <li class="list-group-item list-group-item-danger">{{ $error }}</li>
            @endforeach
        </ul>

        <form action="{{ route('auth.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            
            <button style="background-color: #0d6efd;" type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        
        <div class="text-center mt-3">
            <p>No account? <a href="{{ route('auth.register.show') }}">Register</a></p>
        </div>
    </div>
</div>

@endsection

