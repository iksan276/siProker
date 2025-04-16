@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Indikator Kinerja Management</h1>
<p class="mb-4">Manage all Indikator Kinerja in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Indikator Kinerja List</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <select id="programRektorFilter" class="form-control select2-filter">
                    <option value="">-- Pilih Program Rektor --</option>
                    @foreach($programRektors as $programRektor)
                        <option value="{{ $programRektor->ProgramRektorID }}" {{ isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID ? 'selected' : '' }}>
                            {{ $programRektor->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mr-2">
                <select id="unitFilter" name="unitIDs[]" class="form-control select2-filter select2-multiple" multiple>
                    <option value="">-- Pilih Unit Terkait --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->UnitID }}" {{ isset($selectedUnitIDs) && in_array($unit->UnitID, $selectedUnitIDs) ? 'selected' : '' }}>
                            {{ $unit->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('indikator-kinerjas.create') }}" data-title="Tambah Indikator Kinerja">
                    <i class="fas fa-plus fa-sm"></i> Tambah Indikator Kinerja
                </button>
                <a href="{{ route('indikator-kinerjas.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel fa-sm"></i> Export Excel
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="indikatorKinerjaTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Program Rektor</th>
                        <th>Bobot</th>
                        <th>Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Meta Anggaran</th>
                        <th>Unit Terkait</th>
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
    var indikatorKinerjaTable;
    var isFiltering = false;
    
    $(document).ready(function () {
        // Initialize DataTable with AJAX source
        initDataTable();
        
        // Handle Program Rektor filter change
        $('#programRektorFilter').on('change', function() {
            var programRektorID = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('programRektorID', programRektorID);
            
            // Preserve the unit filter if it exists
            var unitIDs = $('#unitFilter').val();
            if (unitIDs && unitIDs.length > 0) {
                for (var i = 0; i < unitIDs.length; i++) {
                    updateUrlParameter('unitIDs[]', unitIDs[i]);
                }
            }
            
            // Reload DataTable with new filter
            indikatorKinerjaTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
            });
        });
        
        // Handle Unit filter change
        $('#unitFilter').on('change', function() {
            var unitIDs = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            var url = new URL(window.location.href);
            
            // Remove all existing unitIDs parameters
            var params = url.searchParams;
            params.delete('unitIDs[]');
            
            // Add new unitIDs parameters if any are selected
            if (unitIDs && unitIDs.length > 0) {
                for (var i = 0; i < unitIDs.length; i++) {
                    params.append('unitIDs[]', unitIDs[i]);
                }
            }
            
            // Preserve the program rektor filter if it exists
            var programRektorID = $('#programRektorFilter').val();
            if (programRektorID) {
                params.set('programRektorID', programRektorID);
            }
            
            // Update URL
            window.history.pushState({}, '', url.toString());
            
            // Reload DataTable with new filter
            indikatorKinerjaTable.ajax.reload(function() {
                // Reset filtering flag after data is loaded
                isFiltering = false;
            });
        });
    });
    
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
                    d.programRektorID = $('#programRektorFilter').val();
                    d.unitIDs = $('#unitFilter').val();
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#indikatorKinerjaTable_processing').hide();
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
                { data: 'program_rektor' },
                { data: 'bobot', className: 'text-center' },
                { data: 'satuan', className: 'text-center', width: '1px' },
                { data: 'harga_satuan', className: 'text-center' },
                { data: 'jumlah', className: 'text-center' },
                { data: 'meta_anggaran' },
                { data: 'unit_terkait' },
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
    
    // Additional styling for multiple selects
    $('.select2-multiple').next('.select2-container').find('.select2-selection--multiple').css({
        'border': '1px solid #d1d3e2',
        'border-radius': '0.35rem',
        'min-height': '34px'
    });
</script>
@endpush
