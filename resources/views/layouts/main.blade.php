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
    
    /* Default style for extra small screens (xs) */
        .responsive-element {
        width: 100%;
        }

        /* For screens larger than extra small */
        @media (min-width: 576px) {
        .responsive-element {
            width: 25%;
        }
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



    </style>
</head>
<body id="page-top">
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
            $(this).select2({
                dropdownParent: $('#mainModal'),
                width: '100%',
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
</body>
</html>
