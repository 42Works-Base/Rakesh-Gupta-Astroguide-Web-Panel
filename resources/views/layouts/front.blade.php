<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AstroGuide | Home page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/custom-style.css')}}">
</head>

<body>
    <header id="header">
        <div class="container">
            <div class="row align-items-center"> <!-- Added vertical alignment -->
                <div class="col-md-2 col-xxl-2 text-center">
                    <a href="#"><img src="{{asset('assets/images/Logo-2.png')}}" class="img-fluid" alt="Logo" width="138"></a>
                </div>
                <div class="col-md-7 col-xxl-7 d-flex justify-content-center">
                    <!-- Navigation Menu -->
                    <nav class="navbar navbar-expand-lg">

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Features</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Pricing</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Testimonials</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
                <div class="col-md-3 col-xxl-3 d-flex justify-content-center">
                    <div class="cartandgetstarted">
                        <div class="cart-icon"><img src="{{asset('assets/images/cart.svg')}}" class="img-fluid" alt="cart"><span class="cart-number">1</span></div>
                        <a href="#" class="getstartedbtn">Get Started <img src="{{asset('assets/images/next.svg')}}" class="img-fluid" alt="next"></a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End of Topbar -->
    @yield('content') <!-- Dynamic Content Goes Here -->


    <!-- Footer Section -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <!-- Left Content -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <h2 class="fw-bold">Stay Updated!</h2>
                            <p>Subscribe to our newsletter for the latest features and updates.</p>
                        </div>
                        <div class="col-lg-6 col-sm-12">
                            <div class="email-box mt-4">
                                <i class="fas fa-envelope"></i>
                                <input type="email" placeholder="Enter Your Email">
                                <a href="#"><img src="{{asset('assets/images/Out Link.svg')}}" width="30"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <div class="mt-4">
                                <h5>Get in touch</h5>
                                <p><i class="fas fa-envelope"></i> contact@vividly.com</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <div class="mt-3">
                                <div class="google-review mb-4"><img src="{{asset('assets/images/google.svg')}}">
                                    <img src="{{asset('assets/images/Stars.svg')}}" alt="img-fluid" />
                                </div>
                                <p class="mt-2"><strong>800+ 5 Stars Ratings</strong></p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <h5>Quick Links</h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none text-dark">Home</a></li>
                                <li><a href="#" class="text-decoration-none text-dark">Features</a></li>
                                <li><a href="#" class="text-decoration-none text-dark">Pricing</a></li>
                            </ul>
                        </div>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <h5>&nbsp;</h5>
                            <ul class="list-unstyled">
                                <li><a href="#" class="text-decoration-none text-dark">Testimonials</a></li>
                                <li><a href="#" class="text-decoration-none text-dark">About Us</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-5 row">
                <div class="col-sm-12 col-md-4 col-lg-4">
                    <p class="mb-0">&copy; AstroGuide 2025. All rights reserved.</p>
                </div>
                <div class="col-sm-12 col-md-4 col-lg-4 text-center"><img src="{{asset('assets/images/Logo-2.png')}}" class="img-fluid" alt="Logo" width="138"></div>
                <div class="col-sm-12 col-md-4 col-lg-4 text-right">
                    <a href="#" class="text-decoration-none text-dark me-3">Terms & Conditions</a>
                    <a href="#" class="text-decoration-none text-dark me-3">Privacy Policy</a>
                    <a href="#" class="text-decoration-none text-dark">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>