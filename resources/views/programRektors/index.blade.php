@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Program Rektor Management</h1>
<p class="mb-4">Manage all Program Rektor in the system.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Program Rektor List</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <select id="programPengembanganFilter" class="form-control select2-filter">
                    <option value="">-- Pilih Program Pengembangan --</option>
                    @foreach($programPengembangans as $programPengembangan)
                        <option value="{{ $programPengembangan->ProgramPengembanganID }}" {{ isset($selectedProgramPengembangan) && $selectedProgramPengembangan == $programPengembangan->ProgramPengembanganID ? 'selected' : '' }}>
                            {{ $programPengembangan->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mr-2">
                <select id="indikatorKinerjaFilter" class="form-control select2-filter">
                    <option value="">-- Pilih Indikator Kinerja --</option>
                    @foreach($indikatorKinerjas as $indikatorKinerja)
                        <option value="{{ $indikatorKinerja->IndikatorKinerjaID }}" {{ isset($selectedIndikatorKinerja) && $selectedIndikatorKinerja == $indikatorKinerja->IndikatorKinerjaID ? 'selected' : '' }}>
                            {{ $indikatorKinerja->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('program-rektors.create') }}" data-title="Tambah Program Rektor">
                    <i class="fas fa-plus fa-sm"></i> Tambah Program Rektor
                </button>
                <a href="{{ route('program-rektors.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel fa-sm"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="programRektorTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Program Pengembangan</th>
                        <th style="white-space:nowrap">Indikator Kinerja</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">Jenis Kegiatan</th>
                        <th style="white-space:nowrap">Mata Anggaran</th>
                        <th style="white-space:nowrap">Jumlah Kegiatan</th>
                        <th style="white-space:nowrap">Satuan</th>
                        <th style="white-space:nowrap">Harga Satuan</th>
                        <th style="white-space:nowrap">Total</th>
                        <th style="white-space:nowrap">Penanggung Jawab</th>
                        <th style="white-space:nowrap">Pelaksana</th>
                        <th style="white-space:nowrap">Status</th>
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
@endsection

@push('scripts')
<script>
    var programRektorTable;
    var isFiltering = false;
    
    $(document).ready(function () {
        // Initialize DataTable with AJAX source
        initDataTable();
        
        // Handle filter change for Program Pengembangan
        $('#programPengembanganFilter').on('change', function() {
            var programPengembanganID = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('programPengembanganID', programPengembanganID);
            
            // Reload DataTable with new filter
            programRektorTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
            });
        });
        
        // Handle filter change for Indikator Kinerja
        $('#indikatorKinerjaFilter').on('change', function() {
            var indikatorKinerjaID = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('indikatorKinerjaID', indikatorKinerjaID);
            
            // Reload DataTable with new filter
            programRektorTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
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
                        programRektorTable.ajax.reload();
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
        $(document).on('click', '.delete-program-rektor', function(e) {
            e.preventDefault();
            var programId = $(this).data('id');
            var deleteUrl = "{{ route('program-rektors.destroy', ':id') }}".replace(':id', programId);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
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
                                programRektorTable.ajax.reload();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete program rektor');
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
    
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#programRektorTable')) {
            $('#programRektorTable').DataTable().destroy();
        }
        
        // Initialize DataTable with AJAX
        programRektorTable = $('#programRektorTable').DataTable({
            processing: true,
            serverSide: false, // We're handling the data ourselves
            ajax: {
                url: '{{ route('program-rektors.index') }}',
                type: 'GET',
                data: function(d) {
                    d.programPengembanganID = $('#programPengembanganFilter').val();
                    d.indikatorKinerjaID = $('#indikatorKinerjaFilter').val();
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#programRektorTable_processing').hide();
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
                { data: 'program_pengembangan' },
                { data: 'indikator_kinerja' },
                { data: 'nama' },
                { data: 'jenis_kegiatan' },
                { 
                    data: 'mata_anggaran',
                    orderable: false,
                    render: function(data) {
                        return data;
                    }
                },
                { 
                    data: 'jumlah_kegiatan', 
                    className: 'text-center',
                    render: function(data) {
                        return '<span style="white-space:nowrap">' + data + '</span>';
                    }
                },
                { data: 'satuan' },
                { 
                    data: 'harga_satuan', 
                    className: 'text-center',
                    render: function(data) {
                        return '<span style="white-space:nowrap">' + data + '</span>';
                    }
                },
                { 
                    data: 'total', 
                    className: 'text-center',
                    render: function(data) {
                        return '<span style="white-space:nowrap">' + data + '</span>';
                    }
                },
                { data: 'penanggung_jawab' },
                { 
                    data: 'pelaksana',
                    orderable: false,
                    render: function(data) {
                        return data;
                    }
                },
                { 
                    data: 'na', 
                    className: 'text-center text-dark',
                    width: '1px',
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
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
                    $('#programRektorTable_processing').hide();
                }
                return true;
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#programRektorTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#programRektorTable_processing').hide();
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
        
        // Re-initialize delete confirmation for dynamically added buttons
        $('.delete-program-rektor').off('click').on('click', function(e) {
            e.preventDefault();
            var programId = $(this).data('id');
            var deleteUrl = "{{ route('program-rektors.destroy', ':id') }}".replace(':id', programId);
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
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
                                programRektorTable.ajax.reload();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete program rektor');
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
            $('#programRektorTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#programRektorTable_processing').hide();
            }, 200);
        }
    });
</script>
@endpush

