@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Indikator Kinerja</h1>
<p class="mb-4">Kelola Master Indikator Kinerja.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Indikator Kinerja List</h6>
        <div class="d-flex align-items-center">
          
            <div>
               <a href="{{ route('indikator-kinerjas.export.excel') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel fa-sm"></i> Export Excel
                </a>
                <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('indikator-kinerjas.create') }}" data-title="Tambah Indikator Kinerja">
                    <i class="fas fa-plus fa-sm"></i> Tambah Indikator Kinerja
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
    <div class="form-group mb-5">
                <select id="renstraFilter" class="form-control select2-filter">
                    <option value="">-- Pilih Renstra (Untuk Label Tahun) --</option>
                    @foreach($renstras as $renstra)
                        <option value="{{ $renstra->RenstraID }}" {{ isset($selectedRenstraID) && $selectedRenstraID == $renstra->RenstraID ? 'selected' : '' }}>
                            {{ $renstra->Nama }} ({{ $renstra->PeriodeMulai }} - {{ $renstra->PeriodeSelesai }})
                        </option>
                    @endforeach
                </select>
            </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="indikatorKinerjaTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">Satuan</th>
                        <th style="white-space:nowrap">Baseline</th>
                        <th style="white-space:nowrap" id="tahun1Header">{{ $yearLabels[0] ?? '2025' }}</th>
                        <th style="white-space:nowrap" id="tahun2Header">{{ $yearLabels[1] ?? '2026' }}</th>
                        <th style="white-space:nowrap" id="tahun3Header">{{ $yearLabels[2] ?? '2027' }}</th>
                        <th style="white-space:nowrap" id="tahun4Header">{{ $yearLabels[3] ?? '2028' }}</th>
                        <th style="white-space:nowrap">Mendukung IKU</th>
                        <th style="white-space:nowrap">NA</th>
                        <th style="white-space:nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Form Template (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    var indikatorKinerjaTable;
    var isFiltering = false;
    var defaultYearLabels = ['2025', '2026', '2027', '2028'];
    // Then in the view:
    var yearLabels = @json($yearLabels ?? $defaultYearLabels);
    // Add this line to get the selected Renstra from cookie
    var selectedRenstraId = getCookie('selected_renstra') || "{{ $selectedRenstraID ?? '' }}";
    
    $(document).ready(function () {
        // Initialize DataTable with AJAX source
        initDataTable();
        
        // Update year headers based on initial yearLabels
        updateYearHeaders();

        if (selectedRenstraId) {
            $('#renstraFilter').val(selectedRenstraId).trigger('change');
        }
        
        
        // Handle Renstra filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();

            if (renstraID) {
                setCookie('selected_renstra', renstraID, 30);
            } else {
                eraseCookie('selected_renstra');
            }
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('renstraID', renstraID);
            
            // If a renstra is selected, fetch its year labels
            if (renstraID) {
                $.ajax({
                    url: "{{ route('indikator-kinerjas.renstra-years', ':id') }}".replace(':id', renstraID),
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            yearLabels = response.yearLabels;
                            updateYearHeaders();
                            
                            // Reload DataTable with new year labels
                            indikatorKinerjaTable.ajax.reload(function() {
                                // Reset filtering flag after data is loaded
                                isFiltering = false;
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching renstra years:', xhr);
                        isFiltering = false;
                    }
                });
            } else {
                // Reset to default year labels (2025-2028)
                yearLabels = ['2025', '2026', '2027', '2028'];
                updateYearHeaders();
                
                // Reload DataTable with default year labels
                indikatorKinerjaTable.ajax.reload(function() {
                    // Reset filtering flag after data is loaded
                    isFiltering = false;
                });
            }
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
                        indikatorKinerjaTable.ajax.reload();
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
        $(document).on('click', '.delete-indikator', function(e) {
            e.preventDefault();
            var indikatorId = $(this).data('id');
            var deleteUrl = "{{ route('indikator-kinerjas.destroy', ':id') }}".replace(':id', indikatorId);
            
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
                             
                                // Reload DataTable
                                indikatorKinerjaTable.ajax.reload();

                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete indikator kinerja');
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
    });
    
    function updateYearHeaders() {
        // Update table headers with year labels
        if (yearLabels.length >= 1) {
            $('#tahun1Header').text(yearLabels[0]);
        }
        if (yearLabels.length >= 2) {
            $('#tahun2Header').text(yearLabels[1]);
        }
        if (yearLabels.length >= 3) {
            $('#tahun3Header').text(yearLabels[2]);
        }
        if (yearLabels.length >= 4) {
            $('#tahun4Header').text(yearLabels[3]);
        }
    }
    
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#indikatorKinerjaTable')) {
            $('#indikatorKinerjaTable').DataTable().destroy();
        }
        
        // Initialize DataTable with AJAX
        indikatorKinerjaTable = $('#indikatorKinerjaTable').DataTable({
            processing: true,
            serverSide: false, // We're handling the data ourselves
            ajax: {
                url: '{{ route('indikator-kinerjas.index') }}',
                type: 'GET',
                data: function(d) {
                    d.renstraID = $('#renstraFilter').val();
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#indikatorKinerjaTable_processing').hide();
                    }
                },
                dataSrc: function(json) {
                    // Update year labels if provided in response
                    if (json.yearLabels) {
                        yearLabels = json.yearLabels;
                        updateYearHeaders();
                    }
                    return json.data;
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
                { data: 'satuan', className: 'text-center', width: '1px' },
                { data: 'baseline', className: 'text-center' },
                { data: 'tahun1', className: 'text-center' },
                { data: 'tahun2', className: 'text-center' },
                { data: 'tahun3', className: 'text-center' },
                { data: 'tahun4', className: 'text-center' },
                { 
                    data: 'mendukung_iku', 
                    className: 'text-center',
                    width: '1px'
                },
                { 
                    data: 'na', 
                    className: 'text-center text-dark',
                    width: '1px'
                },
                { 
                    data: 'actions', 
                    className: 'text-center',
                    width: '1px',
                    orderable: false,
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
                }
            ],
            responsive: true,
            drawCallback: function() {
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
            },
            // Hide processing indicator for all operations except filtering
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
            // Override the default processing display behavior
            preDrawCallback: function() {
                if (!isFiltering) {
                    $('#indikatorKinerjaTable_processing').hide();
                }
                return true;
            },
            // Apply row classes
            createdRow: function(row, data, dataIndex) {
                if (data.DT_RowClass) {
                    $(row).addClass(data.DT_RowClass);
                }
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#indikatorKinerjaTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#indikatorKinerjaTable_processing').hide();
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
            
            // Add current year labels to the URL as a query parameter
            var yearLabelsParam = encodeURIComponent(JSON.stringify(yearLabels));
            url += (url.includes('?') ? '&' : '?') + 'yearLabels=' + yearLabelsParam;
            
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
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
                }
            });
        });
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
            $('#indikatorKinerjaTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#indikatorKinerjaTable_processing').hide();
            }, 200);
        }
    });

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
</script>
@endpush

