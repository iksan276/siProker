<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SIPROKER - Sistem Informasi Program Kerja Dosen yang memudahkan pengelolaan dan evaluasi program kerja secara efisien dan transparan.">
    
    <title>SIPROKER - Sistem Informasi Program Kerja Dosen</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('asset/favicon.ico') }}" type="image/x-icon">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Animation Libraries -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper Slider CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- Styles -->
    <style>
        :root {
            /* Color Palette */
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --primary-lighter: #93c5fd;
            --primary-lightest: #dbeafe;
            --secondary: #7c3aed;
            --secondary-dark: #6d28d9;
            --secondary-light: #8b5cf6;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --accent-light: #fbbf24;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #0f172a;
            --dark-blue: #1e293b;
            --dark-light: #334155;
            --light: #f8fafc;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            
            /* Typography */
            --font-sans: 'Plus Jakarta Sans', sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;
            --font-size-5xl: 3rem;
            --font-size-6xl: 3.75rem;
            --font-size-7xl: 4.5rem;
            --font-size-8xl: 6rem;
            
            /* Spacing */
            --spacing-px: 1px;
            --spacing-0: 0;
            --spacing-0-5: 0.125rem;
            --spacing-1: 0.25rem;
            --spacing-1-5: 0.375rem;
            --spacing-2: 0.5rem;
            --spacing-2-5: 0.625rem;
            --spacing-3: 0.75rem;
            --spacing-3-5: 0.875rem;
            --spacing-4: 1rem;
            --spacing-5: 1.25rem;
            --spacing-6: 1.5rem;
            --spacing-7: 1.75rem;
            --spacing-8: 2rem;
            --spacing-9: 2.25rem;
            --spacing-10: 2.5rem;
            --spacing-11: 2.75rem;
            --spacing-12: 3rem;
            --spacing-14: 3.5rem;
            --spacing-16: 4rem;
            --spacing-20: 5rem;
            --spacing-24: 6rem;
            --spacing-28: 7rem;
            --spacing-32: 8rem;
            --spacing-36: 9rem;
            --spacing-40: 10rem;
            --spacing-44: 11rem;
            --spacing-48: 12rem;
            --spacing-52: 13rem;
            --spacing-56: 14rem;
            --spacing-60: 15rem;
            --spacing-64: 16rem;
            --spacing-72: 18rem;
            --spacing-80: 20rem;
            --spacing-96: 24rem;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-inner: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
            
            /* Transitions */
            --transition-fast: 0.2s;
            --transition-normal: 0.3s;
            --transition-slow: 0.5s;
            
            /* Border Radius */
            --radius-none: 0;
            --radius-sm: 0.125rem;
            --radius-default: 0.25rem;
            --radius-md: 0.375rem;
            --radius-lg: 0.5rem;
            --radius-xl: 0.75rem;
            --radius-2xl: 1rem;
            --radius-3xl: 1.5rem;
            --radius-full: 9999px;
        }
        
        /* Reset & Base Styles */
        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
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
            padding: 0 var(--spacing-6);
        }
        
        .section {
            padding: var(--spacing-24) 0;
            position: relative;
        }
        
        .section-sm {
            padding: var(--spacing-16) 0;
        }
        
        .section-lg {
            padding: var(--spacing-32) 0;
        }
        
        /* Typography */
        .section-title {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            margin-bottom: var(--spacing-6);
            color: var(--gray-900);
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: var(--radius-full);
        }
        
        .section-title.text-center::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-subtitle {
            font-size: var(--font-size-xl);
            margin-bottom: var(--spacing-12);
            color: var(--gray-600);
            max-width: 800px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-gradient {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            transition: all var(--transition-normal);
            padding: var(--spacing-4) 0;
        }
        
        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-md);
            padding: var(--spacing-3) 0;
        }
        
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
        }
        
        .navbar-logo img {
            height: 40px;
            width: auto;
        }
        
        .navbar-logo-text {
            font-weight: 700;
            font-size: var(--font-size-xl);
            color: var(--primary);
        }
        
        .navbar-menu {
            display: flex;
            gap: var(--spacing-8);
            align-items: center;
        }
        
        .navbar-link {
            font-weight: 500;
            color: var(--gray-700);
            transition: all var(--transition-fast);
            position: relative;
        }
        
        .navbar-link:hover {
            color: var(--primary);
        }
        
        .navbar-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary);
            transition: width var(--transition-normal);
        }
        
        .navbar-link:hover::after {
            width: 100%;
        }
        
        .navbar-cta {
            display: flex;
            gap: var(--spacing-4);
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--gray-700);
            font-size: var(--font-size-2xl);
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--primary-lightest) 0%, var(--light) 100%);
            overflow: hidden;
        }
        
        .hero-shape {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15vh;
            background-color: var(--light);
            clip-path: polygon(0 100%, 100% 100%, 100% 0);
            z-index: 1;
        }
        
        .hero-container {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-16);
            align-items: center;
            margin-top:100px;
        }
        
        .hero-content {
            max-width: 600px;
        }
        
        .hero-title {
            font-size: var(--font-size-6xl);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: var(--spacing-6);
            color: var(--dark);
        }
        
        .hero-subtitle {
            font-size: var(--font-size-xl);
            color: var(--gray-600);
            margin-bottom: var(--spacing-8);
        }
        
        .hero-cta {
            display: flex;
            gap: var(--spacing-4);
            margin-top: var(--spacing-8);
        }
        
        .hero-image-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .hero-image {
            max-width: 100%;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl);
            position: relative;
            z-index: 2;
        }
        
        .hero-image-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: var(--radius-2xl);
            transform: rotate(-3deg) scale(0.95);
            z-index: 1;
            opacity: 0.7;
        }
        
        .hero-particles {
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
        
        /* Floating Elements */
        .floating-card {
            position: absolute;
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: var(--spacing-4);
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
            z-index: 3;
            animation: float-card 6s infinite ease-in-out;
        }
        
        .floating-card-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--primary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: var(--font-size-xl);
        }
        
        .floating-card-content {
            flex: 1;
        }
        
        .floating-card-title {
            font-weight: 600;
            color: var(--gray-800);
            font-size: var(--font-size-sm);
        }
        
        .floating-card-subtitle {
            color: var(--gray-500);
            font-size: var(--font-size-xs);
        }
        
        .floating-card-1 {
            top: 20%;
            right: -5%;
            animation-delay: 0s;
        }
        
        .floating-card-2 {
            bottom: 15%;
            left: 0;
            animation-delay: 2s;
        }
        
        @keyframes float-card {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
            100% {
                transform: translateY(0);
            }
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-3) var(--spacing-6);
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: var(--font-size-base);
            transition: all var(--transition-fast);
            cursor: pointer;
            gap: var(--spacing-2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary-lighter);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.1);
        }
        
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.2);
            background: var(--primary-lightest);
        }
        
        .btn-lg {
            padding: var(--spacing-4) var(--spacing-8);
            font-size: var(--font-size-lg);
        }
        
        /* Features Section */
        .features {
            background-color: var(--light);
            position: relative;
            overflow: hidden;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-8);
            margin-top: var(--spacing-16);
        }
        
        .feature-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-8);
            box-shadow: var(--shadow-md);
            transition: all var(--transition-normal);
            position: relative;
            overflow: hidden;
            z-index: 1;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-lightest) 0%, var(--light) 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity var(--transition-normal);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, var(--primary-lightest) 0%, var(--primary-lighter) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-6);
            color: var(--primary);
            font-size: var(--font-size-2xl);
            transition: all var(--transition-normal);
        }
        
        .feature-card:hover .feature-icon {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            transform: scale(1.1);
        }
        
        .feature-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            margin-bottom: var(--spacing-4);
            color: var(--gray-900);
        }
        
        .feature-description {
            color: var(--gray-600);
            margin-bottom: var(--spacing-6);
            flex-grow: 1;
        }
        
        .feature-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            font-weight: 600;
            gap: var(--spacing-2);
            transition: all var(--transition-fast);
        }
        
        .feature-link:hover {
            gap: var(--spacing-3);
            color: var(--primary-dark);
        }
        
        /* How It Works Section */
        .how-it-works {
            background-color: var(--gray-50);
            position: relative;
            overflow: hidden;
        }
        
        .steps-container {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-16);
            margin-top: var(--spacing-16);
            position: relative;
        }
        
        .step-connector {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary-lighter), var(--secondary-light));
            transform: translateX(-50%);
            z-index: 1;
        }
        
        .step {
            display: grid;
            grid-template-columns: 1fr 100px 1fr;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step:nth-child(even) {
            direction: rtl;
        }
        
        .step:nth-child(even) .step-content {
            direction: ltr;
            text-align: right;
        }
        
        .step-content {
            padding: var(--spacing-6);
        }
        
        .step-number {
            width: 80px;
            height: 80px;
            border-radius: var(--radius-full);
            background: white;
            border: 2px solid var(--primary-lighter);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-2xl);
            font-weight: 800;
            color: var(--primary);
            margin: 0 auto;
            position: relative;
            z-index: 3;
            box-shadow: var(--shadow-lg);
        }
        
        .step-title {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            margin-bottom: var(--spacing-4);
            color: var(--gray-900);
        }
        
        .step-description {
            color: var(--gray-600);
        }
        
        /* Testimonials Section */
        .testimonials {
            background-color: var(--light);
            position: relative;
            overflow: hidden;
        }
        
        .testimonial-container {
            position: relative;
            padding: var(--spacing-8) 0;
        }
        
        .swiper-container {
            overflow: visible;
            padding: var(--spacing-8) 0;
        }
        
        .testimonial-card {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-8);
            box-shadow: var(--shadow-lg);
            height: 100%;
            transition: all var(--transition-normal);
            display: flex;
            flex-direction: column;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }
        
        .testimonial-content {
            font-size: var(--font-size-lg);
            color: var(--gray-700);
            margin-bottom: var(--spacing-6);
            flex-grow: 1;
            position: relative;
        }
        
        .testimonial-content::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: -10px;
            font-size: 80px;
            color: var(--primary-lightest);
            font-family: serif;
            z-index: -1;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: var(--spacing-4);
        }
        
        .testimonial-avatar {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-full);
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: var(--font-size-xl);
        }
        
        .testimonial-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .testimonial-info {
            flex: 1;
        }
        
        .testimonial-name {
            font-weight: 700;
            color: var(--gray-900);
        }
        
        .testimonial-position {
            color: var(--gray-500);
            font-size: var(--font-size-sm);
        }
        
        .testimonial-rating {
            margin-top: var(--spacing-2);
            color: var(--accent);
            display: flex;
            gap: 2px;
        }
        
        .swiper-pagination {
            position: relative;
            margin-top: var(--spacing-8);
        }
        
        .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: var(--primary-lighter);
            opacity: 0.5;
            transition: all var(--transition-fast);
        }
        
        .swiper-pagination-bullet-active {
            width: 30px;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            opacity: 1;
        }
        
        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: var(--spacing-8);
            margin-top: var(--spacing-12);
        }
        
        .stat-item {
            text-align: center;
            padding: var(--spacing-6);
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-xl);
            backdrop-filter: blur(5px);
            transition: all var(--transition-normal);
        }
        
        .stat-item:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.2);
        }
        
        .stat-value {
            font-size: var(--font-size-5xl);
            font-weight: 800;
            margin-bottom: var(--spacing-2);
            background: linear-gradient(90deg, white, rgba(255, 255, 255, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }
        
        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: var(--font-size-lg);
            font-weight: 500;
        }
        
        /* FAQ Section */
        .faq {
            background-color: var(--gray-50);
            position: relative;
            overflow: hidden;
        }
        
        .faq-container {
            max-width: 800px;
            margin: var(--spacing-16) auto 0;
        }
        
        .faq-item {
            background: white;
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-4);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all var(--transition-normal);
        }
        
        .faq-item:hover {
            box-shadow: var(--shadow-md);
        }
        
        .faq-question {
            padding: var(--spacing-5) var(--spacing-6);
            font-weight: 600;
            color: var(--gray-900);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }
        
        .faq-question::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--primary);
            transition: transform var(--transition-fast);
        }
        
        .faq-item.active .faq-question::after {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 var(--spacing-6);
            max-height: 0;
            overflow: hidden;
            color: var(--gray-600);
            transition: all var(--transition-normal);
        }
        
        .faq-item.active .faq-answer {
            padding: 0 var(--spacing-6) var(--spacing-5);
            max-height: 1000px;
        }
        
        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--primary-lightest) 0%, var(--light) 100%);
            position: relative;
            overflow: hidden;
        }
        
        .cta-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-16);
            align-items: center;
        }
        
        .cta-content {
            max-width: 500px;
        }
        
        .cta-title {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            margin-bottom: var(--spacing-6);
            color: var(--gray-900);
        }
        
        .cta-description {
            font-size: var(--font-size-lg);
            color: var(--gray-600);
            margin-bottom: var(--spacing-8);
        }
        
        .cta-form {
            background: white;
            border-radius: var(--radius-xl);
            padding: var(--spacing-8);
            box-shadow: var(--shadow-xl);
            max-width: 500px;
        }
        
        .cta-form-title {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            margin-bottom: var(--spacing-6);
            color: var(--gray-900);
            text-align: center;
        }
        
        .form-group {
            margin-bottom: var(--spacing-4);
        }
        
        .form-label {
            display: block;
            margin-bottom: var(--spacing-2);
            font-weight: 500;
            color: var(--gray-700);
        }
        
        .form-control {
            width: 100%;
            padding: var(--spacing-3) var(--spacing-4);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-lg);
            font-family: var(--font-sans);
            font-size: var(--font-size-base);
            transition: all var(--transition-fast);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-lightest);
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark-blue);
            color: white;
            padding: var(--spacing-16) 0 var(--spacing-8);
            position: relative;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: var(--spacing-8);
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            gap: var(--spacing-3);
            margin-bottom: var(--spacing-4);
        }
        
        .footer-logo img {
            height: 40px;
            width: auto;
        }
        
        .footer-logo-text {
            font-weight: 700;
            font-size: var(--font-size-xl);
            color: white;
        }
        
        .footer-description {
            color: var(--gray-400);
            margin-bottom: var(--spacing-6);
        }
        
        .footer-social {
            display: flex;
            gap: var(--spacing-4);
        }
        
        .footer-social-link {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all var(--transition-fast);
        }
        
        .footer-social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-title {
            font-size: var(--font-size-lg);
            font-weight: 700;
            margin-bottom: var(--spacing-6);
            color: white;
        }
        
        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-3);
        }
        
        .footer-link a {
            color: var(--gray-400);
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-2);
        }
        
        .footer-link a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .footer-contact-item {
            display: flex;
            gap: var(--spacing-3);
            margin-bottom: var(--spacing-4);
            color: var(--gray-400);
        }
        
        .footer-contact-icon {
            color: var(--primary-lighter);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: var(--spacing-12);
            padding-top: var(--spacing-8);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--gray-400);
        }
        
        .footer-bottom-links {
            display: flex;
            gap: var(--spacing-6);
        }
        
        .footer-bottom-link {
            color: var(--gray-400);
            transition: color var(--transition-fast);
        }
        
        .footer-bottom-link:hover {
            color: white;
        }
        
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-normal);
            z-index: 999;
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }
        
        /* Responsive Styles */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: var(--font-size-5xl);
            }
            
            .section-title {
                font-size: var(--font-size-3xl);
            }
            
            .cta-title {
                font-size: var(--font-size-3xl);
            }
        }
        
        @media (max-width: 992px) {
            .navbar-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: white;
                padding: var(--spacing-4) 0;
                box-shadow: var(--shadow-md);
                flex-direction: column;
                gap: var(--spacing-4);
                z-index: 1000;
            }
            
            .navbar-menu.active {
                display: flex;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .hero-container {
                grid-template-columns: 1fr;
                gap: var(--spacing-8);
                text-align: center;
            }
            
            .hero-content {
                max-width: 100%;
            }
            
            .hero-cta {
                justify-content: center;
            }
            
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: var(--spacing-6);
            }
            
            .cta-container {
                grid-template-columns: 1fr;
                gap: var(--spacing-8);
                text-align: center;
            }
            
            .cta-content {
                max-width: 100%;
            }
            
            .cta-form {
                margin: 0 auto;
            }
            
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: var(--spacing-8) var(--spacing-4);
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: var(--font-size-4xl);
            }
            
            .section-title {
                font-size: var(--font-size-2xl);
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .step {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .step:nth-child(even) {
                direction: ltr;
            }
            
            .step:nth-child(even) .step-content {
                text-align: center;
            }
            
            .step-connector {
                left: 50%;
            }
            
            .step-number {
                margin: var(--spacing-6) auto;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-8);
                text-align: center;
            }
            
            .footer-logo {
                justify-content: center;
            }
            
            .footer-social {
                justify-content: center;
            }
            
            .footer-contact-item {
                justify-content: center;
            }
            
            .footer-bottom {
                flex-direction: column;
                gap: var(--spacing-4);
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: var(--font-size-3xl);
            }
            
            .hero-subtitle {
                font-size: var(--font-size-lg);
            }
            
            .hero-cta {
                flex-direction: column;
                gap: var(--spacing-4);
            }
            
            .btn {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-bottom-links {
                flex-direction: column;
                gap: var(--spacing-4);
                align-items: center;
            }
        }
        
        /* Animation Utilities */
        .reveal-animation {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        
        .reveal-animation.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .preloader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .preloader-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid var(--primary-lightest);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="preloader-spinner"></div>
    </div>
    
    <!-- Back to Top Button -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container navbar-container">
            <div class="navbar-logo">
                <img src="{{ asset('asset/itp.png') }}" alt="SIPROKER Logo">
                <div class="navbar-logo-text">SIPROKER</div>
            </div>
            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="navbar-menu">
                <a href="#features" class="navbar-link">Fitur</a>
                <a href="#how-it-works" class="navbar-link">Cara Kerja</a>
                <a href="#testimonials" class="navbar-link">Testimoni</a>
                <a href="#faq" class="navbar-link">FAQ</a>
            </div>
            
            <div class="navbar-cta">
                @if (Route::has('login'))
                    @auth
                  
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                 

                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <div class="container hero-container">
            <div class="hero-content" data-aos="fade-right" data-aos-duration="1000">
                <h1 class="hero-title">
                    <span class="text-gradient">SIPROKER</span><br>
                    Sistem Informasi Program Kerja Dosen
                </h1>
                <p class="hero-subtitle">
                    Platform terintegrasi untuk mengelola, memantau, dan mengevaluasi program kerja dosen secara efisien dan transparan. Tingkatkan produktivitas dan kualitas program kerja Anda dengan SIPROKER.
                </p>
                <div class="hero-cta">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                        </a>
                    @endauth
                    <a href="#features" class="btn btn-secondary btn-lg">
                        <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            
            <div class="hero-image-container" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                <div class="hero-image-bg"></div>
                <img src="https://img.freepik.com/free-vector/business-team-discussing-ideas-startup_74855-4380.jpg" alt="SIPROKER Illustration" class="hero-image">
                
                <div class="floating-card floating-card-1">
                    <div class="floating-card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="floating-card-content">
                        <div class="floating-card-title">Laporan Real-time</div>
                        <div class="floating-card-subtitle">Pantau progres program kerja</div>
                    </div>
                </div>
                
                <div class="floating-card floating-card-2">
                    <div class="floating-card-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="floating-card-content">
                        <div class="floating-card-title">Manajemen Tugas</div>
                        <div class="floating-card-subtitle">Kelola program kerja dengan mudah</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="hero-shape"></div>
    </section>
    
    <!-- Features Section -->
    <section class="section features" id="features">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Fitur Unggulan</h2>
            <p class="section-subtitle text-start" data-aos="fade-up" data-aos-delay="100">
                SIPROKER menyediakan berbagai fitur yang dirancang untuk memudahkan pengelolaan program kerja dosen
            </p>
            
            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="feature-title">Manajemen Program Kerja</h3>
                    <p class="feature-description">
                        Buat, kelola, dan pantau program kerja dengan mudah melalui dashboard yang intuitif dan user-friendly.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Laporan & Analitik</h3>
                    <p class="feature-description">
                        Dapatkan insight melalui laporan dan visualisasi data yang komprehensif untuk evaluasi program kerja.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="feature-title">Notifikasi & Pengingat</h3>
                    <p class="feature-description">
                        Dapatkan pengingat tentang tenggat waktu dan pembaruan program kerja secara real-time.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Kolaborasi Tim</h3>
                    <p class="feature-description">
                        Bekerja sama dengan rekan dosen dan staf dalam mengelola program kerja dengan fitur kolaborasi.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="feature-title">Dokumentasi Digital</h3>
                    <p class="feature-description">
                        Simpan dan kelola semua dokumen terkait program kerja dalam satu platform terintegrasi.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card" data-aos="fade-up" data-aos-delay="700">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Keamanan Data</h3>
                    <p class="feature-description">
                        Data program kerja Anda aman dengan sistem keamanan berlapis dan enkripsi tingkat tinggi.
                    </p>
                    <a href="#" class="feature-link">
                        Pelajari lebih lanjut <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="section how-it-works" id="how-it-works">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Cara Kerja SIPROKER</h2>
            <p class="section-subtitle text-start" data-aos="fade-up" data-aos-delay="100">
                Proses sederhana untuk memulai dan mengelola program kerja dosen dengan SIPROKER
            </p>
            
            <div class="steps-container">
                <div class="step-connector"></div>
                
                <!-- Step 1 -->
                <div class="step" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-content">
                        <h3 class="step-title">Registrasi & Login</h3>
                        <p class="step-description">
                            Daftar akun SIPROKER atau login dengan kredensial yang diberikan oleh administrator. Akses dashboard personal Anda untuk memulai.
                        </p>
                    </div>
                    <div class="step-number">1</div>
                    <div></div>
                </div>
                
                <!-- Step 2 -->
                <div class="step" data-aos="fade-up" data-aos-delay="300">
                    <div></div>
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3 class="step-title">Buat Program Kerja</h3>
                        <p class="step-description">
                            Buat program kerja baru dengan mengisi detail seperti judul, deskripsi, tenggat waktu, dan anggota tim yang terlibat.
                        </p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="step" data-aos="fade-up" data-aos-delay="400">
                    <div class="step-content">
                        <h3 class="step-title">Kelola & Pantau Progres</h3>
                        <p class="step-description">
                            Kelola program kerja, tambahkan tugas, unggah dokumen, dan pantau progres melalui dashboard yang informatif.
                        </p>
                    </div>
                    <div class="step-number">3</div>
                    <div></div>
                </div>
                
                <!-- Step 4 -->
                <div class="step" data-aos="fade-up" data-aos-delay="500">
                    <div></div>
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3 class="step-title">Evaluasi & Laporan</h3>
                        <p class="step-description">
                            Evaluasi keberhasilan program kerja dan hasilkan laporan komprehensif untuk keperluan dokumentasi dan peningkatan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="section testimonials" id="testimonials">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Testimoni Pengguna</h2>
            <p class="section-subtitle text-start" data-aos="fade-up" data-aos-delay="100">
                Lihat apa kata pengguna SIPROKER tentang pengalaman mereka
            </p>
            
            <div class="testimonial-container" data-aos="fade-up" data-aos-delay="200">
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <!-- Testimonial 1 -->
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "SIPROKER sangat membantu saya dalam mengelola dan melacak program kerja. Interface yang intuitif dan fitur yang lengkap membuat pekerjaan menjadi lebih efisien. Saya sangat merekomendasikan platform ini untuk semua dosen."
                                </div>
                                <div class="testimonial-author">
                                    <div class="testimonial-avatar">
                                        DR
                                    </div>
                                    <div class="testimonial-info">
                                        <div class="testimonial-name">Dr. Rudi Hartono</div>
                                        <div class="testimonial-position">Dosen Fakultas Teknik</div>
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
                        </div>
                        
                        <!-- Testimonial 2 -->
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "Fitur kolaborasi tim di SIPROKER memudahkan saya berkoordinasi dengan rekan dosen lain. Laporan analitik juga sangat membantu dalam evaluasi program kerja dan pengambilan keputusan strategis."
                                </div>
                                <div class="testimonial-author">
                                    <div class="testimonial-avatar">
                                        SA
                                    </div>
                                    <div class="testimonial-info">
                                        <div class="testimonial-name">Dr. Siti Aminah</div>
                                        <div class="testimonial-position">Dosen Fakultas Ekonomi</div>
                                        <div class="testimonial-rating">
                                        <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Testimonial 3 -->
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "Sebagai ketua jurusan, SIPROKER membantu saya memantau kinerja program kerja seluruh dosen. Sistem notifikasi dan pengingat sangat berguna untuk memastikan semua tenggat waktu terpenuhi dan program berjalan lancar."
                                </div>
                                <div class="testimonial-author">
                                    <div class="testimonial-avatar">
                                        BP
                                    </div>
                                    <div class="testimonial-info">
                                        <div class="testimonial-name">Prof. Budi Prasetyo</div>
                                        <div class="testimonial-position">Ketua Jurusan Informatika</div>
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
                        </div>
                        
                        <!-- Testimonial 4 -->
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    "SIPROKER telah mengubah cara kami mengelola program kerja di fakultas. Dengan adanya sistem ini, transparansi dan akuntabilitas meningkat secara signifikan. Semua pihak dapat dengan mudah melihat progres dan hasil dari setiap program."
                                </div>
                                <div class="testimonial-author">
                                    <div class="testimonial-avatar">
                                        LS
                                    </div>
                                    <div class="testimonial-info">
                                        <div class="testimonial-name">Dr. Lina Santoso</div>
                                        <div class="testimonial-position">Dekan Fakultas MIPA</div>
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
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Stats Section -->
    <section class="section-sm stats">
        <div class="container">
            <h2 class="section-title text-center" style="color: white;" data-aos="fade-up">SIPROKER dalam Angka</h2>
            <p class="section-subtitle text-start" style="color: rgba(255, 255, 255, 0.8);" data-aos="fade-up" data-aos-delay="100">
                Dampak SIPROKER dalam meningkatkan efisiensi pengelolaan program kerja dosen
            </p>
            
            <div class="stats-grid">
                <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-value" data-count="500">0</div>
                    <div class="stat-label">Dosen Aktif</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-value" data-count="1200">0</div>
                    <div class="stat-label">Program Kerja Terkelola</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-value" data-count="85">0</div>
                    <div class="stat-label">Peningkatan Efisiensi</div>
                </div>
                
                <div class="stat-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Dukungan Sistem</div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- FAQ Section -->
    <section class="section faq" id="faq">
        <div class="container">
            <h2 class="section-title text-center" data-aos="fade-up">Pertanyaan Umum</h2>
            <p class="section-subtitle text-start" data-aos="fade-up" data-aos-delay="100">
                Jawaban untuk pertanyaan yang sering diajukan tentang SIPROKER
            </p>
            
            <div class="faq-container">
                <!-- FAQ Item 1 -->
                <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-question">Bagaimana cara mendaftar di SIPROKER?</div>
                    <div class="faq-answer">
                        <p>Anda dapat mendaftar dengan mengklik tombol "Daftar Sekarang" dan mengikuti petunjuk pendaftaran. Setelah verifikasi oleh admin, akun Anda akan aktif dan Anda dapat mulai menggunakan semua fitur SIPROKER.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="faq-question">Apakah SIPROKER dapat diakses melalui perangkat mobile?</div>
                    <div class="faq-answer">
                        <p>Ya, SIPROKER dirancang dengan responsif dan dapat diakses melalui browser di perangkat mobile seperti smartphone dan tablet. Anda dapat mengelola program kerja kapan saja dan di mana saja.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="faq-question">Bagaimana keamanan data di SIPROKER?</div>
                    <div class="faq-answer">
                        <p>SIPROKER mengimplementasikan sistem keamanan berlapis, termasuk enkripsi data, autentikasi dua faktor, dan backup data reguler untuk memastikan keamanan informasi Anda. Kami memprioritaskan privasi dan keamanan data pengguna.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="faq-question">Apakah ada pelatihan untuk menggunakan SIPROKER?</div>
                    <div class="faq-answer">
                        <p>Ya, kami menyediakan tutorial interaktif, dokumentasi lengkap, dan webinar pelatihan berkala untuk membantu pengguna memaksimalkan penggunaan SIPROKER. Tim dukungan kami juga siap membantu Anda kapan saja.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="faq-item" data-aos="fade-up" data-aos-delay="600">
                    <div class="faq-question">Bagaimana cara melaporkan masalah atau memberikan umpan balik?</div>
                    <div class="faq-answer">
                        <p>Anda dapat melaporkan masalah atau memberikan umpan balik melalui fitur "Bantuan & Dukungan" di dalam aplikasi. Alternatif lain, Anda dapat menghubungi tim dukungan kami melalui email di support@siproker.ac.id atau telepon di (021) 1234-5678.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="section cta">
        <div class="container cta-container">
            <div class="cta-content" data-aos="fade-right">
                <h2 class="cta-title">Siap Mengoptimalkan Program Kerja Anda?</h2>
                <p class="cta-description">
                    Bergabunglah dengan SIPROKER dan rasakan kemudahan dalam mengelola program kerja dosen. Tingkatkan produktivitas dan kualitas program kerja Anda sekarang juga!
                </p>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                </a>
            </div>
            
            <div class="cta-form" data-aos="fade-left">
                <h3 class="cta-form-title">Hubungi Kami</h3>
                <form>
                    <div class="form-group">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="name" class="form-control" placeholder="Masukkan nama lengkap Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" class="form-control" placeholder="Masukkan alamat email Anda">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Subjek</label>
                        <input type="text" id="subject" class="form-control" placeholder="Masukkan subjek pesan">
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea id="message" class="form-control" rows="4" placeholder="Tulis pesan Anda di sini"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-logo">
                        <img src="{{ asset('asset/itp.png') }}" alt="SIPROKER Logo">
                        <div class="footer-logo-text">SIPROKER</div>
                    </div>
                    <p class="footer-description">
                        Sistem Informasi Program Kerja Dosen yang memudahkan pengelolaan dan evaluasi program kerja secara efisien dan transparan.
                    </p>
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
                
                <div>
                    <h4 class="footer-title">Tautan Cepat</h4>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#home"><i class="fas fa-chevron-right"></i> Beranda</a></li>
                        <li class="footer-link"><a href="#features"><i class="fas fa-chevron-right"></i> Fitur</a></li>
                        <li class="footer-link"><a href="#how-it-works"><i class="fas fa-chevron-right"></i> Cara Kerja</a></li>
                        <li class="footer-link"><a href="#testimonials"><i class="fas fa-chevron-right"></i> Testimoni</a></li>
                        <li class="footer-link"><a href="#faq"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-title">Layanan</h4>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Manajemen Program Kerja</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Laporan & Analitik</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Kolaborasi Tim</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Dokumentasi Digital</a></li>
                        <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Dukungan Teknis</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="footer-title">Kontak</h4>
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
            
            <div class="footer-bottom">
                <p class="footer-copyright">&copy; {{ date('Y') }} SIPROKER. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#" class="footer-bottom-link">Kebijakan Privasi</a>
                    <a href="#" class="footer-bottom-link">Syarat & Ketentuan</a>
                    <a href="#" class="footer-bottom-link">Peta Situs</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialize AOS (Animate on Scroll)
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Preloader
        $(window).on('load', function() {
            setTimeout(function() {
                $('.preloader').addClass('fade-out');
            }, 500);
        });
        
        // Mobile Menu Toggle
        $('.mobile-menu-toggle').on('click', function() {
            $('.navbar-menu').toggleClass('active');
        });
        
        // Smooth Scroll
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            
            var target = $(this.hash);
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800, 'swing');
                
                // Close mobile menu if open
                $('.navbar-menu').removeClass('active');
            }
        });
        
        // Back to Top Button
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').addClass('visible');
            } else {
                $('.back-to-top').removeClass('visible');
            }
        });
        
        $('.back-to-top').on('click', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 800, 'swing');
            return false;
        });
        
        // Testimonials Slider
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
        
        // FAQ Accordion
        $('.faq-question').on('click', function() {
            $(this).parent().toggleClass('active');
            $(this).parent().siblings().removeClass('active');
        });
        
        // Counter Animation
        function animateCounter() {
            $('.stat-value').each(function() {
                var $this = $(this);
                var countTo = $this.attr('data-count');
                
                if (countTo && !$this.hasClass('counted')) {
                    $({ countNum: 0 }).animate({
                        countNum: countTo
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.countNum));
                        },
                        complete: function() {
                            $this.text(this.countNum);
                            $this.addClass('counted');
                            
                            // Add + sign to percentage
                            if ($this.parent().find('.stat-label').text().includes('Efisiensi')) {
                                $this.text($this.text() + '%');
                            }
                            
                            // Add + sign to numbers
                            if ($this.parent().find('.stat-label').text().includes('Dosen') || 
                                $this.parent().find('.stat-label').text().includes('Program')) {
                                $this.text($this.text() + '+');
                            }
                        }
                    });
                }
            });
        }
        
        // Trigger counter animation when stats section is in viewport
        $(window).on('scroll', function() {
            var statsSection = $('.stats');
            if (statsSection.length) {
                var statsSectionTop = statsSection.offset().top;
                var statsSectionHeight = statsSection.outerHeight();
                var windowHeight = $(window).height();
                var scrollY = $(window).scrollTop();
                
                if (scrollY > statsSectionTop - windowHeight + statsSectionHeight / 2) {
                    animateCounter();
                }
            }
        });
        
        // Form Validation
        $('form').on('submit', function(e) {
            e.preventDefault();
            
            var name = $('#name').val();
            var email = $('#email').val();
            var subject = $('#subject').val();
            var message = $('#message').val();
            var isValid = true;
            
            if (!name) {
                isValid = false;
                $('#name').addClass('is-invalid');
            } else {
                $('#name').removeClass('is-invalid');
            }
            
            if (!email || !isValidEmail(email)) {
                isValid = false;
                $('#email').addClass('is-invalid');
            } else {
                $('#email').removeClass('is-invalid');
            }
            
            if (!subject) {
                isValid = false;
                $('#subject').addClass('is-invalid');
            } else {
                $('#subject').removeClass('is-invalid');
            }
            
            if (!message) {
                isValid = false;
                $('#message').addClass('is-invalid');
            } else {
                $('#message').removeClass('is-invalid');
            }
            
            if (isValid) {
                // Show success message
                alert('Pesan Anda telah berhasil dikirim. Terima kasih telah menghubungi kami!');
                
                // Reset form
                this.reset();
            }
        });
        
        function isValidEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
        
        // Navbar Scroll Effect
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 50) {
                $('.navbar').addClass('navbar-scrolled');
            } else {
                $('.navbar').removeClass('navbar-scrolled');
            }
        });
        
        // Reveal Animation on Scroll
        function revealOnScroll() {
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            
            $('.reveal-animation').each(function() {
                var elementTop = $(this).offset().top;
                var elementVisible = 150;
                
                if (elementTop < scrollTop + windowHeight - elementVisible) {
                    $(this).addClass('active');
                }
            });
        }
        
        $(window).on('scroll', revealOnScroll);
        revealOnScroll();
    </script>
</body>
</html>

