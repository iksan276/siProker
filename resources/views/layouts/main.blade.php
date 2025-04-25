<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Admin Panel' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Load jQuery first -->
    <script src="{{ asset('sb-admin/vendor/jquery/jquery.min.js') }}"></script>
    
    <!-- Then load other CSS and JS dependencies -->
    <link href="{{ asset('sb-admin/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sb-admin/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sb-admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="{{ asset('custom/select2.css') }}" rel="stylesheet" />
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Tambahkan TreeGrid CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-treegrid/0.2.0/css/jquery.treegrid.min.css">
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

    #programPengembanganFilter + .select2-container,
    #programRektorFilter + .select2-container,
    #unitFilter + .select2-container,
    #indikatorKinerjaFilter + .select2-container {
        max-width: 200px !important;
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
    </style>
</head>
<body id="page-top">
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

    <script>
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
    });
    
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
                
                // Initialize with basic settings
                $(this).select2({
                    width: '100%',
                    placeholder: placeholderText,
                    allowClear: true
                });
                
                // Apply custom styling to all elements
                var $container = $(this).next('.select2-container');
                
                // Style the selection container
                $container.find('.select2-selection--single').css({
                    'height': '34px',
                    'padding': '0.15rem 0.75rem',
                    'border': '1px solid #d1d3e2',
                    'border-radius': '0.35rem'
                });
                
                // Style the rendered text and center the placeholder
                $container.find('.select2-selection__rendered').css({
                    'line-height': '1.5',
                    'padding-left': '0',
                    'padding-top': '0.15rem',
                    'padding-bottom': '0.15rem',
                    'color': '#6e707e',
                    'text-align': 'center' // Center the text
                });
                
                // Style the dropdown arrow
                $container.find('.select2-selection__arrow').css({
                    'height': '34px'
                });
                
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
            const navbar = document.querySelector('.navbar-nav');
            if (navbar) {
                const indicator = document.createElement('li');
                indicator.className = 'nav-item dropdown no-arrow mx-1';
                indicator.innerHTML = `
                    <a class="nav-link dropdown-toggle" href="#" id="networkIndicator" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-wifi fa-fw"></i>
                        <span id="network-indicator-status" class="badge badge-success badge-counter">Online</span>
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
                `;
                navbar.appendChild(indicator);
                
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
</body>
</html>
