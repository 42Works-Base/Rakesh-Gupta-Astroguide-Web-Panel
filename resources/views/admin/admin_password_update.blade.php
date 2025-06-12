@extends('layouts.app')
@section('content')

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

<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="h3 mb-2 text-gray-800">Edit</h1>
    </div>

    <!-- Update Admin Password -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Update Admin Password</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.password.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="current_password">Current Password:</label>
                        <div class="form-group password-wrapper">
                            <input type="password" name="current_password" id="current_password" class="form-control">
                            <i class="fas fa-eye-slash toggle-password" data-target="#current_password"></i>
                        </div>
                        @error('current_password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="new_password">New Password:</label>
                        <div class="form-group password-wrapper">
                            <input type="password" name="new_password" id="new_password" class="form-control">
                            <i class="fas fa-eye-slash toggle-password" data-target="#new_password"></i>
                        </div>
                        @error('new_password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="new_password_confirmation">Confirm New Password:</label>
                        <div class="form-group password-wrapper">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control">
                            <i class="fas fa-eye-slash toggle-password" data-target="#new_password_confirmation"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->


<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-password").forEach(function(icon) {
            icon.addEventListener("click", function() {
                const targetSelector = this.getAttribute("data-target");
                const input = document.querySelector(targetSelector);
                const isPassword = input.getAttribute("type") === "password";

                input.setAttribute("type", isPassword ? "text" : "password");
                this.classList.toggle("fa-eye", isPassword);
                this.classList.toggle("fa-eye-slash", !isPassword);
            });
        });
    });
</script>


@endsection