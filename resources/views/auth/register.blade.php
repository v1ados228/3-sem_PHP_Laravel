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
        <h3 class="card-title text-center mb-4">Registration</h3>
        
        <ul class="list-group mb-3">
            @foreach($errors->all() as $error)
                <li class="list-group-item list-group-item-danger">{{ $error }}</li>
            @endforeach
        </ul>

        <form action="{{ route('auth.register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">We will never share your email with third parties.</div>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" required minlength="6">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Password confirmation</label>
                <input type="password" class="form-control" 
                       id="password_confirmation" name="password_confirmation" required minlength="6">
            </div>
            
            <button style="background-color: #0d6efd;" type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        
        <div class="text-center mt-3">
            <p>Do you already have an account? <a href="{{ route('auth.login.show') }}">Login</a></p>
        </div>
    </div>
</div>

@endsection

