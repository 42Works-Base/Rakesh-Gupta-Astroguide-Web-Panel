@extends('auth.auth')

@section('form')
<div class="text-center mb-4">
    <h1 class="h4 text-gray-900">Please enter your registered email!</h1>
    <!-- <p class="text-muted small">Enter your registered email to receive a reset link</p> -->
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any() && $errors->has('error'))
<div class="alert alert-danger">{{ $errors->first('error') }}</div>
@endif

<form class="user" action="{{ route('password.email') }}" method="POST">
    @csrf
    <div class="form-group">
        <input type="email" class="form-control form-control-user" name="email" placeholder="Enter your Email" required>
    </div>
    <button type="submit" class="btn btn-primary btn-user btn-block">Send Password Reset Link</button>
</form>

<hr>
<div class="text-center">
    <a class="small custom-link" href="{{ route('login') }}">Back to Login</a>
</div>
@endsection