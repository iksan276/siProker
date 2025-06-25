<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ 'SIPROKER - ITP' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('asset/itp.png') }}">
    

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
        
        .siproker-text {
        font-size: 24px;
        font-weight: bold;
        background-size: 200% 200%;
        color: transparent;
        -webkit-background-clip: text;
        background-clip: text;
        animation: gradient-animation 3s ease infinite, pulse 2s infinite;
        text-shadow: 0 0 5px rgba(78, 115, 223, 0.1);
        letter-spacing: 1px;
        background: var(--primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
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
        .particle {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--secondary-light) 100%);
    opacity: 0.4;
    animation: float 15s infinite ease-in-out;
    box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
}

.particle-1 {
    width: 80px;
    height: 80px;
    top: 10%;
    left: 5%;
    animation-delay: 0s;
    background: linear-gradient(135deg, var(--primary-lighter), var(--accent-light));
}

.particle-2 {
    width: 60px;
    height: 60px;
    top: 20%;
    right: 10%;
    animation-delay: 2s;
    background: linear-gradient(135deg, var(--secondary-light), var(--primary-lighter));
}

.particle-3 {
    width: 120px;
    height: 120px;
    bottom: 15%;
    left: 15%;
    animation-delay: 4s;
    background: linear-gradient(135deg, var(--accent-light), var(--primary-light));
}

.particle-4 {
    width: 50px;
    height: 50px;
    bottom: 10%;
    right: 20%;
    animation-delay: 6s;
    background: linear-gradient(135deg, var(--primary-lighter), var(--secondary-light));
}

.particle-5 {
    width: 70px;
    height: 70px;
    top: 50%;
    left: 30%;
    animation-delay: 8s;
    background: linear-gradient(135deg, var(--secondary-light), var(--accent-light));
}

.particle-6 {
    width: 40px;
    height: 40px;
    top: 30%;
    left: 70%;
    animation-delay: 1s;
    background: linear-gradient(135deg, var(--accent-light), var(--primary-lighter));
}

.particle-7 {
    width: 90px;
    height: 90px;
    bottom: 40%;
    right: 5%;
    animation-delay: 3s;
    background: linear-gradient(135deg, var(--primary-light), var(--secondary-light));
}

.particle-8 {
    width: 35px;
    height: 35px;
    top: 70%;
    left: 10%;
    animation-delay: 5s;
    background: linear-gradient(135deg, var(--secondary-light), var(--primary-lighter));
}

.particle-9 {
    width: 65px;
    height: 65px;
    top: 15%;
    left: 50%;
    animation-delay: 7s;
    background: linear-gradient(135deg, var(--accent-light), var(--secondary-light));
}

.particle-10 {
    width: 55px;
    height: 55px;
    bottom: 25%;
    left: 60%;
    animation-delay: 9s;
    background: linear-gradient(135deg, var(--primary-lighter), var(--accent-light));
}

/* 3D Geometric Shapes */
.geometric-shape {
    position: absolute;
    opacity: 0.1;
    animation: rotate3d 20s infinite linear;
    z-index: 0;
}

.shape-1 {
    width: 100px;
    height: 100px;
    top: 20%;
    left: 20%;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    transform: rotate(45deg);
    border-radius: 20px;
    animation-delay: 0s;
}

.shape-2 {
    width: 80px;
    height: 80px;
    top: 60%;
    right: 25%;
    background: linear-gradient(45deg, var(--secondary), var(--accent));
    clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    animation-delay: 2s;
}

.shape-3 {
    width: 120px;
    height: 120px;
    bottom: 30%;
    left: 10%;
    background: linear-gradient(45deg, var(--accent), var(--primary));
    border-radius: 50%;
    animation-delay: 4s;
}

.shape-4 {
    width: 90px;
    height: 90px;
    top: 40%;
    right: 15%;
    background: linear-gradient(45deg, var(--primary-light), var(--secondary-light));
    clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
    animation-delay: 6s;
}

.shape-5 {
    width: 70px;
    height: 70px;
    top: 10%;
    right: 40%;
    background: linear-gradient(45deg, var(--secondary-light), var(--primary-light));
    transform: rotate(30deg);
    border-radius: 15px;
    animation-delay: 8s;
}

.shape-6 {
    width: 110px;
    height: 110px;
    bottom: 10%;
    right: 10%;
    background: linear-gradient(45deg, var(--accent-light), var(--secondary-light));
    clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    animation-delay: 10s;
}
/* 3D Container for Hero Image */
.hero-3d-container {
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 1;
    pointer-events: none;
}

.hero-3d-element {
    position: absolute;
    border-radius: var(--radius-xl);
    opacity: 0.6;
    animation: float3d 12s infinite ease-in-out;
}

.element-1 {
    width: 60px;
    height: 60px;
    top: 10%;
    right: 10%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(124, 58, 237, 0.3));
    transform: rotateX(45deg) rotateY(45deg);
    animation-delay: 0s;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

.element-2 {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: -10%;
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.3), rgba(245, 158, 11, 0.3));
    transform: rotateX(-30deg) rotateY(60deg);
    animation-delay: 2s;
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.2);
}

.element-3 {
    width: 50px;
    height: 50px;
    top: 50%;
    right: -5%;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.3), rgba(37, 99, 235, 0.3));
    transform: rotateX(60deg) rotateY(-45deg);
    animation-delay: 4s;
    box-shadow: 0 10px 30px rgba(245, 158, 11, 0.2);
}

.element-4 {
    width: 70px;
    height: 70px;
    bottom: 40%;
    left: 5%;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(245, 158, 11, 0.3));
    transform: rotateX(-45deg) rotateY(30deg);
    animation-delay: 6s;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.2);
}

/* Enhanced Animations */
@keyframes float {
    0% {
        transform: translateY(0) rotate(0deg) scale(1);
    }
    33% {
        transform: translateY(-20px) rotate(5deg) scale(1.05);
    }
    66% {
        transform: translateY(-10px) rotate(-3deg) scale(0.95);
    }
    100% {
        transform: translateY(0) rotate(0deg) scale(1);
    }
}

@keyframes rotate3d {
    0% {
        transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg);
    }
    33% {
        transform: rotateX(120deg) rotateY(120deg) rotateZ(120deg);
    }
    66% {
        transform: rotateX(240deg) rotateY(240deg) rotateZ(240deg);
    }
    100% {
        transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg);
    }
}

@keyframes float3d {
    0% {
        transform: translateY(0) rotateX(45deg) rotateY(45deg);
    }
    50% {
        transform: translateY(-15px) rotateX(60deg) rotateY(60deg);
    }
    100% {
        transform: translateY(0) rotateX(45deg) rotateY(45deg);
    }
}

/* Enhanced Hero Image Container */
.hero-image-container {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    perspective: 1000px;
}

.hero-image {
    max-width: 100%;
    border-radius: var(--radius-2xl);
    box-shadow: 
        var(--shadow-xl),
        0 0 50px rgba(37, 99, 235, 0.2);
    position: relative;
    z-index: 2;
    transform: translateZ(20px);
    transition: transform 0.3s ease;
}

.hero-image:hover {
    transform: translateZ(30px) scale(1.02);
}

.hero-image-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: var(--radius-2xl);
    transform: rotate(-3deg) scale(0.95) translateZ(-10px);
    z-index: 1;
    opacity: 0.8;
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
    animation: float-bg 8s infinite ease-in-out;
}

@keyframes float-bg {
    0% {
        transform: rotate(-3deg) scale(0.95) translateZ(-10px);
    }
    50% {
        transform: rotate(-5deg) scale(0.97) translateZ(-15px);
    }
    100% {
        transform: rotate(-3deg) scale(0.95) translateZ(-10px);
    }
}

/
/* Enhanced Hero Particles Background */
.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(245, 158, 11, 0.05) 0%, transparent 50%);
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
           <div class="particle particle-1"></div>
        <div class="particle particle-2"></div>
        <div class="particle particle-3"></div>
        <div class="particle particle-4"></div>
        <div class="particle particle-5"></div>
        <div class="particle particle-6"></div>
        <div class="particle particle-7"></div>
        <div class="particle particle-8"></div>
        <div class="particle particle-9"></div>
        <div class="particle particle-10"></div>
        
        <!-- 3D Geometric Shapes -->
        <div class="geometric-shape shape-1"></div>
        <div class="geometric-shape shape-2"></div>
        <div class="geometric-shape shape-3"></div>
        <div class="geometric-shape shape-4"></div>
        <div class="geometric-shape shape-5"></div>
        <div class="geometric-shape shape-6"></div>
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
                                        <h1 class="h4 text-gray-900 mb-4 siproker-text">SIPR 
                                  <img src="{{ asset('asset/itp.png') }}" alt="Logo" style="width: 30px; height: 30px;border:#6447eb solid 1px;border-radius:24px">
                             KER</h1>
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
   <!-- 3D Container Background -->
            <div class="hero-3d-container">
                <div class="hero-3d-element element-1"></div>
                <div class="hero-3d-element element-2"></div>
                <div class="hero-3d-element element-3"></div>
                <div class="hero-3d-element element-4"></div>
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
