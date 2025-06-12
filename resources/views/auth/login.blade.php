@extends('auth.auth')

<style>
    .custom-control-input:checked~.custom-control-label::before {
        background-color: #f7c626 !important;
        border-color: #f7c626 !important;
    }

    .custom-control-input:focus:checked~.custom-control-label::before {
        box-shadow: 0 0 0 0.2rem rgba(247, 198, 38, 0.25) !important;
    }

    .custom-control-label {
        font-size: 14px;
        color: #333;
    }

    .password-wrapper {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
    }
</style>

@section('form')
<div class="text-center mb-4">
    <h1 class="h4 text-gray-900">Welcome to AstroGuide Admin!</h1>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any() && $errors->has('error'))
<div class="alert alert-danger">{{ $errors->first('error') }}</div>
@endif

<form class="user" action="{{ route('login') }}" method="POST" autocomplete="on">
    @csrf
    <div class="form-group">
        <input type="email" name="email" class="form-control form-control-user" placeholder="Enter Email Address" required autocomplete="email" value="{{ old('email', Cookie::get('remembered_email')) }}">
    </div>

    <div class="form-group password-wrapper">
        <input type="password" name="password" class="form-control form-control-user" id="password" placeholder="Password" required autocomplete="current-password" value="{{ Cookie::get('remembered_password') }}">
        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox small">
            <input type="checkbox" name="remember" class="custom-control-input" id="rememberMe" {{ Cookie::get('remembered_email') ? 'checked' : '' }}>
            <label class="custom-control-label" for="rememberMe">Remember Me</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
</form>

<hr>
<div class="text-center">
    <a class="small custom-link" href="{{ route('password.request') }}">Forgot Password?</a>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const togglePassword = document.querySelector("#togglePassword");
        const passwordInput = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
</script>
@endpush
@endsection