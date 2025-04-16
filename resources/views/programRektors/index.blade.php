@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Program Rektor Management</h1>
<p class="mb-4">Manage all Program Rektor in the system.</p>

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
                        <th>No</th>
                        <th>Nama</th>
                        <th>Program Pengembangan</th>
                        <th>Tahun</th>
                        <th>NA</th>
                        <th>Actions</th>
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
        
        // Handle filter change
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
                { data: 'nama' },
                { data: 'program_pengembangan' },
                { 
                    data: 'tahun', 
                    className: 'text-center',
                    width: '1px',
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
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
        $('.delete-confirm').off('click').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            if (confirm('Are you sure you want to delete this item?')) {
                form.submit();
            }
        });
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
