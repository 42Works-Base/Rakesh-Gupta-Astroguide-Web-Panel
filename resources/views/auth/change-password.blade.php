<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AstroGuide - Change Password</title>

    <!-- Custom fonts for this template-->
    <link href="{{ url('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ url('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <style>
        .bg-register-image {
            background-image: url('backend/img/premium_photo-1706559780094-648dbe2b2bd0.jpeg');
        }

        .login-error {
            color: red;
        }
    </style>
</head>

<body class="">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                            <div class="col-lg-7">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Change Password!</h1>
                                    </div>
                                    @if ($errors->any())

                                    <p class="login-error">{{ $errors->first() }}</p>

                                    @endif
                                    <form class="user" action="{{ route('password.change') }}" method="POST">
                                        @csrf

                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="current_password" id=""
                                                placeholder="Current Password">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="password" class="form-control form-control-user" name="new_password"
                                                    id="" placeholder="New Password">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control form-control-user" name="new_password_confirmation"
                                                    id="" placeholder="Repeat Password">
                                            </div>
                                        </div>

                                        <input type="submit" name="changePassword" value="Change Password" class="btn btn-primary btn-user btn-block">
                                        <hr>
                                    </form>

                                    <!-- <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div> -->
                                    <div class="text-center">
                                        <a class="small" href="{{route('dashboard')}}">Back to Dashboard!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ url('backend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('backend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ url('backend/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ url('backend/js/sb-admin-2.min.js')}}"></script>

</body>

</html>