<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'SIPROKER - ITP' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Load jQuery first -->
    <script src="{{ asset('sb-admin/vendor/jquery/jquery.min.js') }}"></script>
    
    <!-- Then load other CSS and JS dependencies -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('asset/itp.png') }}">
    <link href="{{ asset('sb-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sb-admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sb-admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="{{ asset('custom/select2.css') }}" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Tambahkan TreeGrid CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-treegrid/0.2.0/css/jquery.treegrid.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     
   <style>
/* Enhanced Tooltip Styling - keep existing */
.fas.fa-info-circle.text-primary {
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.3s ease, color 0.3s ease;
  position: relative;
}

/* ... keep existing tooltip styles ... */

/* Professional Toast Notification Styles - Enhanced */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 420px;
    width: 100%;
    pointer-events: none;
}

.custom-toast {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.15),
        0 12px 24px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
    margin-bottom: 16px;
    overflow: hidden;
    transform: translateX(120%) scale(0.8);
    transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 6px solid;
    position: relative;
    pointer-events: auto;
    min-height: 80px;
}

.custom-toast::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, 
        transparent 0%, 
        currentColor 20%, 
        rgba(255,255,255,0.8) 50%, 
        currentColor 80%, 
        transparent 100%);
    animation: shimmerWave 3s ease-in-out infinite;
    opacity: 0.7;
}

.custom-toast::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 50% 0%, rgba(255,255,255,0.1) 0%, transparent 50%);
    pointer-events: none;
}

.custom-toast.show {
    transform: translateX(0) scale(1);
    animation: toastSlideIn 0.7s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.custom-toast.hide {
    transform: translateX(120%) scale(0.8);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.55, 0.085, 0.68, 0.53);
}

/* Toast Type Styles */
.custom-toast.toast-success {
    border-left-color: #10b981;
    color: #10b981;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.custom-toast.toast-error {
    border-left-color: #ef4444;
    color: #ef4444;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.custom-toast.toast-warning {
    border-left-color: #f59e0b;
    color: #f59e0b;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

.custom-toast.toast-info {
    border-left-color: #3b82f6;
    color: #3b82f6;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(255, 255, 255, 0.95) 100%);
}

/* Toast Header */
.toast-header {
    background: transparent;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    padding: 20px 24px 16px;
    display: flex;
    align-items: center;
    font-weight: 600;
    position: relative;
}

/* Animated Icons */
.toast-icon {
    width: 32px;
    height: 32px;
    margin-right: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 14px;
    color: white;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
}

/* Success Icon - Animated Checkmark */
.toast-success .toast-icon {
    background: linear-gradient(135deg, #10b981, #059669);
}

.toast-success .toast-icon::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    border: 2px solid white;
    border-radius: 50%;
    animation: checkCircle 0.6s ease-in-out;
}

.toast-success .toast-icon::after {
    content: '';
    position: absolute;
    width: 6px;
    height: 10px;
    border: 2px solid white;
    border-top: none;
    border-left: none;
    transform: rotate(45deg) translate(-1px, -2px);
    animation: checkMark 0.8s ease-in-out 0.3s both;
}

/* Error Icon - Animated X */
.toast-error .toast-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}

.toast-error .toast-icon::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 2px;
    background: white;
    border-radius: 1px;
    transform: rotate(45deg);
    animation: errorLine1 0.6s ease-in-out;
}

.toast-error .toast-icon::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 2px;
    background: white;
    border-radius: 1px;
    transform: rotate(-45deg);
    animation: errorLine2 0.6s ease-in-out 0.2s both;
}

/* Warning Icon - Animated Exclamation */
.toast-warning .toast-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.toast-warning .toast-icon::before {
    content: '';
    position: absolute;
    width: 2px;
    height: 8px;
    background: white;
    border-radius: 1px;
    top: 6px;
    animation: warningLine 0.6s ease-in-out;
}

.toast-warning .toast-icon::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 2px;
    background: white;
    border-radius: 50%;
    bottom: 6px;
    animation: warningDot 0.4s ease-in-out 0.4s both;
}

/* Info Icon - Animated i */
.toast-info .toast-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.toast-info .toast-icon::before {
    content: '';
    position: absolute;
    width: 2px;
    height: 2px;
    background: white;
    border-radius: 50%;
    top: 6px;
    animation: infoDot 0.4s ease-in-out;
}

.toast-info .toast-icon::after {
    content: '';
    position: absolute;
    width: 2px;
    height: 8px;
    background: white;
    border-radius: 1px;
    bottom: 6px;
    animation: infoLine 0.6s ease-in-out 0.2s both;
}

/* Toast Title */
.toast-title {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    flex: 1;
    line-height: 1.4;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Toast Close Button */
.toast-close {
    background: rgba(107, 114, 128, 0.1);
    border: none;
    font-size: 16px;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
}

.toast-close:hover {
    background: rgba(107, 114, 128, 0.2);
    color: #374151;
    transform: rotate(90deg) scale(1.1);
}

.toast-close:active {
    transform: rotate(90deg) scale(0.95);
}

/* Toast Body */
.toast-body {
    padding: 0 24px 24px;
    color: #4b5563;
    font-size: 14px;
    line-height: 1.6;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Progress Bar */
.toast-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: currentColor;
    border-radius: 0 0 20px 20px;
    transform-origin: left;
    animation: progressBar 5s linear;
    opacity: 0.8;
}

/* Keyframe Animations */
@keyframes toastSlideIn {
    0% {
        transform: translateX(120%) scale(0.8) rotate(10deg);
        opacity: 0;
    }
    50% {
        transform: translateX(-10px) scale(1.02) rotate(-2deg);
        opacity: 0.8;
    }
    100% {
        transform: translateX(0) scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes shimmerWave {
    0% {
        transform: translateX(-100%) scaleX(0);
    }
    50% {
        transform: translateX(0%) scaleX(1);
    }
    100% {
        transform: translateX(100%) scaleX(0);
    }
}

/* Success Animations */
@keyframes checkCircle {
    0% {
        transform: scale(0) rotate(180deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.1) rotate(90deg);
        opacity: 0.8;
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes checkMark {
    0% {
        width: 0;
        height: 0;
        opacity: 0;
    }
    50% {
        width: 6px;
        height: 0;
        opacity: 0.8;
    }
    100% {
        width: 6px;
        height: 10px;
        opacity: 1;
    }
}

/* Error Animations */
@keyframes errorLine1 {
    0% {
        width: 0;
        transform: rotate(45deg) scale(0);
    }
    50% {
        width: 18px;
        transform: rotate(45deg) scale(1.1);
    }
    100% {
        width: 16px;
        transform: rotate(45deg) scale(1);
    }
}

@keyframes errorLine2 {
    0% {
        width: 0;
        transform: rotate(-45deg) scale(0);
    }
    50% {
        width: 18px;
        transform: rotate(-45deg) scale(1.1);
    }
    100% {
        width: 16px;
        transform: rotate(-45deg) scale(1);
    }
}

/* Warning Animations */
@keyframes warningLine {
    0% {
        height: 0;
        transform: scaleY(0);
    }
    50% {
        height: 10px;
        transform: scaleY(1.2);
    }
    100% {
        height: 8px;
        transform: scaleY(1);
    }
}

@keyframes warningDot {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.5);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Info Animations */
@keyframes infoDot {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.5);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes infoLine {
    0% {
        height: 0;
        transform: scaleY(0);
    }
    50% {
        height: 10px;
        transform: scaleY(1.2);
    }
    100% {
        height: 8px;
        transform: scaleY(1);
    }
}

@keyframes progressBar {
    0% {
        transform: scaleX(1);
        opacity: 0.8;
    }
    100% {
        transform: scaleX(0);
        opacity: 0.3;
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .toast-container {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
    
    .custom-toast {
        margin-bottom: 12px;
        border-radius: 16px;
        min-height: 70px;
    }
    
    .toast-header {
        padding: 16px 20px 12px;
    }
    
        .toast-body {
        padding: 0 20px 20px;
        font-size: 13px;
    }
    
    .toast-icon {
        width: 28px;
        height: 28px;
        margin-right: 12px;
    }
    
    .toast-title {
        font-size: 15px;
    }
    
    .toast-close {
        width: 24px;
        height: 24px;
    }
}

@media (max-width: 480px) {
    .toast-container {
        top: 8px;
        right: 8px;
        left: 8px;
    }
    
    .custom-toast {
        border-radius: 12px;
        min-height: 60px;
    }
    
    .toast-header {
        padding: 12px 16px 8px;
    }
    
    .toast-body {
        padding: 0 16px 16px;
        font-size: 12px;
    }
    
    .toast-icon {
        width: 24px;
        height: 24px;
        margin-right: 10px;
    }
    
    .toast-title {
        font-size: 14px;
    }
    
    .toast-close {
        width: 20px;
        height: 20px;
        font-size: 14px;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .custom-toast {
        background: rgba(17, 24, 39, 0.95);
        border: 1px solid rgba(75, 85, 99, 0.3);
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.3),
            0 12px 24px rgba(0, 0, 0, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }
    
    .toast-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(17, 24, 39, 0.95) 100%) !important;
    }
    
    .toast-error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(17, 24, 39, 0.95) 100%) !important;
    }
    
    .toast-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(17, 24, 39, 0.95) 100%) !important;
    }
    
    .toast-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(17, 24, 39, 0.95) 100%) !important;
    }
    
    .toast-title {
        color: #f9fafb;
    }
    
    .toast-body {
        color: #d1d5db;
    }
    
    .toast-close {
        color: #9ca3af;
        background: rgba(75, 85, 99, 0.2);
    }
    
    .toast-close:hover {
        background: rgba(75, 85, 99, 0.4);
        color: #f3f4f6;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .custom-toast {
        border: 2px solid currentColor;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }
    
    .toast-title {
        font-weight: 800;
    }
    
    .toast-body {
        font-weight: 500;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .custom-toast {
        transition: opacity 0.3s ease;
        animation: none;
    }
    
    .custom-toast.show {
        animation: none;
        transform: translateX(0) scale(1);
    }
    
    .toast-icon::before,
    .toast-icon::after {
        animation: none;
    }
    
    .toast-progress {
        animation: progressBarReduced 5s linear;
    }
    
    @keyframes progressBarReduced {
        0% { width: 100%; }
        100% { width: 0%; }
    }
}

/* Loading state for icons */
.toast-icon.loading {
    animation: iconSpin 1s linear infinite;
}

@keyframes iconSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Hover effects for entire toast */
.custom-toast:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 
        0 32px 64px rgba(0, 0, 0, 0.2),
        0 16px 32px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.custom-toast:hover .toast-progress {
    animation-play-state: paused;
}

/* Multiple toast stacking */
.custom-toast:nth-child(2) {
    transform: translateX(120%) scale(0.95);
    z-index: -1;
}

.custom-toast:nth-child(3) {
    transform: translateX(120%) scale(0.9);
    z-index: -2;
}

.custom-toast.show:nth-child(2) {
    transform: translateX(0) scale(0.95);
    margin-top: -8px;
}

.custom-toast.show:nth-child(3) {
    transform: translateX(0) scale(0.9);
    margin-top: -16px;
}

</style>



     <style>
    /* Enhanced Tooltip Styling */
.fas.fa-info-circle.text-primary {
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.3s ease, color 0.3s ease;
  position: relative;
}

.fas.fa-info-circle.text-primary:hover {
  transform: scale(1.2);
  color: #007bff !important;
  animation: pulse 1.5s infinite;
}

/* Custom tooltip styling */
.tooltip-inner {
  max-width: 350px;
  padding: 15px;
  color: #fff;
  background-color: rgba(33, 37, 41, 0.95);
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  font-family: 'Segoe UI', Roboto, Arial, sans-serif;
  line-height: 1.5;
}

/* Tooltip arrow styling */
.tooltip .arrow::before {
  border-top-color: rgba(33, 37, 41, 0.95);
}

/* Feedback history styling */
.tooltip-inner span {
  display: block;
  margin-bottom: 20px;
  font-size: 1.1rem;
  color: white;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  padding-bottom: 5px;
}

.tooltip-inner ul {
  margin: 0;
  padding: 0;
}

.tooltip-inner li {
  margin-bottom: 8px;
  padding-left: 5px;
  border-left: 3px solid #007bff;
  list-style-type: none;
}

.tooltip-inner .text-muted {
  font-size: 0.75rem;
  color: #adb5bd !important;
  margin-left: 5px;
}

/* Pulse animation for the icon */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
  }
}

</style>
     <style>
      /* Apply dark text color to all text elements */
      body, p, h1, h2, h3, h4, h5, h6, input, select, textarea, 
    table, th, td, label, .card-title, .form-control {
        color: #343a40 !important; /* Bootstrap's text-dark color */
    }
    
    /* Fix Select2 to match Bootstrap form-control */
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: #6e707e;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem);
    }
    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #d1d3e2;
    }
    /* DataTable processing indicator */
    div.dataTables_processing {
        z-index: 1;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    /* Spinner animation */
    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
        vertical-align: text-bottom;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
    }
    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }    
    
    /* Custom responsive styling for Harga Satuan Select2 only */
    .satuan-select + .select2-container {
        width: 100% !important;
    }

    @media (min-width: 576px) {
        .satuan-select + .select2-container {
            width: 25% !important;
        }
    }

    /* Network Disconnection Overlay Styles */
    #network-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        backdrop-filter: blur(5px);
    }

    .network-content {
        background-color: #fff;
        border-radius: 10px;
        padding: 30px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .network-content::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #4e73df, #36b9cc, #1cc88a);
        animation: gradient-animation 3s ease infinite;
        background-size: 200% 200%;
    }

    @keyframes gradient-animation {
        0% {background-position: 0% 50%}
        50% {background-position: 100% 50%}
        100% {background-position: 0% 50%}
    }

    .network-title {
        color: #e74a3b !important;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .network-icon {
        font-size: 50px;
        color: #e74a3b;
        margin-bottom: 20px;
    }

    .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    .retry-btn {
        background-color: #4e73df;
        color: white !important;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 15px;
    }

    .retry-btn:hover {
        background-color: #2e59d9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .network-status {
        font-size: 14px;
        margin-top: 10px;
        color: #858796 !important;
    }

    body,
    .daterangepicker {
        font-family: "Poppins" !important;
    }

    /* Page Transition Splashscreen Styles */
    #page-transition-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #fff;
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        opacity: 1;
        visibility: visible;
    }

    .page-transition-content {
        text-align: center;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .page-transition-spinner {
        position: relative;
        width: 150px;
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .spinner-ring {
        position: absolute;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid transparent;
        border-top-color: #4e73df;
        animation: spin 1.5s linear infinite;
        top: 0;
        left: 0;
    }

    .spinner-ring:nth-child(2) {
        width: 130px;
        height: 130px;
        border-top-color: #36b9cc;
        animation-duration: 1.75s;
        animation-direction: reverse;
        top: 10px;
        left: 10px;
    }

    .spinner-ring:nth-child(3) {
        width: 110px;
        height: 110px;
        border-top-color: #1cc88a;
        animation-duration: 2s;
        top: 20px;
        left: 20px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .logo-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: logo-pulse 2s ease-in-out infinite;
    }

    .page-transition-progress {
        width: 200px;
        height: 4px;
        background-color: #e2e8f0;
        border-radius: 2px;
        margin: 20px auto 0;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #4e73df, #36b9cc, #1cc88a);
        background-size: 200% 200%;
        animation: progress-animation 2s ease-out forwards, gradient-animation 3s ease infinite;
    }

    @keyframes progress-animation {
        0% { width: 0%; }
        100% { width: 100%; }
    }

    .page-transition-text {
        margin-top: 15px;
        font-size: 16px;
        font-weight: 500;
        color: #4e73df;
        opacity: 0;
        animation: fade-in 0.5s ease-out 0.5s forwards;
    }

    @keyframes fade-in {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .page-transition-dots {
        display: inline-block;
    }

    .page-transition-dots span {
        display: inline-block;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background-color: #4e73df;
        margin: 0 2px;
        opacity: 0;
    }

    .page-transition-dots span:nth-child(1) {
        animation: dot-animation 1.5s infinite 0.2s;
    }

    .page-transition-dots span:nth-child(2) {
        animation: dot-animation 1.5s infinite 0.4s;
    }

    .page-transition-dots span:nth-child(3) {
        animation: dot-animation 1.5s infinite 0.6s;
    }

    @keyframes dot-animation {
        0% { opacity: 0; transform: translateY(0); }
        50% { opacity: 1; transform: translateY(-5px); }
        100% { opacity: 0; transform: translateY(0); }
    }

    @keyframes logo-pulse {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.05); }
        100% { transform: translate(-50%, -50%) scale(1); }
    }
    
    /* Background gradient and particles */
    .page-gradient-bg {
        background: linear-gradient(135deg, #dbeafe 0%, #f8fafc 100%);
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    /* Particles container and styling */
    .particles-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
        pointer-events: none;
    }

    .particle {
          opacity: 0.1;
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        animation: float 15s infinite ease-in-out;
    }

    .particle:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 100px;
        right: 10px;
        animation-delay: 0s;
    }

    .particle:nth-child(2) {
        width: 60px;
        height: 60px;
        top: 110px;
        right: 100px;
        animation-delay: 2s;
    }

    .particle:nth-child(3) {
        width: 120px;
        height: 120px;
        top: 50px;
        left: 100px;
        animation-delay: 4s;
    }

    .particle:nth-child(4) {
        width: 50px;
        height: 50px;
        top: 50px;
        left: 200px;
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

    /* Modify splashscreen to have transparent background */
    #page-transition-overlay {
        background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
        backdrop-filter: blur(5px); /* Add blur effect to see content behind */
    }

    .page-transition-content {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 30px;
        border-radius: 10px;
    }
    
    /* Content wrapper background */
    #content {
        background: linear-gradient(135deg, #dbeafe 0%, #f8fafc 100%) !important;
        position: relative;
    }
    </style>
</head>
<body id="page-top" data-is-admin="{{ auth()->user()->isAdmin() ? 'true' : 'false' }}">
    <!-- Page Transition Splash
    Page Transition Splashscreen -->
    <div id="page-transition-overlay">
        <div class="page-transition-content">
            <div class="page-transition-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="logo-pulse">
                    <i class="fas fa-university" style="font-size: 40px; color: #4e73df;"></i>
                </div>
            </div>
            <div class="page-transition-progress">
                <div class="progress-bar"></div>
            </div>
            <div class="page-transition-text">
                Memuat Halaman
                <div class="page-transition-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Disconnection Overlay -->
    <div id="network-overlay">
        <div class="network-content">
            <div class="network-icon pulse">
                <i class="fas fa-wifi"></i>
            </div>
            <h4 class="network-title">Koneksi Internet Terputus</h4>
            <p>Sistem mendeteksi koneksi internet Anda terputus. Aplikasi akan kembali berfungsi saat koneksi internet tersambung kembali.</p>
            
            <button class="retry-btn" id="retry-connection">
                <i class="fas fa-sync-alt"></i> Periksa Koneksi
            </button>
            
            <div class="network-status" id="network-status">Menunggu koneksi internet...</div>
        </div>
    </div>



    <div id="wrapper">
        @include('partials.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                        <!-- Particles container -->
            <div class="particles-container">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
                @include('partials.topbar')
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            @include('partials.footer')
        </div>
    </div>

    <!-- Include the reusable modal component -->
    @include('components.modal')

    <!-- Load remaining scripts in the correct order -->
    <script src="{{ asset('sb-admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('sb-admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('sb-admin/js/sb-admin-2.min.js') }}"></script>
    <script src="{{ asset('sb-admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sb-admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Page level plugins -->
    <script src="{{ asset('sb-admin/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('sb-admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('sb-admin/js/demo/chart-pie-demo.js') }}"></script>
    
    <!-- Select2 JS (after jQuery) -->
    <script src="{{ asset('custom/select2.js') }}"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Di bagian scripts setelah jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-treegrid/0.2.0/js/jquery.treegrid.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // Add this to the scripts section of your main.blade.php file
// $(document).ready(function() {
//     // Initialize tooltips
//     $('[data-toggle="tooltip"]').tooltip();
    
//     // Re-initialize tooltips after AJAX content is loaded
//     $(document).ajaxComplete(function() {
//         $('[data-toggle="tooltip"]').tooltip();
//     });
// });
$(document).ajaxComplete(function() {
    // Only reinitialize tooltips if we're on the pilars page
    if (typeof initTooltips === 'function') {
        setTimeout(function() {
            initTooltips();
        }, 100);
    }
});

  // Page Transition Splashscreen Script
(function() {
    const pageTransitionOverlay = document.getElementById('page-transition-overlay');
    
    // Function to hide the splashscreen
    function hideSplashscreen() {
        pageTransitionOverlay.style.opacity = '0';
        setTimeout(() => {
            pageTransitionOverlay.style.visibility = 'hidden';
        }, 500); // Match the transition duration
    }
    
    // Hide splashscreen after page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(hideSplashscreen, 1000); // Add a small delay for better UX
    });
    
    // Show splashscreen on page navigation, but only for sidebar navigation links
    document.addEventListener('click', function(e) {
        // Check if the clicked element is a sidebar link
        const link = e.target.closest('#accordionSidebar a');
        
        if (link && 
            link.href && 
            !link.href.startsWith('#') && 
            !link.href.includes('javascript:') && 
            !link.target && 
            !link.classList.contains('no-transition') && 
            link.hostname === window.location.hostname) {
            
            // Skip parent menu items that have submenu (they have collapse class or data-toggle="collapse")
            if (link.classList.contains('collapse-item') || 
                (!link.classList.contains('nav-link') || !link.hasAttribute('data-toggle'))) {
                
                // Show the splashscreen only for actual navigation links
                pageTransitionOverlay.style.visibility = 'visible';
                pageTransitionOverlay.style.opacity = '1';
                
                // Update the loading text to show the destination
                const pageTitle = link.getAttribute('data-page-title') || link.textContent.trim() || 'halaman berikutnya';
                document.querySelector('.page-transition-text').innerHTML = 
                    'Memuat ' + pageTitle + 
                    '<div class="page-transition-dots"><span></span><span></span><span></span></div>';
                
                // Reset the progress bar animation
                const progressBar = document.querySelector('.progress-bar');
                progressBar.style.animation = 'none';
                progressBar.offsetHeight; // Trigger reflow
                progressBar.style.animation = 'progress-animation 2s ease-out forwards, gradient-animation 3s ease infinite';
            }
        }
    });
    
    // Handle browser back/forward navigation
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Page was loaded from cache (back/forward navigation)
            hideSplashscreen();
        }
    });
})();


    $(document).ready(function() {
        // Setup AJAX CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize Select2 for page elements
        initPageSelect2();
        
        // Handle modal loading
        $(document).on('click', '.load-modal', function(e) {
            e.preventDefault();
               e.stopPropagation();
              $('.tooltip').remove();
            var url = $(this).data('url');
            var title = $(this).data('title');
            
            $('#mainModalLabel').text(title);
            $('#mainModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#mainModal').modal('show');
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#mainModal .modal-body').html(response);
                    initModalSelect2();
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    
                    // Show error with SweetAlert2
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load content: ' + (xhr.responseJSON?.message || 'Unknown error'),
                    });
                    
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
                }
            });
        });
        
        // Global AJAX error handler
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            if (jqxhr.status === 419) { // CSRF token mismatch
                Swal.fire({
                    icon: 'warning',
                    title: 'Session Expired',
                    text: 'Your session has expired. Please refresh the page.',
                    confirmButtonText: 'Refresh Page',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });
        
        // Re-initialize select2 after modal is hidden
        $('#mainModal').on('hidden.bs.modal', function () {
            initPageSelect2();
        });
         initTooltips();
    });

    // Function to initialize tooltips properly
        function initTooltips() {
            // First destroy any existing tooltips to prevent duplicates
            $('[data-toggle="tooltip"]').tooltip('dispose');
            
            // Then reinitialize with proper settings
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                container: 'body',
                animation: false,
                boundary: 'viewport',
                placement: 'auto'
            });
        }

    
    // Initialize Select2 for page elements (outside modal)
    function initPageSelect2() {
    $('.select2-filter').each(function() {
        // Skip if it's inside a modal
        if ($(this).closest('.modal').length === 0) {
            // Get placeholder text from the empty option if it exists
            var placeholderText = "-- Pilih --"; // Default placeholder
            $(this).find('option[value=""]').each(function() {
                placeholderText = $(this).text();
            });
            
            // Destroy if already initialized
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            // Store the element for later reference
            var $select = $(this);
            
            // Initialize with basic settings and custom template
            $(this).select2({
                width: '100%',
                placeholder: placeholderText,
                allowClear: true,
                templateSelection: function(data) {
                    if (data.id && data.id !== '') {
                        // Just return the data text - tooltip will be handled separately
                        return $('<span style="color:#000000">' + data.text + '</span>');
                    }
                    return data.text;
                }
            });
            
            // Apply custom styling to all elements
            var $container = $(this).next('.select2-container');
            
            // Style the selection container
            $container.find('.select2-selection--single').css({
                'height': '34px',
                'padding': '0.15rem 0.75rem',
                'border': '1px solid #d1d3e2',
                'border-radius': '0.35rem',
                'position': 'relative'  // For absolute positioning of tooltip
            });
            
            // Style the rendered text
            $container.find('.select2-selection__rendered').css({
                'line-height': '1.5',
                'padding-left': '0',
                'padding-top': '0.15rem',
                'padding-bottom': '0.15rem',
                'color': '#6e707e'
            });
            
            // Style the dropdown arrow
            $container.find('.select2-selection__arrow').css({
                'height': '34px'
            });
            
            // Add CSS for the permanent tooltip
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    .select2-container {
                        position: relative;
                    }
                    .permanent-tooltip {
                        position: absolute;
                        top: -8px;
                        left: 8px;
                        background-color: white;
                        color: #4e73df;
                        font-size: 0.7rem;
                        padding: 0 5px;
                        border-radius: 3px;
                        z-index: 1;
                        font-weight: normal;
                        line-height: 1.2;
                        pointer-events: none;
                    }
                    .select2-value {
                        font-weight: bold;
                        color: #5a5c69;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        display: inline-block;
                        width: 100%;
                    }
                `)
                .appendTo('head');
            
            // Function to update the tooltip
            function updateTooltip() {
                // Remove any existing tooltip
                $container.find('.permanent-tooltip').remove();
                
                // Only add tooltip if a value is selected
                if ($select.val() && $select.val() !== '') {
                    // Extract label from placeholder text
                    var label = '';
                    if (placeholderText) {
                        // Remove dashes and trim
                        var cleanText = placeholderText.replace(/--/g, '').trim();
                        // Remove "pilih " if it exists (case insensitive)
                        var labelText = cleanText.replace(/^pilih\s+/i, '').trim();
                        // Use the result as label
                        label = labelText;
                    }
                    
                    // Create and append the permanent tooltip
                    if (label) {
                        $('<div class="permanent-tooltip">' + label + '</div>').appendTo($container);
                    }
                }
            }
            
            // Update tooltip on initialization
            updateTooltip();
            
            // Update tooltip when selection changes
            $select.on('change', updateTooltip);
            
            // Namespace the event handler to avoid affecting modals
            var selectId = $(this).attr('id') || 'select-' + Math.random().toString(36).substring(2, 15);
            $(this).attr('id', selectId);
            
            // Remove any previous event handlers
            $(document).off('select2:open.' + selectId);
            
            // Add namespaced event handler
            $(document).on('select2:open.' + selectId, function() {
                // Only target dropdowns that are not in modals
                $('.select2-dropdown').each(function() {
                    if ($(this).closest('.modal').length === 0) {
                        $(this).css({
                            'font-size': '0.875rem'
                        });
                        
                        $(this).find('.select2-search__field').css({
                            'height': '28px',
                            'padding': '2px 6px'
                        });
                        
                        $(this).find('.select2-results__option').css({
                            'padding': '4px 8px',
                            'min-height': '28px'
                        });
                    }
                });
            });
        }
    });
}






    
    // Initialize Select2 for modal elements
    function initModalSelect2() {
        $('.modal .select2').each(function() {
            // Destroy if already initialized
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            
            // Initialize with proper parent
            $('select.select2:not(.normal)').each(function () {
            $(this).select2({
                dropdownParent: $('#mainModal .modal-body'),
                width: '100%',
            });
         });
            
        });
    }
    
    // Global function to show toast notifications
    function showToast(type, message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        Toast.fire({
            icon: type, // 'success', 'error', 'warning', 'info', 'question'
            title: message
        });
    }
    </script>
    
    @stack('scripts')
    
    <!-- Network Connection Monitor Script -->
    <script>
    // Network connection monitoring
    (function() {
        const overlay = document.getElementById('network-overlay');
        const retryBtn = document.getElementById('retry-connection');
        const networkStatusEl = document.getElementById('network-status');
        
        // Variables to track connection state
        let isOverlayShown = false;
        let connectionCheckerInterval = null;
        
        // Initialize connection monitoring
        initConnectionMonitoring();
        
        // Set up retry button
        retryBtn.addEventListener('click', function() {
            networkStatusEl.textContent = "Memeriksa koneksi internet...";
            checkInternetConnection();
        });
        
        function initConnectionMonitoring() {
            // Check connection immediately on page load
            if (!navigator.onLine) {
                showOverlay();
            }
            
            // Listen for online/offline events
            window.addEventListener('online', function() {
                networkStatusEl.textContent = "Koneksi internet tersambung kembali.";
                // Verify the connection is really back with a ping test
                pingServer().then(isConnected => {
                    if (isConnected) {
                        hideOverlay();
                        // Add a small toast notification
                        showToast('success', 'Koneksi internet tersambung kembali');
                    } else {
                        networkStatusEl.textContent = "Browser melaporkan online, tetapi tidak dapat terhubung ke server.";
                    }
                });
            });
            
            window.addEventListener('offline', function() {
                showOverlay();
                networkStatusEl.textContent = "Koneksi internet terputus.";
            });
            
            // Monitor AJAX errors as potential network issues
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                // If we get network-related errors, check the connection
                           // If we get network-related errors, check the connection
                           if (jqxhr.status === 0 || jqxhr.status === 502 || jqxhr.status === 503 || jqxhr.status === 504) {
                    checkInternetConnection();
                }
            });
        }
        
        function checkInternetConnection() {
            // First check if browser reports as online
            if (!navigator.onLine) {
                showOverlay();
                networkStatusEl.textContent = "Browser melaporkan tidak ada koneksi internet.";
                return;
            }
            
            // Verify with a ping test
            pingServer().then(isConnected => {
                if (!isConnected) {
                    showOverlay();
                    networkStatusEl.textContent = "Tidak dapat terhubung ke server meskipun browser melaporkan online.";
                } else {
                    hideOverlay();
                }
            });
        }
        
        function pingServer() {
            return new Promise((resolve) => {
                // Try to load a small image from a reliable server
                const img = new Image();
                const timeout = setTimeout(() => {
                    resolve(false);
                }, 5000);
                
                img.onload = function() {
                    clearTimeout(timeout);
                    resolve(true);
                };
                
                img.onerror = function() {
                    clearTimeout(timeout);
                    resolve(false);
                };
                
                // Use a reliable CDN or service (Google's favicon in this case)
                img.src = "https://www.google.com/favicon.ico?_=" + Date.now();
            });
        }
        
        function showOverlay() {
            if (!isOverlayShown) {
                overlay.style.display = "flex";
                document.body.style.overflow = "hidden"; // Prevent scrolling
                isOverlayShown = true;
                
                // Start periodic checking when overlay is shown
                if (!connectionCheckerInterval) {
                    connectionCheckerInterval = setInterval(function() {
                        if (navigator.onLine) {
                            pingServer().then(isConnected => {
                                if (isConnected) {
                                    hideOverlay();
                                    showToast('success', 'Koneksi internet tersambung kembali');
                                }
                            });
                        }
                    }, 5000); // Check every 5 seconds
                }
            }
        }
        
        function hideOverlay() {
            if (isOverlayShown) {
                overlay.style.display = "none";
                document.body.style.overflow = ""; // Restore scrolling
                isOverlayShown = false;
                
                // Stop periodic checking when overlay is hidden
                if (connectionCheckerInterval) {
                    clearInterval(connectionCheckerInterval);
                    connectionCheckerInterval = null;
                }
            }
        }
        
        // Add a visual indicator for current network status in the navbar
        const addNetworkIndicator = () => {
            // Use the dedicated check-internet div instead of inserting before user dropdown
            const checkInternetDiv = document.querySelector('.check-internet');
            if (checkInternetDiv) {
                // Clear any existing content
                checkInternetDiv.innerHTML = `
                    <div style="margin-top:13px">
                        <a class="nav-link" href="#" id="networkIndicator" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-wifi fa-fw mr-1 text-success" style="font-size:12px"></i>
                            </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="networkIndicator">
                            <div class="dropdown-header">Status Jaringan</div>
                            <div class="dropdown-item">
                                <div class="small" id="connection-status-detail">Koneksi internet tersedia</div>
                            </div>
                            <div class="dropdown-item">
                                <button class="btn btn-sm btn-primary btn-block" onclick="checkInternetConnection()">
                                    <i class="fas fa-sync-alt fa-sm"></i> Periksa Koneksi
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                // Make the checkInternetConnection function globally accessible
                window.checkInternetConnection = checkInternetConnection;
                
                // Update indicator based on current status
                updateNetworkIndicator(navigator.onLine);
                
                // Listen for online/offline events to update the indicator
                window.addEventListener('online', function() {
                    updateNetworkIndicator(true);
                });
                
                window.addEventListener('offline', function() {
                    updateNetworkIndicator(false);
                });
            }
        };
        
        // Update the network indicator in the navbar
        function updateNetworkIndicator(isOnline) {
            const statusBadge = document.getElementById('network-indicator-status');
            const statusDetail = document.getElementById('connection-status-detail');
            
            if (statusBadge && statusDetail) {
                if (isOnline) {
                    statusBadge.className = "badge badge-success badge-counter";
                    statusBadge.textContent = "Online";
                    statusDetail.textContent = "Koneksi internet tersedia";
                } else {
                    statusBadge.className = "badge badge-danger badge-counter";
                    statusBadge.textContent = "Offline";
                    statusDetail.textContent = "Tidak ada koneksi internet";
                }
            }
        }
        
        // Add the network indicator after the page loads
        document.addEventListener('DOMContentLoaded', addNetworkIndicator);
        
        // Initial check - show overlay if offline at startup
        if (!navigator.onLine) {
            showOverlay();
            networkStatusEl.textContent = "Tidak ada koneksi internet saat memuat halaman.";
        }
    })();
    </script>

    <!-- AJAX Page Loading Enhancement -->
    <script>
  // Enhance navigation with AJAX for smoother transitions
(function() {
    // Only apply AJAX navigation to sidebar links
    $(document).on('click', '#accordionSidebar a:not(.no-ajax):not([href^="#"]):not([href^="javascript"]):not([href^="mailto"]):not([href^="tel"]):not([target="_blank"])', function(e) {
        // Only intercept links to the same domain
        if (this.hostname === window.location.hostname) {
            const $link = $(this);
            const href = $link.attr('href');
            
            // Skip parent menu items that have submenu (they have collapse class or data-toggle="collapse")
            if ($link.hasClass('nav-link') && $link.attr('data-toggle') === 'collapse') {
                return; // Let the default behavior handle this (expand/collapse)
            }
            
            // For sidebar menu items with submenu, don't show splashscreen
            if ($link.closest('.nav-item').find('.collapse').length > 0 && !$link.hasClass('collapse-item')) {
                return; // This is a parent menu item with submenu
            }
            
            const pageTitle = $link.data('page-title') || $link.text().trim() || 'Halaman';
            
            // Show the splashscreen with the appropriate title
            const pageTransitionOverlay = document.getElementById('page-transition-overlay');
            pageTransitionOverlay.style.visibility = 'visible';
            pageTransitionOverlay.style.opacity = '1';
            
            document.querySelector('.page-transition-text').innerHTML = 
                'Memuat ' + pageTitle + 
                '<div class="page-transition-dots"><span></span><span></span><span></span></div>';
            
            // Reset the progress bar animation
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.animation = 'none';
            progressBar.offsetHeight; // Trigger reflow
            progressBar.style.animation = 'progress-animation 2s ease-out forwards, gradient-animation 3s ease infinite';
            
            // Update browser history
            window.history.pushState({path: href}, '', href);
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            e.preventDefault();
            
            // Load the new page content
            setTimeout(() => {
                window.location.href = href;
            }, 500);
        }
    });
    
    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.path) {
            // Show the splashscreen
            const pageTransitionOverlay = document.getElementById('page-transition-overlay');
            pageTransitionOverlay.style.visibility = 'visible';
            pageTransitionOverlay.style.opacity = '1';
            
            document.querySelector('.page-transition-text').innerHTML = 
                'Kembali ke halaman sebelumnya' + 
                '<div class="page-transition-dots"><span></span><span></span><span></span></div>';
            
            // Reset the progress bar animation
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.animation = 'none';
            progressBar.offsetHeight; // Trigger reflow
            progressBar.style.animation = 'progress-animation 2s ease-out forwards, gradient-animation 3s ease infinite';
            
            // Load the page
            setTimeout(() => {
                window.location.href = event.state.path;
            }, 500);
        }
    });
    
    // Add class to all parent menu items to help identify them
    $(document).ready(function() {
        // Mark all sidebar menu items that have submenus
        $('.sidebar .nav-item').each(function() {
            if ($(this).find('.collapse').length > 0) {
                $(this).find('> .nav-link').addClass('has-submenu');
            }
        });
    });
})();

    </script>
   
   <script>
    // ... kode yang sudah ada sebelumnya ...

    // Enhanced Professional Toast Notification System
    function showProfessionalToast(type, title, message, duration = 5000) {
        // Create toast container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Toast configuration with enhanced settings
        const config = {
            success: {
                title: title || 'Berhasil!',
                class: 'toast-success',
                sound: 'success' // Optional: for sound effects
            },
            error: {
                title: title || 'Error!',
                class: 'toast-error',
                sound: 'error'
            },
            warning: {
                title: title || 'Peringatan!',
                class: 'toast-warning',
                sound: 'warning'
            },
            info: {
                title: title || 'Informasi',
                class: 'toast-info',
                sound: 'info'
            },
            loading: {
                title: title || 'Memuat...',
                class: 'toast-info',
                sound: null
            }
        };

        const toastConfig = config[type] || config.info;

        // Create unique ID for this toast
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

        // Create toast element with enhanced structure
        const toast = document.createElement('div');
        toast.className = `custom-toast ${toastConfig.class}`;
        toast.id = toastId;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="toast-header">
                <div class="toast-icon ${type === 'loading' ? 'loading' : ''}"></div>
                <div class="toast-title">${toastConfig.title}</div>
                <button type="button" class="toast-close" aria-label="Close" data-toast-id="${toastId}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="toast-body">${message}</div>
            ${type !== 'loading' ? '<div class="toast-progress"></div>' : ''}
        `;

        // Add to container with stacking effect
        container.appendChild(toast);

        // Trigger reflow for animation
        toast.offsetHeight;

        // Show toast with animation
        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        // Auto hide after duration (except for loading toasts)
        let hideTimer;
        if (type !== 'loading' && duration > 0) {
            hideTimer = setTimeout(() => {
                hideToast(toast);
            }, duration);
        }

        // Close button functionality
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (hideTimer) clearTimeout(hideTimer);
            hideToast(toast);
        });

        // Pause progress on hover
        toast.addEventListener('mouseenter', () => {
            const progressBar = toast.querySelector('.toast-progress');
            if (progressBar) {
                progressBar.style.animationPlayState = 'paused';
            }
            if (hideTimer) {
                clearTimeout(hideTimer);
            }
        });

        // Resume progress on mouse leave
        toast.addEventListener('mouseleave', () => {
            const progressBar = toast.querySelector('.toast-progress');
            if (progressBar) {
                progressBar.style.animationPlayState = 'running';
            }
            if (type !== 'loading' && duration > 0) {
                hideTimer = setTimeout(() => {
                    hideToast(toast);
                }, 2000); // Shorter duration after hover
            }
        });

        // Keyboard accessibility
        toast.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (hideTimer) clearTimeout(hideTimer);
                hideToast(toast);
            }
        });

        // Optional: Add sound effect (you can implement this with Web Audio API)
        if (toastConfig.sound && typeof playNotificationSound === 'function') {
            playNotificationSound(toastConfig.sound);
        }

        // Return toast element for external control
        return {
            element: toast,
            id: toastId,
            hide: () => hideToast(toast),
            updateMessage: (newMessage) => {
                const body = toast.querySelector('.toast-body');
                if (body) body.innerHTML = newMessage;
            },
            updateTitle: (newTitle) => {
                const titleEl = toast.querySelector('.toast-title');
                if (titleEl) titleEl.textContent = newTitle;
            }
        };
    }

    function hideToast(toast) {
        if (!toast || !toast.parentNode) return;
        
        toast.classList.remove('show');
        toast.classList.add('hide');
        
        // Remove from DOM after animation
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
            
            // Clean up container if empty
            const container = document.querySelector('.toast-container');
            if (container && container.children.length === 0) {
                container.remove();
            }
        }, 400);
    }

    // Enhanced showAlert function with better type mapping
    function showAlert(type, message, title = null, duration = 5000) {
        const typeMap = {
            'success': 'success',
            'danger': 'error',
            'error': 'error',
            'warning': 'warning',
            'info': 'info',
            'loading': 'loading'
        };

        const toastType = typeMap[type] || 'info';
        return showProfessionalToast(toastType, title, message, duration);
    }

    // Utility functions for common toast patterns
    function showSuccessToast(message, title = 'Berhasil!', duration = 4000) {
        return showProfessionalToast('success', title, message, duration);
    }

    function showErrorToast(message, title = 'Error!', duration = 6000) {
        return showProfessionalToast('error', title, message, duration);
    }

    function showWarningToast(message, title = 'Peringatan!', duration = 5000) {
        return showProfessionalToast('warning', title, message, duration);
    }

    function showInfoToast(message, title = 'Informasi', duration = 4000) {
        return showProfessionalToast('info', title, message, duration);
    }

    function showLoadingToast(message, title = 'Memuat...') {
        return showProfessionalToast('loading', title, message, 0);
    }

    // Global function to hide all toasts
    function hideAllToasts() {
        const toasts = document.querySelectorAll('.custom-toast');
        toasts.forEach(toast => hideToast(toast));
    }

    // Optional: Sound notification function (implement if needed)
    function playNotificationSound(type) {
        // You can implement Web Audio API here for sound effects
        // Example:
        // const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        // ... sound implementation
    }

    // ... sisa kode yang sudah ada ...
</script>


</body>
</html>
