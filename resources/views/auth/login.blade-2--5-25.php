<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>AstroGuide - Login</title>

    <!-- Fonts and styles -->
    <link href="{{ url('backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700" rel="stylesheet">
    <link href="{{ url('backend/css/sb-admin-2.min.css') }}" rel="stylesheet">



</head>

<body>

    <div class="container-fluid login-container">
        <div class="row w-100">
            <!-- Image Section -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-0" style="background-color: #f1e29f;">
                <img src="{{ url('backend/img/astrologer_rakesh_gibli.png') }}" alt="Astrologer" class="img-fluid w-100 h-auto" style="object-fit: contain; max-height: 100vh;">
            </div>

            <!-- Login Form Section -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="w-75">
                    <div class="text-center mb-4">
                        <img src="{{ url('backend/img/logo.png') }}" alt="Astrology Logo" class="img-fluid" height="90">
                    </div>
                    <div class="text-center mb-4">
                        <h1 class="h4 text-gray-900">Welcome to AstroGuide Admin!</h1>
                    </div>

                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                    @if ($errors->has('error'))
                    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
                    @endif
                    @endif

                    <form class="user" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="email" name="email" class="form-control form-control-user"
                                placeholder="Enter Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control form-control-user"
                                placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                    </form>

                    <hr>
                    <div class="text-center">
                        <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ url('backend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('backend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('backend/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ url('backend/js/sb-admin-2.min.js') }}"></script>

</body>

</html>