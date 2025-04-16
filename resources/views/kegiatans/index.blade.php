@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Kegiatan Management</h1>
<p class="mb-4">Manage all Kegiatan in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kegiatan List</h6>
        <div class="d-flex align-items-center">
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
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('kegiatans.create') }}" data-title="Tambah Kegiatan">
                <i class="fas fa-plus fa-sm"></i> Tambah Kegiatan
            </button>
            <a href="{{ route('kegiatans.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="kegiatanTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Indikator Kinerja</th>
                        <th>Nama</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Rincian Kegiatan</th>
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
    var kegiatanTable;
    var isFiltering = false;
    
    $(document).ready(function () {
        // Initialize DataTable with AJAX source
        initDataTable();
        
        // Handle filter change
        $('#indikatorKinerjaFilter').on('change', function() {
            var indikatorKinerjaID = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('indikatorKinerjaID', indikatorKinerjaID);
            
            // Reload DataTable with new filter
            kegiatanTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
            });
        });
    });
    
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#kegiatanTable')) {
            $('#kegiatanTable').DataTable().destroy();
        }
        
        // Initialize DataTable with AJAX
        kegiatanTable = $('#kegiatanTable').DataTable({
            processing: true,
            serverSide: false, // We're handling the data ourselves
            ajax: {
                url: '{{ route('kegiatans.index') }}',
                type: 'GET',
                data: function(d) {
                    d.indikatorKinerjaID = $('#indikatorKinerjaFilter').val();
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#kegiatanTable_processing').hide();
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
                { data: 'indikator_kinerja' },
                { data: 'nama' },
                { data: 'tanggal_mulai', className: 'text-center' },
                { data: 'tanggal_selesai', className: 'text-center' },
                { data: 'rincian_kegiatan' },
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
            // Override the default processing display behavior
            preDrawCallback: function() {
                if (!isFiltering) {
                    $('#kegiatanTable_processing').hide();
                }
                return true;
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#kegiatanTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#kegiatanTable_processing').hide();
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
            $('#kegiatanTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#kegiatanTable_processing').hide();
            }, 200);
        }
    });
</script>
@endpush
