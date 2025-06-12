@extends('auth.auth')

@section('form')
<div class="text-center mb-4">
    <h1 class="h4 text-gray-900 mb-4">Change Password!</h1>
</div>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if ($errors->any())
@if ($errors->has('error'))
<div class="alert alert-danger">
    {{ $errors->first('error') }}
</div>
@endif
@endif

<form class="user" action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <input type="password" class="form-control form-control-user" name="password"
                placeholder="Password" required>
        </div>
        <div class="col-sm-6">
            <input type="password" class="form-control form-control-user" name="password_confirmation"
                placeholder="Confirm Password" required>
        </div>
    </div>

    <input type="submit" name="resetPassword" value="Reset Password" class="btn btn-primary btn-user btn-block">
</form>

<div class="text-center mt-3">
    <a class="small custom-link" href="{{ route('login') }}">Back to Login!</a>
</div>
@endsection