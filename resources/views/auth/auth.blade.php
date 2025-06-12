<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>AstroGuide - {{ $title ?? 'Auth' }}</title>

    <!-- Fonts and styles -->
    <link href="{{ url('backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700" rel="stylesheet">
    <link href="{{ url('backend/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f1e29f;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: #f7c626;
            border-color: #f7c626;
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e6b41d;
            border-color: #e6b41d;
        }

        .login-error {
            color: red;
        }

        .custom-link {
            color: #3a3b45 !important;
            text-decoration: none;
        }

        .custom-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container-fluid login-container">
        <div class="row w-100">
            <!-- Image Section -->
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-0" style="background-color: #f1e29f;">
                <img src="{{ url('backend/img/astrologer_rakesh_gibli.png') }}" alt="Astrologer" class="img-fluid w-100 h-auto" style="object-fit: contain; max-height: 100vh;">
            </div>

            <!-- Form Section -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="w-75">
                    <div class="text-center mb-4">
                        <img src="{{ url('backend/img/logo.png') }}" alt="Astrology Logo" class="img-fluid" height="90">
                    </div>
                    @yield('form')
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ url('backend/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('backend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('backend/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ url('backend/js/sb-admin-2.min.js') }}"></script>

    {{-- Add this line --}}
    @stack('scripts')
</body>

</html>