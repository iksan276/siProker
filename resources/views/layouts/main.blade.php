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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
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
                    console.error('AJAX Error:', xhr); // Add this line for debugging
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
                }
            });
        });
        
        // Handle form submission within modal
        $(document).on('submit', '.modal-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method');
            var data = form.serialize();
            
            $.ajax({
                url: url,
                type: method,
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#mainModal').modal('hide');
                        // Reload the page or update the table
                        window.location.reload();
                    } else {
                        // Display error message
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    
                    for (var field in errors) {
                        errorMessage += errors[field][0] + '\n';
                    }
                    
                    alert(errorMessage || 'An error occurred');
                }
            });
        });
        
        // Handle delete confirmation
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            if (confirm('Are you sure you want to delete this item?')) {
                form.submit();
            }
        });
        
        // Re-initialize select2 after modal is hidden
        $('#mainModal').on('hidden.bs.modal', function () {
            initPageSelect2();
        });
    });
    
    // Initialize Select2 for page elements (outside modal)
 // Initialize Select2 for page elements (outside modal)
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
    </script>
    
    @stack('scripts')
</body>
</html>
