@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Pilar</h1>
<p class="mb-4">Kelola Master Pilar.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pilar List</h6>
        @if(auth()->user()->isAdmin())
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('pilars.create') }}" data-title="Tambah Pilar">
                <i class="fas fa-plus fa-sm"></i> Tambah Pilar
            </button>
        </div>
        @endif
    </div>
    <div class="card-body">
        <!-- Move filter here and make it full width -->
        <div class="form-group mb-5">
            <select id="renstraFilter" class="form-control select2-filter">
                <option value="">-- Pilih Renstra --</option>
                @foreach($renstras as $renstra)
                    <option value="{{ $renstra->RenstraID }}" {{ isset($selectedRenstra) && $selectedRenstra == $renstra->RenstraID ? 'selected' : '' }}>
                        {{ $renstra->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="pilarTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">NA</th>
                        @if(auth()->user()->isAdmin())
                        <th style="white-space:nowrap">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var pilarTable;
    var isFiltering = false;
    var selectedRenstraId = getCookie('selected_renstra') || "{{ $selectedRenstra ?? '' }}";
    
    $(document).ready(function () {
        // Initialize Select2 for the filter
        
        // Set the filter value from cookie if available
        if (selectedRenstraId) {
            $('#renstraFilter').val(selectedRenstraId).trigger('change');
        }
        
        // Initialize DataTable with AJAX source
        initDataTable();
        
        // Handle filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Store selected Renstra ID in global variable
            selectedRenstraId = renstraID;
            
            // Store in cookie for persistence
            setCookie('selected_renstra', renstraID, 30); // Store for 30 days
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('renstraID', renstraID);
            
            // Reload DataTable with new filter
            pilarTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
            });
        });
        
        @if(auth()->user()->isAdmin())
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
                beforeSend: function() {
                    // Disable submit button and show loading indicator with smaller spinner
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 1rem; height: 1rem; border-width: 0.15em;"></span> <small>Processing...</small>');
                },
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#mainModal').modal('hide');
                        
                        // Show success message
                        showAlert('success', response.message || 'Operation completed successfully');
                        
                        // Reload DataTable
                        pilarTable.ajax.reload();
                    } else {
                        // Display error message
                        showAlert('danger', response.message || 'An error occurred');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    var errors = xhr.responseJSON?.errors;
                    var errorMessage = '';
                    
                    if (errors) {
                        for (var field in errors) {
                            errorMessage += errors[field][0] + '<br>';
                        }
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else {
                        errorMessage = 'An error occurred';
                    }
                    
                    showAlert('danger', errorMessage);
                },
                complete: function() {
                    // Re-enable submit button
                    form.find('button[type="submit"]').prop('disabled', false).html('Save');
                }
            });
        });
        
        // Handle delete button click
        $(document).on('click', '.delete-pilar', function(e) {
            e.preventDefault();
            var pilarId = $(this).data('id');
            var deleteUrl = "{{ route('pilars.destroy', ':id') }}".replace(':id', pilarId);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Menghapus data?',
                text: "Kamu yakin menghapus baris ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX delete
                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                           
                                // Reload DataTable
                                pilarTable.ajax.reload();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete pilar');
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var message = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('danger', message);
                        }
                    });
                }
            });
        });
        @endif
    });
    
    // Cookie functions
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    function eraseCookie(name) {   
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
    
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#pilarTable')) {
            $('#pilarTable').DataTable().destroy();
        }
        
        // Initialize DataTable with AJAX
        pilarTable = $('#pilarTable').DataTable({
            processing: true,
            serverSide: false, // We're handling the data ourselves
            ajax: {
                url: '{{ route('pilars.index') }}',
                type: 'GET',
                data: function(d) {
                    d.renstraID = selectedRenstraId; // Use the global variable
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#pilarTable_processing').hide();
                    }
                }
            },
            columns: [
                { 
                    data: 'no', 
                    className: 'text-center',
                    width: '1px',
                    orderable: false,
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
                },
                { data: 'nama' },
                { 
                    data: 'na', 
                    className: 'text-center',
                    width: '1px',
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
                },
                @if(auth()->user()->isAdmin())
                { 
                    data: 'actions', 
                    className: 'text-center',
                    width: '1px',
                    orderable: false,
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
                }
                @endif
            ],
            responsive: true,
            drawCallback: function() {
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
            },
            // Apply row class for inactive items
            createdRow: function(row, data, dataIndex) {
                if (data.row_class) {
                    $(row).addClass(data.row_class);
                }
            },
            // Hide processing indicator for all operations except filtering
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
            // Override the default processing display behavior
            preDrawCallback: function() {
                if (!isFiltering) {
                    $('#pilarTable_processing').hide();
                }
                return true;
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#pilarTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#pilarTable_processing').hide();
            }
        });
    }
    
    function updateUrlParameter(key, value) {
        var url = new URL(window.location.href);
        
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        
        window.history.pushState({}, '', url.toString());
    }
    
    function initEventHandlers() {
        // Re-initialize modal loading for dynamically added buttons
        $('.load-modal').off('click').on('click', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var title = $(this).data('title');
            
            // Add selected Renstra ID to the URL if it exists
            if (selectedRenstraId) {
                url = addOrUpdateQueryParam(url, 'renstraID', selectedRenstraId);
            }
            
            $('#mainModalLabel').text(title);
            $('#mainModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#mainModal').modal('show');
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#mainModal .modal-body').html(response);
                    
                    // Initialize Select2
                    if (typeof initModalSelect2 === 'function') {
                        initModalSelect2();
                    }
                    
                    // Set the Renstra field value with a more robust approach
                    if (selectedRenstraId && $('#RenstraID').length) {
                        // First destroy and reinitialize Select2 to ensure clean state
                        if ($('#RenstraID').hasClass('select2-hidden-accessible')) {
                            $('#RenstraID').select2('destroy');
                        }
                        
                        // Set the value directly on the DOM element
                        $('#RenstraID').val(selectedRenstraId);
                        
                        // Reinitialize Select2
                        $('#RenstraID').select2({
                            placeholder: "Pilih Renstra",
                            allowClear: true,
                            dropdownParent: $('#mainModal .modal-body'),
                            width: '100%'
                        });
                        
                        // Force a change event after initialization
                        setTimeout(function() {
                            $('#RenstraID').trigger('change.select2');
                        }, 200);
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
                }
            });
        });
        
        @if(auth()->user()->isAdmin())
        // Re-initialize delete confirmation for dynamically added buttons
        $('.delete-pilar').off('click').on('click', function(e) {
            e.preventDefault();
            var pilarId = $(this).data('id');
            var deleteUrl = "{{ route('pilars.destroy', ':id') }}".replace(':id', pilarId);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Menghapus data?',
                text: "Kamu yakin menghapus baris ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Iya, yakin',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX delete
                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                showAlert('success', response.message || 'Pilar berhasil dihapus');
                                
                                // Reload DataTable
                                pilarTable.ajax.reload();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete pilar');
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var message = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('danger', message);
                        }
                    });
                }
            });
        });
        @endif
    }
    
    // Function to add or update query parameter in URL
    function addOrUpdateQueryParam(url, key, value) {
        // Check if URL already has parameters
        if (url.indexOf('?') !== -1) {
            // Check if the specific parameter already exists
            var regex = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
            if (url.match(regex)) {
                return url.replace(regex, '$1' + key + '=' + value + '$2');
            } else {
                return url + '&' + key + '=' + value;
            }
        } else {
            return url + '?' + key + '=' + value;
        }
    }
    
    // Function to show alert messages
    function showAlert(type, message) {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#alertContainer').html(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Initial data load - hide processing indicator if not filtering
    $(document).ajaxStart(function() {
        if (!isFiltering) {
            $('#pilarTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#pilarTable_processing').hide();
            }, 200);
        }
    });

    // Apply the stored Renstra filter value when the page is loaded or refreshed
    $(window).on('pageshow', function(event) {
        // This event fires when the page is shown, including when navigating back to it
        if (event.originalEvent.persisted) {
            // Page was loaded from cache (e.g., back button)
            var storedRenstraId = getCookie('selected_renstra');
            if (storedRenstraId) {
                selectedRenstraId = storedRenstraId;
                
                // Set the select value and trigger change
                if ($('#renstraFilter').val() !== storedRenstraId) {
                    $('#renstraFilter').val(storedRenstraId).trigger('change');
                }
                
                // Update URL parameter
                updateUrlParameter('renstraID', storedRenstraId);
            }
        }
    });
</script>
@endpush
