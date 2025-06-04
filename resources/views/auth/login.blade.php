<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sb-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <!-- Custom styles for this template-->
    <link href="{{ asset('sb-admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins" !important;
        }
        
        /* Background and Particle Styles from welcome.blade.php */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --primary-lighter: #93c5fd;
            --primary-lightest: #dbeafe;
            --secondary: #7c3aed;
            --secondary-dark: #6d28d9;
            --secondary-light: #8b5cf6;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-lightest) 0%, #f8fafc 100%) !important;
            position: relative;
            overflow: hidden;
        }
        
        .particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--secondary-light) 100%);
            opacity: 0.3;
            animation: float 15s infinite ease-in-out;
        }
        
        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }
        
        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .particle:nth-child(3) {
            width: 120px;
            height: 120px;
            bottom: 15%;
            left: 15%;
            animation-delay: 4s;
        }
        
        .particle:nth-child(4) {
            width: 50px;
            height: 50px;
            bottom: 10%;
            right: 20%;
            animation-delay: 6s;
        }
        
        .particle:nth-child(5) {
            width: 70px;
            height: 70px;
            top: 50%;
            left: 30%;
            animation-delay: 8s;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
            100% {
                transform: translateY(0) rotate(0deg);
            }
        }
        
        /* Original login styles */
        .bg-login-image {
            background-image: url('https://img.freepik.com/free-vector/business-team-discussing-ideas-startup_74855-4380.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .bg-login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
        }
        
        .btn-email {
            background-color: #ffffff;
            color: #4a5568;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        
        .btn-email:hover {
            background-color: #f7fafc;
            color: #2d3748;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        
        .btn-email i {
            margin-right: 10px;
            font-size: 1.2em;
            color: #4299e1;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .divider span {
            padding: 0 10px;
            color: #718096;
            font-size: 0.875rem;
        }
        
        .login-options {
            margin-bottom: 20px;
        }
        
        /* Card styling enhancements */
        .card {
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
            position: relative;
            z-index: 1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <!-- Particles Background -->
    <div class="particles-container">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    
                                    <!-- Session Status -->
                                    @if (session('status'))
                                        <div class="alert alert-success mb-4">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <!-- Login Options -->
                                    <div class="login-options">
                                        <a href="https://layanan.itp.ac.id/validasi/oauth-google/23" class="btn btn-email btn-user btn-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 48 48">
                                                        <path fill="#fbc02d" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12	s5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24s8.955,20,20,20	s20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                                        <path fill="#e53935" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039	l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                                                        <path fill="#4caf50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36	c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                                                        <path fill="#1565c0" d="M43.611,20.083L43.595,20L42,20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571	c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
                                                    </svg> Login with Email
                                        </a>
                                    </div>
                                    
                                    <div class="divider">
                                        <span>OR</span>
                                    </div>

                                    <form class="user" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}" required autofocus
                                                aria-describedby="emailHelp" placeholder="Enter Email Address...">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-4">
                                            <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror"
                                                id="password" name="password" required placeholder="Password">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                      
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <!-- <hr>
                                    @if (Route::has('password.request'))
                                        <div class="text-center">
                                            <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>
                                        </div>
                                    @endif
                                    @if (Route::has('register'))
                                        <div class="text-center">
                                            <a class="small" href="{{ route('register') }}">Create an Account!</a>
                                        </div>
                                    @endif -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sb-admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sb-admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sb-admin/js/sb-admin-2.min.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Check for SweetAlert messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('swal_error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('swal_error') }}",
                    confirmButtonColor: '#3085d6'
                });
            @endif
            
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#3085d6'
                });
            @endif
            
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6'
                });
            @endif
        });
        
        // Animate particles
        document.addEventListener('DOMContentLoaded', function() {
            // Add random movement to particles
            const particles = document.querySelectorAll('.particle');
            
            particles.forEach(particle => {
                // Add some randomness to the initial position
                const randomX = Math.random() * 20 - 10; // -10 to 10
                const randomY = Math.random() * 20 - 10; // -10 to 10
                
                // Apply the random position
                const currentTransform = window.getComputedStyle(particle).transform;
                particle.style.transform = `translate(${randomX}px, ${randomY}px)`;
                
                // Add random animation duration for more natural movement
                const randomDuration = 15 + Math.random() * 10; // 15-25s
                particle.style.animationDuration = `${randomDuration}s`;
            });
        });
    </script>
</body>
</html>
