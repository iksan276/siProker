<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SIPROKER - Sistem Informasi Program Kerja Dosen</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Animation Library -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Styles -->
        <style>
            /* Base Variables */
            :root {
                --primary: #2563eb;
                --primary-dark: #1d4ed8;
                --secondary: #4f46e5;
                --accent: #8b5cf6;
                --dark: #1e293b;
                --light: #f8fafc;
                --gray-100: #f3f4f6;
                --gray-200: #e5e7eb;
                --gray-300: #d1d5db;
                --gray-400: #9ca3af;
                --gray-500: #6b7280;
                --gray-600: #4b5563;
                --gray-700: #374151;
                --gray-800: #1f2937;
                --gray-900: #111827;
                --transition-fast: 0.3s;
                --transition-normal: 0.5s;
                --transition-slow: 0.7s;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                --font-sans: 'Poppins', sans-serif;
            }

            /* Reset & Base Styles */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                font-family: var(--font-sans);
                color: var(--gray-700);
                line-height: 1.6;
                background-color: var(--light);
                overflow-x: hidden;
            }

            a {
                text-decoration: none;
                color: inherit;
                transition: color var(--transition-fast);
            }

            img {
                max-width: 100%;
                height: auto;
            }

            /* Layout */
            .container {
                width: 100%;
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 1.5rem;
            }

            .section {
                padding: 5rem 0;
            }

            .section-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
                text-align: center;
                color: var(--gray-900);
            }

            .section-subtitle {
                font-size: 1.125rem;
                text-align: center;
                max-width: 800px;
                margin: 0 auto 3rem;
                color: var(--gray-600);
            }

            /* Grid & Flex Utilities */
            .grid {
                display: grid;
                gap: 2rem;
            }

            .grid-cols-1 {
                grid-template-columns: 1fr;
            }

            .grid-cols-2 {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-cols-3 {
                grid-template-columns: repeat(3, 1fr);
            }

            .grid-cols-4 {
                grid-template-columns: repeat(4, 1fr);
            }

            .flex {
                display: flex;
            }

            .flex-col {
                flex-direction: column;
            }

            .items-center {
                align-items: center;
            }

            .justify-center {
                justify-content: center;
            }

            .justify-between {
                justify-content: space-between;
            }

            .gap-4 {
                gap: 1rem;
            }

            .gap-8 {
                gap: 2rem;
            }

            /* Background & Hero Styles */
            .hero {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                background-image: url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
                background-size: cover;
                background-position: center;
                color: white;
                z-index: 1;
            }

            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.9) 0%, rgba(79, 70, 229, 0.8) 100%);
                z-index: -1;
            }

            .hero-content {
                max-width: 650px;
                z-index: 2;
            }

            .hero-title {
                font-size: 3.5rem;
                font-weight: 800;
                line-height: 1.2;
                margin-bottom: 1.5rem;
                background: linear-gradient(to right, #ffffff, #e2e8f0);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .hero-subtitle {
                font-size: 1.5rem;
                margin-bottom: 2rem;
                color: rgba(255, 255, 255, 0.9);
            }

            .hero-description {
                font-size: 1.125rem;
                margin-bottom: 2.5rem;
                color: rgba(255, 255, 255, 0.8);
            }

            .hero-image {
                position: relative;
                z-index: 2;
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
                100% { transform: translateY(0px); }
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all var(--transition-fast);
                cursor: pointer;
                box-shadow: var(--shadow);
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                color: white;
                border: none;
            }

            .btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
                background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary) 100%);
            }

            .btn-outline {
                background: transparent;
                color: white;
                border: 2px solid white;
            }

            .btn-outline:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            .btn-dark {
                background: var(--gray-900);
                color: white;
                border: none;
            }

            .btn-dark:hover {
                background: var(--gray-800);
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            /* Feature Cards */
            .feature-card {
                background: white;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: var(--shadow);
                transition: all var(--transition-fast);
                position: relative;
                overflow: hidden;
                z-index: 1;
                height: 100%;
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                opacity: 0;
                z-index: -1;
                transition: opacity var(--transition-fast);
            }

            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: var(--shadow-lg);
            }

            .feature-card:hover::before {
                opacity: 0.05;
            }

            .feature-icon {
                width: 70px;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 1rem;
                margin-bottom: 1.5rem;
                background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
                color: var(--primary);
                font-size: 1.75rem;
                transition: all var(--transition-fast);
            }

            .feature-card:hover .feature-icon {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                color: white;
                transform: scale(1.1);
            }

            .feature-title {
                font-size: 1.25rem;
                font-weight: 700;
                margin-bottom: 1rem;
                color: var(--gray-900);
            }

            .feature-description {
                color: var(--gray-600);
            }

            /* Testimonial Cards */
            .testimonial-card {
                background: white;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: var(--shadow);
                height: 100%;
            }

            .testimonial-header {
                display: flex;
                align-items: center;
                margin-bottom: 1.5rem;
            }

            .testimonial-avatar {
                width: 60px;
                height: 60px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                color: white;
                margin-right: 1rem;
            }

            .testimonial-name {
                font-weight: 700;
                color: var(--gray-900);
            }

            .testimonial-position {
                font-size: 0.875rem;
                color: var(--gray-500);
            }

            .testimonial-content {
                color: var(--gray-600);
                font-style: italic;
            }

            .testimonial-rating {
                margin-top: 1rem;
                color: #f59e0b;
            }

            /* Stats Section */
            .stats-section {
                background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
                padding: 5rem 0;
            }

            .stat-item {
                text-align: center;
            }

            .stat-value {
                font-size: 3rem;
                font-weight: 800;
                color: var(--primary);
                margin-bottom: 0.5rem;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .stat-label {
                color: var(--gray-600);
                font-size: 1.125rem;
            }

            /* FAQ Section */
            .faq-item {
                background: white;
                border-radius: 1rem;
                padding: 1.5rem;
                box-shadow: var(--shadow);
                margin-bottom: 1.5rem;
                transition: all var(--transition-fast);
            }

            .faq-item:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-md);
            }

            .faq-question {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--gray-900);
                margin-bottom: 1rem;
            }

            .faq-answer {
                color: var(--gray-600);
            }

            /* CTA Section */
            .cta-section {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                color: white;
                padding: 5rem 0;
                text-align: center;
            }

            .cta-title {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
            }

            .cta-description {
                font-size: 1.25rem;
                max-width: 800px;
                margin: 0 auto 2.5rem;
                opacity: 0.9;
            }

            /* Footer */
            .footer {
                background: var(--gray-900);
                color: white;
                padding: 5rem 0 2rem;
            }

            .footer-title {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 1.5rem;
            }

            .footer-description {
                color: var(--gray-400);
                margin-bottom: 2rem;
            }

            .footer-links-title {
                font-weight: 700;
                margin-bottom: 1.5rem;
            }

            .footer-links {
                list-style: none;
            }

            .footer-link {
                margin-bottom: 0.75rem;
            }

            .footer-link a {
                color: var(--gray-400);
                transition: color var(--transition-fast);
            }

            .footer-link a:hover {
                color: white;
            }

            .footer-contact {
                color: var(--gray-400);
            }

            .footer-contact-item {
                display: flex;
                align-items: center;
                margin-bottom: 1rem;
            }

            .footer-contact-icon {
                margin-right: 0.75rem;
            }

            .footer-social {
                display: flex;
                gap: 1rem;
            }

            .footer-social-link {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: var(--gray-800);
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all var(--transition-fast);
            }

            .footer-social-link:hover {
                background: var(--primary);
                transform: translateY(-3px);
            }

            .footer-bottom {
                border-top: 1px solid var(--gray-800);
                margin-top: 3rem;
                padding-top: 2rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .footer-copyright {
                color: var(--gray-400);
                margin-bottom: 1rem;
            }

            .footer-bottom-links {
                display: flex;
                gap: 2rem;
            }

            .footer-bottom-link {
                color: var(--gray-400);
                transition: color var(--transition-fast);
            }

            .footer-bottom-link:hover {
                color: white;
            }

            /* Animations */
            .animate-fade-up {
                opacity: 0;
                transform: translateY(20px);
                transition: opacity var(--transition-normal), transform var(--transition-normal);
            }

            .animate-fade-up.active {
                opacity: 1;
                transform: translateY(0);
            }

            .animate-fade-in {
                opacity: 0;
                transition: opacity var(--transition-normal);
            }

            .animate-fade-in.active {
                opacity: 1;
            }

            .animate-scale-in {
                opacity: 0;
                transform: scale(0.9);
                transition: opacity var(--transition-normal), transform var(--transition-normal);
            }

            .animate-scale-in.active {
                opacity: 1;
                transform: scale(1);
            }

            /* Responsive Styles */
            @media (max-width: 1200px) {
                .container {
                    max-width: 1140px;
                }
            }

            @media (max-width: 992px) {
                .container {
                    max-width: 960px;
                }
                .grid-cols-4 {
                    grid-template-columns: repeat(2, 1fr);
                }
                .hero-title {
                    font-size: 3rem;
                }
            }

            @media (max-width: 768px) {
                .container {
                    max-width: 720px;
                }
                .grid-cols-3, .grid-cols-4 {
                    grid-template-columns: repeat(2, 1fr);
                }
                .grid-cols-2 {
                    grid-template-columns: 1fr;
                }
                .hero-title {
                    font-size: 2.5rem;
                }
                .hero-subtitle {
                    font-size: 1.25rem;
                }
                .section-title {
                    font-size: 2rem;
                }
                .footer-grid {
                    grid-template-columns: repeat(2, 1fr);
                    gap: 2rem;
                }
            }

            @media (max-width: 576px) {
                .container {
                    max-width: 100%;
                    padding: 0 1rem;
                }
                .grid-cols-2, .grid-cols-3, .grid-cols-4 {
                    grid-template-columns: 1fr;
                }
                .hero-title {
                    font-size: 2rem;
                }
                .hero-subtitle {
                    font-size: 1.125rem;
                }
                .section-title {
                    font-size: 1.75rem;
                }
                .footer-grid {
                    grid-template-columns: 1fr;
                }
                .footer-bottom {
                    flex-direction: column;
                    text-align: center;
                }
                .footer-bottom-links {
                    flex-direction: column;
                    gap: 1rem;
                }
            }

            /* Utility Classes */
            .text-center { text-align: center; }
            .mb-1 { margin-bottom: 0.25rem; }
            .mb-2 { margin-bottom: 0.5rem; }
            .mb-3 { margin-bottom: 0.75rem; }
            .mb-4 { margin-bottom: 1rem; }
            .mb-5 { margin-bottom: 1.25rem; }
            .mb-6 { margin-bottom: 1.5rem; }
            .mb-8 { margin-bottom: 2rem; }
            .mb-10 { margin-bottom: 2.5rem; }
            .mb-12 { margin-bottom: 3rem; }
            .mt-1 { margin-top: 0.25rem; }
            .mt-2 { margin-top: 0.5rem; }
            .mt-3 { margin-top: 0.75rem; }
            .mt-4 { margin-top: 1rem; }
            .mt-5 { margin-top: 1.25rem; }
            .mt-6 { margin-top: 1.5rem; }
            .mt-8 { margin-top: 2rem; }
            .mt-10 { margin-top: 2.5rem; }
            .mt-12 { margin-top: 3rem; }
            .mx-auto { margin-left: auto; margin-right: auto; }
            .w-full { width: 100%; }
            .max-w-sm { max-width: 24rem; }
            .max-w-md { max-width: 28rem; }
            .max-w-lg { max-width: 32rem; }
            .max-w-xl { max-width: 36rem; }
            .max-w-2xl { max-width: 42rem; }
            .max-w-3xl { max-width: 48rem; }
            .max-w-4xl { max-width: 56rem; }
            .max-w-5xl { max-width: 64rem; }
            .max-w-6xl { max-width: 72rem; }
            .max-w-7xl { max-width: 80rem; }

            /* Enhanced Hero Image Styles */
.hero-image {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2;
}

.image-container {
    position: relative;
    width: 100%;
    max-width: 550px;
    filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.15));
    transition: transform 0.5s ease;
}

.image-container:hover {
    transform: translateY(-10px);
}

.main-image {
    width: 100%;
    height: auto;
    border-radius: 20px;
    z-index: 2;
    position: relative;
    transition: all 0.5s ease;
}

.floating-element {
    position: absolute;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--primary);
    z-index: 3;
    animation: float 6s ease-in-out infinite;
}

.floating-element-1 {
    top: -20px;
    right: 30px;
    animation-delay: 0s;
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
}

.floating-element-2 {
    bottom: 30px;
    left: -20px;
    animation-delay: 1s;
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
}

.floating-element-3 {
    bottom: -15px;
    right: 40px;
    animation-delay: 2s;
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
}

.glow-effect {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 20px;
    background: radial-gradient(circle at center, rgba(79, 70, 229, 0.2) 0%, transparent 70%);
    filter: blur(20px);
    z-index: 1;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.image-container:hover .glow-effect {
    opacity: 1;
}

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(5deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

@media (max-width: 768px) {
    .floating-element {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .floating-element-1 {
        top: -15px;
        right: 20px;
    }
    
    .floating-element-2 {
        bottom: 20px;
        left: -15px;
    }
    
    .floating-element-3 {
        bottom: -10px;
        right: 30px;
    }
}

        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 w-full bg-white bg-opacity-90 backdrop-blur-md shadow-sm z-50">
            <div class="container flex justify-between items-center py-4">
                <a href="#" class="text-2xl font-bold text-gray-900">
                    <span style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SIPROKER</span>
                </a>
                
                @if (Route::has('login'))
                    <div class="flex items-center gap-4 mb-4 mt-4">
                        @auth
                            <a href="{{ url('/users') }}" class="btn btn-primary">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline" style="color: var(--primary); border-color: var(--primary);">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container grid grid-cols-2">
                <div class="hero-content animate-fade-up" data-delay="0">
                    <h1 class="hero-title">SIPROKER</h1>
                    <h2 class="hero-subtitle">Sistem Informasi Program Kerja Dosen</h2>
                    <p class="hero-description">
                        Platform terintegrasi untuk mengelola, memantau, dan mengevaluasi program kerja dosen secara efisien dan transparan. Tingkatkan produktivitas dan kualitas program kerja Anda dengan SIPROKER.
                    </p>
                    <div class="flex gap-4">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt mr-2"></i> &nbsp;Masuk Sekarang
                        </a>
                        <a href="#features" class="btn btn-outline">
                            <i class="fas fa-info-circle mr-2"></i> &nbsp;Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>
                <div class="hero-image animate-fade-up" data-delay="0.3">
            <div class="image-container">
                <img src="https://img.freepik.com/free-vector/business-team-discussing-ideas-startup_74855-4380.jpg" alt="SIPROKER Illustration" class="main-image">
                <div class="floating-element floating-element-1">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="floating-element floating-element-2">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="floating-element floating-element-3">
                    <i class="fas fa-users"></i>
                </div>
                <div class="glow-effect"></div>
            </div>
        </div>

            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="section">
            <div class="container">
                <h2 class="section-title animate-fade-up" data-delay="0">Fitur Unggulan</h2>
                <p class="section-subtitle animate-fade-up" data-delay="0.1">
                    SIPROKER menyediakan berbagai fitur yang dirancang untuk memudahkan pengelolaan program kerja dosen
                </p>

                <div class="grid grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card animate-fade-up" data-delay="0.2">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3 class="feature-title">Manajemen Program Kerja</h3>
                        <p class="feature-description">
                            Buat, kelola, dan pantau program kerja dengan mudah melalui dashboard yang intuitif dan user-friendly.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card animate-fade-up" data-delay="0.3">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Laporan & Analitik</h3>
                        <p class="feature-description">
                            Dapatkan insight melalui laporan dan visualisasi data yang komprehensif untuk evaluasi program kerja.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card animate-fade-up" data-delay="0.4">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="feature-title">Notifikasi & Pengingat</h3>
                        <p class="feature-description">
                            Dapatkan pengingat tentang tenggat waktu dan pembaruan program kerja secara real-time.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card animate-fade-up" data-delay="0.5">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="feature-title">Kolaborasi Tim</h3>
                        <p class="feature-description">
                            Bekerja sama dengan rekan dosen dan staf dalam mengelola program kerja dengan fitur kolaborasi.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card animate-fade-up" data-delay="0.6">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3 class="feature-title">Dokumentasi Digital</h3>
                        <p class="feature-description">
                            Simpan dan kelola semua dokumen terkait program kerja dalam satu platform terintegrasi.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card animate-fade-up" data-delay="0.7">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Keamanan Data</h3>
                        <p class="feature-description">
                            Data program kerja Anda aman dengan sistem keamanan berlapis dan enkripsi tingkat tinggi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="section bg-gray-100">
            <div class="container">
                <h2 class="section-title animate-fade-up" data-delay="0">Testimoni Pengguna</h2>
                <p class="section-subtitle animate-fade-up" data-delay="0.1">
                    Lihat apa kata pengguna SIPROKER tentang pengalaman mereka
                </p>

                <div class="grid grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="testimonial-card animate-fade-up" data-delay="0.2">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar" style="background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
                                DR
                            </div>
                            <div>
                                <h4 class="testimonial-name">Dr. Rudi Hartono</h4>
                                <p class="testimonial-position">Dosen Fakultas Teknik</p>
                            </div>
                        </div>
                        <p class="testimonial-content">
                            "SIPROKER sangat membantu saya dalam mengelola dan melacak program kerja. Interface yang intuitif dan fitur yang lengkap membuat pekerjaan menjadi lebih efisien. Saya sangat merekomendasikan platform ini untuk semua dosen."
                        </p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="testimonial-card animate-fade-up" data-delay="0.3">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);">
                                SA
                            </div>
                            <div>
                                <h4 class="testimonial-name">Dr. Siti Aminah</h4>
                                <p class="testimonial-position">Dosen Fakultas Ekonomi</p>
                            </div>
                        </div>
                        <p class="testimonial-content">
                            "Fitur kolaborasi tim di SIPROKER memudahkan saya berkoordinasi dengan rekan dosen lain. Laporan analitik juga sangat membantu dalam evaluasi program kerja dan pengambilan keputusan strategis."
                        </p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="testimonial-card animate-fade-up" data-delay="0.4">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar" style="background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);">
                                BP
                            </div>
                            <div>
                                <h4 class="testimonial-name">Prof. Budi Prasetyo</h4>
                                <p class="testimonial-position">Ketua Jurusan Informatika</p>
                            </div>
                        </div>
                        <p class="testimonial-content">
                            "Sebagai ketua jurusan, SIPROKER membantu saya memantau kinerja program kerja seluruh dosen. Sistem notifikasi dan pengingat sangat berguna untuk memastikan semua tenggat waktu terpenuhi dan program berjalan lancar."
                        </p>
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-section">
            <div class="container">
                <h2 class="section-title animate-fade-up" data-delay="0">SIPROKER dalam Angka</h2>
                <p class="section-subtitle animate-fade-up" data-delay="0.1">
                    Dampak SIPROKER dalam meningkatkan efisiensi pengelolaan program kerja dosen
                </p>

                <div class="grid grid-cols-4 gap-8">
                    <div class="stat-item animate-fade-up" data-delay="0.2">
                        <div class="stat-value">500+</div>
                        <div class="stat-label">Dosen Aktif</div>
                    </div>
                    <div class="stat-item animate-fade-up" data-delay="0.3">
                        <div class="stat-value">1,200+</div>
                        <div class="stat-label">Program Kerja Terkelola</div>
                    </div>
                    <div class="stat-item animate-fade-up" data-delay="0.4">
                        <div class="stat-value">85%</div>
                        <div class="stat-label">Peningkatan Efisiensi</div>
                    </div>
                    <div class="stat-item animate-fade-up" data-delay="0.5">
                        <div class="stat-value">24/7</div>
                        <div class="stat-label">Dukungan Sistem</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2 class="cta-title animate-fade-up" data-delay="0">Siap Mengoptimalkan Program Kerja Anda?</h2>
                <p class="cta-description animate-fade-up" data-delay="0.1">
                    Bergabunglah dengan SIPROKER dan rasakan kemudahan dalam mengelola program kerja dosen. Tingkatkan produktivitas dan kualitas program kerja Anda sekarang juga!
                </p>
                <a href="{{ route('register') }}" class="btn btn-dark animate-fade-up" data-delay="0.2">
                    <i class="fas fa-user-plus mr-2"></i> Daftar Sekarang
                </a>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="section">
            <div class="container">
                <h2 class="section-title animate-fade-up" data-delay="0">Pertanyaan Umum</h2>
                <p class="section-subtitle animate-fade-up" data-delay="0.1">
                    Jawaban untuk pertanyaan yang sering diajukan tentang SIPROKER
                </p>

                <div class="max-w-4xl mx-auto">
                    <div class="faq-item animate-fade-up" data-delay="0.2">
                        <h3 class="faq-question">Bagaimana cara mendaftar di SIPROKER?</h3>
                        <p class="faq-answer">
                            Anda dapat mendaftar dengan mengklik tombol "Daftar Sekarang" dan mengikuti petunjuk pendaftaran. Setelah verifikasi oleh admin, akun Anda akan aktif dan Anda dapat mulai menggunakan semua fitur SIPROKER.
                        </p>
                    </div>
                    
                    <div class="faq-item animate-fade-up" data-delay="0.3">
                        <h3 class="faq-question">Apakah SIPROKER dapat diakses melalui perangkat mobile?</h3>
                        <p class="faq-answer">
                            Ya, SIPROKER dirancang dengan responsif dan dapat diakses melalui browser di perangkat mobile seperti smartphone dan tablet. Anda dapat mengelola program kerja kapan saja dan di mana saja.
                        </p>
                    </div>
                    
                    <div class="faq-item animate-fade-up" data-delay="0.4">
                        <h3 class="faq-question">Bagaimana keamanan data di SIPROKER?</h3>
                        <p class="faq-answer">
                            SIPROKER mengimplementasikan sistem keamanan berlapis, termasuk enkripsi data, autentikasi dua faktor, dan backup data reguler untuk memastikan keamanan informasi Anda. Kami memprioritaskan privasi dan keamanan data pengguna.
                        </p>
                    </div>

                    <div class="faq-item animate-fade-up" data-delay="0.5">
                        <h3 class="faq-question">Apakah ada pelatihan untuk menggunakan SIPROKER?</h3>
                        <p class="faq-answer">
                            Ya, kami menyediakan tutorial interaktif, dokumentasi lengkap, dan webinar pelatihan berkala untuk membantu pengguna memaksimalkan penggunaan SIPROKER. Tim dukungan kami juga siap membantu Anda kapan saja.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="grid grid-cols-4 gap-8 footer-grid">
                    <div>
                        <h3 class="footer-title">SIPROKER</h3>
                        <p class="footer-description">
                            Sistem Informasi Program Kerja Dosen yang memudahkan pengelolaan dan evaluasi program kerja secara efisien dan transparan.
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="footer-links-title">Tautan Cepat</h4>
                        <ul class="footer-links">
                            <li class="footer-link"><a href="#">Beranda</a></li>
                            <li class="footer-link"><a href="#features">Fitur</a></li>
                            <li class="footer-link"><a href="#">Tentang Kami</a></li>
                            <li class="footer-link"><a href="#">Kontak</a></li>
                            <li class="footer-link"><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="footer-links-title">Kontak</h4>
                        <div class="footer-contact">
                            <div class="footer-contact-item">
                                <i class="fas fa-map-marker-alt footer-contact-icon"></i>
                                <span>Jl. Gajah Mada Jl. Kandis Raya, Kp. Olo, Kec. Nanggalo, Kota Padang, Sumatera Barat 25173</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-phone footer-contact-icon"></i>
                                <span>(021) 1234-5678</span>
                            </div>
                            <div class="footer-contact-item">
                                <i class="fas fa-envelope footer-contact-icon"></i>
                                <span>info@siproker.ac.id</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="footer-links-title">Ikuti Kami</h4>
                        <div class="footer-social">
                            <a href="#" class="footer-social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="footer-social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="footer-social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="footer-social-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p class="footer-copyright">&copy; {{ date('Y') }} SIPROKER. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="#" class="footer-bottom-link">Kebijakan Privasi</a>
                        <a href="#" class="footer-bottom-link">Syarat & Ketentuan</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Animation on scroll
                function animateOnScroll() {
                    $('.animate-fade-up, .animate-fade-in, .animate-scale-in').each(function() {
                        const elementTop = $(this).offset().top;
                        const elementHeight = $(this).outerHeight();
                        const windowHeight = $(window).height();
                        const scrollY = $(window).scrollTop();
                        const delay = $(this).data('delay') || 0;
                        
                        if (scrollY > elementTop - windowHeight + elementHeight / 4) {
                            setTimeout(() => {
                                $(this).addClass('active');
                            }, delay * 1000);
                        }
                    });
                }
                
                // Initialize animations
                animateOnScroll();
                
                // Trigger animations on scroll
                $(window).on('scroll', function() {
                    animateOnScroll();
                });
                
                // Smooth scroll for anchor links
                $('a[href^="#"]').on('click', function(e) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $($(this).attr('href')).offset().top - 80
                    }, 800, 'swing');
                });
            });
        </script>
    </body>
</html>
         