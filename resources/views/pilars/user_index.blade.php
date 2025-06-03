@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Pilar</h1>
<p class="mb-4">Kelola pilar.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- Color Legend Card -->
<div class="card shadow mb-3">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
    </div>
    <div class="card-body">
         <div class="row mb-2">
            <div class="col-md-4">
                <div class="form-group">
                    <select id="renstraFilter" class="form-control select2-filter">
                        <option value="">-- Pilih Renstra --</option>
                        @foreach($renstras as $renstra)
                            <option value="{{ $renstra->RenstraID }}" {{ isset($selectedRenstra) && $selectedRenstra == $renstra->RenstraID ? 'selected' : '' }}>
                                {{ $renstra->Nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select id="treeLevelFilter" class="form-control select2-filter">
                        <option value="">-- Pilih Level Tree --</option>
                        <option value="pilar" {{ isset($selectedTreeLevel) && $selectedTreeLevel == 'pilar' ? 'selected' : '' }}>Pilar</option>
                        <option value="isu" {{ isset($selectedTreeLevel) && $selectedTreeLevel == 'isu' ? 'selected' : '' }}>Isu Strategis</option>
                        <option value="program" {{ isset($selectedTreeLevel) && $selectedTreeLevel == 'program' ? 'selected' : '' }}>Program Pengembangan</option>
                        <option value="rektor" {{ isset($selectedTreeLevel) && $selectedTreeLevel == 'rektor' ? 'selected' : '' }}>Program Rektor</option>
                        <option value="kegiatan" {{ isset($selectedTreeLevel) && $selectedTreeLevel == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <div class="input-group">
                        <input type="text" id="searchFilter" class="form-control" placeholder="Cari berdasarkan level tree">
                      
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Filter - Only visible when tree level is 'kegiatan' -->
        <div id="statusFilterContainer" class="row mb-3" style="display: none;">
            <div class="col-md-6">
                <div class="form-group">
                    <select id="statusFilter" class="form-control select2-filter">
                        <option value="">Pilih Status Kegiatan</option>
                        <option value="N" {{ isset($selectedStatus) && $selectedStatus == 'N' ? 'selected' : '' }}>Menunggu</option>
                        <option value="P" {{ isset($selectedStatus) && $selectedStatus == 'P' ? 'selected' : '' }}>Pengajuan</option>
                        <option value="Y" {{ isset($selectedStatus) && $selectedStatus == 'Y' ? 'selected' : '' }}>Disetujui</option>
                        <option value="T" {{ isset($selectedStatus) && $selectedStatus == 'T' ? 'selected' : '' }}>Ditolak</option>
                        <option value="R" {{ isset($selectedStatus) && $selectedStatus == 'R' ? 'selected' : '' }}>Revisi</option>
                        <option value="PT" {{ isset($selectedStatus) && $selectedStatus == 'PT' ? 'selected' : '' }}>Pengajuan TOR</option>
                        <option value="YT" {{ isset($selectedStatus) && $selectedStatus == 'YT' ? 'selected' : '' }}>TOR Disetujui</option>
                        <option value="TT" {{ isset($selectedStatus) && $selectedStatus == 'TT' ? 'selected' : '' }}>TOR Ditolak</option>
                        <option value="RT" {{ isset($selectedStatus) && $selectedStatus == 'RT' ? 'selected' : '' }}>TOR Revisi</option>
                        <option value="TP" {{ isset($selectedStatus) && $selectedStatus == 'RT' ? 'selected' : '' }}>Tunda Pencairan</option>
                    </select>
                </div>
            </div>
              <div class="col-md-6">
                <div class="form-group">
                    <select id="kegiatanFilter" class="form-control select2-filter">
                        <option value="">Pilih Kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                                            <option value="{{ $kegiatan->KegiatanID }}" {{ isset($selectedKegiatan) && $selectedKegiatan == $kegiatan->KegiatanID ? 'selected' : '' }}>
                                {{ $kegiatan->Nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
    </div>
</div>



<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Struktur Pilar</h6>
    </div>
    <div class="card-body">
         <div class="d-flex flex-wrap justify-content-between mb-3">
            <!-- Warna yang sudah ada -->
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(231, 74, 59, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Pilar</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(246, 194, 62, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Isu Strategis</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(28, 200, 138, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Program Pengembangan</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(10, 63, 223, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Program Rektor</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(156, 39, 176, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Kegiatan</span>
                </div>
            </div>
            <!-- Tambahan Warna Baru -->
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(255, 140, 0, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">Sub Kegiatan</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(0, 0, 0, 0.1); height: 30px; width: 30px;"></div>
                    <span class="ml-2">RAB</span>
                </div>
            </div>
        </div>
      
        <div id="tree-grid-container" class="table-responsive">
            <table id="tree-grid" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- TreeGrid data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="{{ asset('vendor/treegrid/css/jquery.treegrid.css') }}" rel="stylesheet">
<style>
    /* Column styling */
    #tree-grid th {
        text-align: center;
    }
    
    #tree-grid td:first-child {
        text-align: center;
        white-space: nowrap;
        width: 1px;
    }
    
    /* Tree grid icon styling */
    .tree-grid-icon {
        margin-right: 5px;
    }
    
    /* Level-based indentation and styling */
    tr[data-level="1"] td:nth-child(2) {
        padding-left: 30px;
    }
    
    tr[data-level="2"] td:nth-child(2) {
        padding-left: 60px;
    }
    
    tr[data-level="3"] td:nth-child(2) {
        padding-left: 90px;
    }
    tr[data-level="4"] td:nth-child(2) {
        padding-left: 120px;
    }
    
    tr[data-level="5"] td:nth-child(2) {
        padding-left: 150px;
    }
    
    /* Hover effect */
    #tree-grid tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.1); /* light-primary color */
    }
    
    /* Different background colors for different node types */
    tr.node-pilar {
        background-color: rgba(231, 74, 59, 0.1); /* Light red for Pilar */
    }
    
    tr.node-isu {
        background-color: rgba(246, 194, 62, 0.1); /* Light yellow for Isu Strategis */
    }
    
    tr.node-program {
        background-color: rgba(28, 200, 138, 0.1); /* Light green for Program Pengembangan */
    }
    
    tr.node-rektor {
        background-color: rgba(78, 115, 223, 0.1); /* Light blue for Program Rektor */
    }
    
    tr.node-indikator {
        background-color: rgba(54, 185, 204, 0.1); /* Light info color for Indikator Kinerja */
    }
    
    tr.node-kegiatan {
        background-color: rgba(156, 39, 176, 0.1); /* Light purple for Kegiatan */
    }
    
    /* Custom expand/collapse icons */
    .expander {
        cursor: pointer;
        margin-right: 5px;
    }
    
    /* Tooltip styling */
    .tooltip-inner {
        max-width: 300px;
        padding: 8px;
        background-color: #333;
        font-size: 14px;
    }
    
    /* Node name styling for tooltip trigger */
    .node-name {
        cursor: pointer;
    }

    /* Hover effect - updated with stronger specificity */
#tree-grid tbody tr {
    transition: all 0.2s ease;
}

/* New styles for dashed indentation */
.tree-indent {
    position: relative;
    display: inline-block;
    width: 20px;
    height: 1px;
}

.tree-indent::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 0;
    height: 1px;
    background-color: #6c757d;
    animation: dashGrow 1.5s ease-in-out infinite;
}

.tree-indent:nth-child(1)::before { animation-delay: 0.1s; }
.tree-indent:nth-child(2)::before { animation-delay: 0.2s; }
.tree-indent:nth-child(3)::before { animation-delay: 0.3s; }
.tree-indent:nth-child(4)::before { animation-delay: 0.4s; }
.tree-indent:nth-child(5)::before { animation-delay: 0.5s; }

@keyframes dashGrow {
    0%, 100% { width: 0; opacity: 0.3; }
    50% { width: 15px; opacity: 1; }
}

/* Color gradient for indentation dashes */
tr[data-level="0"] .tree-indent::before {
    background-color: #0d47a1; /* Darkest blue */
}
tr[data-level="1"] .tree-indent::before {
    background-color: #1976d2; /* Dark blue */
}
tr[data-level="2"] .tree-indent::before {
    background-color: #2196f3; /* Medium blue */
}
tr[data-level="3"] .tree-indent::before {
    background-color: #64b5f6; /* Light blue */
}
tr[data-level="4"] .tree-indent::before {
    background-color: #90caf9; /* Lighter blue */
}
tr[data-level="5"] .tree-indent::before {
    background-color: #bbdefb; /* Lightest blue */
}

/* Ensure all tooltips are visible */
.tooltip {
    z-index: 9999;
}

/* Highlight search results */
.search-highlight {
    background-color: #ffff00;
    padding: 2px;
    border-radius: 3px;
}

/* No results message */
.no-results {
    padding: 20px;
    text-align: center;
    font-style: italic;
    color: #6c757d;
}
</style>
@endpush


@push('scripts')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    // Add this to your document ready function in the scripts section
// Replace the existing mouseenter and mouseleave event handlers with this code
$(document).on('mouseenter', '#tree-grid tbody tr', function() {
    // Store the original background color before changing it
    var originalBgColor = $(this).css('background-color');
    $(this).data('original-bg-color', originalBgColor);
    
    // Apply a subtle highlight effect instead of completely changing the background
    $(this).css('filter', 'brightness(1.1)');
    $(this).css('cursor', 'pointer');
});

$(document).on('mouseleave', '#tree-grid tbody tr', function() {
    // Remove the highlight effect
    $(this).css('filter', 'none');
});

    // Store expanded state
    var expandedNodes = JSON.parse(localStorage.getItem('expandedNodes') || '{}');
    var nodeRelationships = {}; // To store parent-child relationships
    var nodeLevels = {}; // To store node levels
    var nodeTypes = {}; // To store node types
    var nodeHierarchy = {}; // To store complete hierarchy information
    var activeAccordion = null;
    var previousTreeLevel = null; // Store the previous tree level
    var allTreeData = []; // Store all tree data for searching
    var currentSearchTerm = ''; // Store current search term
    var levelHierarchy = {
        'pilar': 0,
        'isu': 1,
        'program': 2,
        'rektor': 3,
        'kegiatan': 4,
        'subkegiatan': 5,
        'rab': 6
    }; // Define level hierarchy for comparison
    
    $(document).ready(function() {
        $('#tree-grid th').addClass('text-dark');

        // Store initial tree level
        previousTreeLevel = $('#treeLevelFilter').val();
        
        // Show/hide status filter based on tree level
        toggleStatusFilter();
        
        loadTreeData();
        
        // Handle Renstra filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Update URL without page refresh
                   // Update URL without page refresh
                    updateUrlParameter('renstraID', renstraID);
            
            // Reset search
            $('#searchFilter').val('');
            currentSearchTerm = '';
            
            // Reload tree data
               // Reload tree data
            loadTreeData();
        });
        // Handle Tree Level filter change
        $('#treeLevelFilter').on('change', function() {
            var newTreeLevel = $(this).val();
            var previousLevel = previousTreeLevel;
            
            // Update URL without page refresh
            updateUrlParameter('treeLevel', newTreeLevel);
            
            // Store the current tree level for next change
            previousTreeLevel = newTreeLevel;
            
            // Toggle status filter visibility
            toggleStatusFilter();
            
            // Reset search
            $('#searchFilter').val('');
            currentSearchTerm = '';
            
            // Reload tree data with special handling for level changes
            loadTreeData(previousLevel, newTreeLevel);
        });
        
        // Handle status filter change
        $('#statusFilter').on('change', function() {
            var status = $(this).val();
            
            // Update URL without page refresh
            updateUrlParameter('status', status);
            
            // Reload tree data
            loadTreeData();
        });
        
        $('#kegiatanFilter').on('change', function() {
            var kegiatanID = $(this).val();
            
            // Update URL without page refresh
            updateUrlParameter('kegiatanID', kegiatanID);
            
            // Reset search
            $('#searchFilter').val('');
            
            // Reload tree data
            loadTreeData();
        });
        // Handle search input (on Enter key press)
        $('#searchFilter').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });
        
        // Handle search button click
        $('#searchButton').on('click', function() {
            performSearch();
        });
        
        // Handle reset search button click
        $('#resetSearchButton').on('click', function() {
            $('#searchFilter').val('');
            currentSearchTerm = '';
            resetSearch();
        });
        
        // Handle tooltip for node names - using direct event binding
        $(document).on('mouseenter', '.node-name', function() {
            $(this).tooltip('show');
        });
        
        $(document).on('mouseleave', '.node-name', function() {
            $(this).tooltip('hide');
        });
        
        // Handle tooltip for expander icons - using direct event binding
        $(document).on('mouseenter', '.node-expander i', function() {
            $(this).tooltip('show');
        });
        
        $(document).on('mouseleave', '.node-expander i', function() {
            $(this).tooltip('hide');
        });
        
        // Ensure tooltips are destroyed when elements are clicked
        $(document).on('click', '.node-expander', function() {
            // Hide any tooltips that might be visible
            $('.tooltip').remove();
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
                    // Disable submit button and show loading indicator
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 1rem; height: 1rem; border-width: 0.15em;"></span> Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#mainModal').modal('hide');
                        
                        // Show success message
                        showAlert('success', response.message || 'Operation completed successfully');
                        
                        // Reload tree data
                        loadTreeData();
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
        
        // Handle delete kegiatan button click
        $(document).on('click', '.delete-kegiatan', function(e) {
            e.preventDefault();
            var kegiatanId = $(this).data('id');
            var deleteUrl = "{{ route('kegiatans.destroy', ':id') }}".replace(':id', kegiatanId);
            
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
                               
                                // Reload tree data
                                loadTreeData();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete kegiatan');
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

        // Add this to the document ready function in the scripts section
        // Handle ajukan kegiatan button click
        $(document).on('click', '.ajukan-kegiatan', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var kegiatanId = $(this).data('id');
            var updateUrl = "{{ url('api/kegiatan') }}/" + kegiatanId + "/update-status";
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Ajukan Kegiatan?',
                text: "Apakah Anda yakin ingin mengajukan kegiatan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX update
                    $.ajax({
                        url: updateUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            status:"P",
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message || 'Kegiatan berhasil diajukan.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
                                
                                // Reload tree data to update the UI
                                loadTreeData();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Gagal mengajukan kegiatan');
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var message = 'Terjadi kesalahan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('danger', message);
                        }
                    });
                }
            });
        });

          $(document).on('click', '.ajukan-tor-kegiatan', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var kegiatanId = $(this).data('id');
            var updateUrl = "{{ url('api/kegiatan') }}/" + kegiatanId + "/update-status";
            
            // Show confirmation dialog
            Swal.fire({
                title: 'Ajukan TOR Kegiatan?',
                text: "Apakah Anda yakin ingin mengajukan TOR untuk kegiatan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ajukan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX update
                    $.ajax({
                        url: updateUrl,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: "PT",
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message || 'Kegiatan berhasil diajukan.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
                                
                                // Reload tree data to update the UI
                                loadTreeData();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Gagal mengajukan kegiatan');
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var message = 'Terjadi kesalahan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('danger', message);
                        }
                    });
                }
            });
        });
        

            // Handle delete sub-kegiatan button click
            $(document).on('click', '.delete-sub-kegiatan', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var subKegiatanId = $(this).data('id');
            var deleteUrl = "{{ route('sub-kegiatans.destroy', ':id') }}".replace(':id', subKegiatanId);
            
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
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
                                
                                // Reload tree data
                                loadTreeData();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete sub kegiatan');
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
        
        // Handle delete RAB button click
        $(document).on('click', '.delete-rab', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var rabId = $(this).data('id');
            var deleteUrl = "{{ route('rabs.destroy', ':id') }}".replace(':id', rabId);
            
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
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
                                
                                // Reload tree data
                                loadTreeData();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete RAB');
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
        
        // Handle tree node expansion/collapse
        $(document).on('click', '.node-expander', function(e) {
            e.stopPropagation();
            
            // Hide any tooltips that might be visible
            $('.tooltip').remove();
            
            var nodeId = $(this).closest('tr').data('node-id');
            var isExpanded = $(this).hasClass('expanded');
            var level = $(this).closest('tr').data('level');
            var nodeType = nodeTypes[nodeId] || '';
            
            if (isExpanded) {
                // Collapse this node
                collapseNode(nodeId);
                $(this).removeClass('expanded');
                
                // Update icon with appropriate tooltip based on node type
                var expandTooltip = getExpandTooltip(nodeType);
                $(this).html('<i class="fas fa-chevron-right text-primary" data-toggle="tooltip" title="' + expandTooltip + '"></i>');
                
                // Remove from expanded nodes
                delete expandedNodes[nodeId];
                
                // Reset UI state for all descendants
                resetDescendantUIState(nodeId);
            } else {
                // Collapse all other nodes at the same level with the same parent
                var parentId = nodeRelationships[nodeId];
                
                // Find all nodes at the same level with the same parent
                $('tr[data-level="' + level + '"]').each(function() {
                    var otherNodeId = $(this).data('node-id');
                    var otherParentId = nodeRelationships[otherNodeId];
                    var otherNodeType = nodeTypes[otherNodeId] || '';
                    
                    // If it's a different node but has the same parent (or both are top-level)
                    if (otherNodeId !== nodeId && otherParentId === parentId) {
                        // Find the expander for this node
                        var $otherExpander = $(this).find('.node-expander');
                        
                        // If it's expanded, collapse it
                        if ($otherExpander.hasClass('expanded')) {
                            collapseNodeAndAllChildren(otherNodeId);
                            $otherExpander.removeClass('expanded');
                            
                            // Update icon with appropriate tooltip
                            var expandTooltip = getExpandTooltip(otherNodeType);
                            $otherExpander.html('<i class="fas fa-chevron-right text-primary" data-toggle="tooltip" title="' + expandTooltip + '"></i>');
                            
                            // Remove from expanded nodes
                            delete expandedNodes[otherNodeId];
                            
                            // Reset UI state for all descendants
                            resetDescendantUIState(otherNodeId);
                        }
                    }
                });
                
                // Expand this node
                expandNode(nodeId);
                $(this).addClass('expanded');
                
                // Update icon with appropriate tooltip based on node type
                var collapseTooltip = getCollapseTooltip(nodeType);
                $(this).html('<i class="fas fa-chevron-down text-primary" data-toggle="tooltip" title="' + collapseTooltip + '"></i>');
                
                // Add to expanded nodes
                expandedNodes[nodeId] = true;
            }
            
            // Initialize tooltips for newly added elements
            initTooltips();
            
            // Save expanded state to localStorage
            localStorage.setItem('expandedNodes', JSON.stringify(expandedNodes));
        });
        
        // Handle row click to toggle expansion
        $(document).on('click', '#tree-grid tbody tr', function(e) {
            // Only if the click was not on a button or other interactive element
            if (!$(e.target).closest('button, a, input, select').length) {
                $(this).find('.node-expander').trigger('click');
            }
        });
    });
    
    // Function to toggle status filter visibility based on tree level
    function toggleStatusFilter() {
        var treeLevel = $('#treeLevelFilter').val();
        
        if (treeLevel === 'kegiatan') {
            $('#statusFilterContainer').slideDown();
            
            // Check if there's a status parameter in the URL
            var urlParams = new URLSearchParams(window.location.search);
            var statusParam = urlParams.get('status');
            var kegiatanParam = urlParams.get('kegiatanID');
            
            if (statusParam) {
                $('#statusFilter').val(statusParam);
            }
             if (kegiatanParam) {
            $('#kegiatanFilter').val(kegiatanParam);
        }
        } else {
            $('#statusFilterContainer').slideUp();
            $('#statusFilter').val(''); // Reset status filter
            $('#kegiatanFilter').val('');
        }
    }
    
    // Function to perform search
    function performSearch() {
        var searchTerm = $('#searchFilter').val().trim().toLowerCase();
        currentSearchTerm = searchTerm;
        
        if (searchTerm === '') {
            resetSearch();
            return;
        }
        
        // Update URL parameter
        updateUrlParameter('search', searchTerm);
        
        // First, remove any existing highlights
        $('.search-highlight').each(function() {
            var text = $(this).text();
            $(this).replaceWith(text);
        });
        
        // Hide all rows initially
        $('#tree-grid tbody tr').hide();
        
        // Get the current tree level filter
        var currentTreeLevel = $('#treeLevelFilter').val();
        
        // Keep track of matched nodes and their ancestors
        var matchedNodes = new Set();
        var nodesToShow = new Set();
        
        // Search through all rows
        $('#tree-grid tbody tr').each(function() {
            var $row = $(this);
            var nodeId = $row.data('node-id');
                     var nodeText = $row.find('td:nth-child(2)').text().toLowerCase();
            var nodeType = $row.data('node-type');
            
            // Modified search logic: Search in the current tree level and show all descendants
            if (nodeType === currentTreeLevel && nodeText.includes(searchTerm)) {
                // This node matches the search
                matchedNodes.add(nodeId);
                
                // Add all ancestors to nodes to show
                if (nodeHierarchy[nodeId]) {
                    nodeHierarchy[nodeId].ancestors.forEach(function(ancestorId) {
                        nodesToShow.add(ancestorId);
                    });
                }
                
                // Add this node to nodes to show
                nodesToShow.add(nodeId);
                
                // Add all descendants to nodes to show
                if (nodeHierarchy[nodeId] && nodeHierarchy[nodeId].descendants) {
                    nodeHierarchy[nodeId].descendants.forEach(function(descendantId) {
                        nodesToShow.add(descendantId);
                    });
                }
            }
        });
        
        // If no matches found, show a message
        if (matchedNodes.size === 0) {
            $('#tree-grid tbody').append('<tr class="no-results"><td colspan="3">Tidak ada hasil yang ditemukan untuk "' + searchTerm + '"</td></tr>');
            return;
        }
        
        // Show all matched nodes, their ancestors, and their descendants
        nodesToShow.forEach(function(nodeId) {
            var $row = $('tr[data-node-id="' + nodeId + '"]');
            $row.show();
            
            // If this node is a parent, expand it
            var $expander = $row.find('.node-expander');
            if ($expander.length && !$expander.hasClass('expanded') && 
                (matchedNodes.has(nodeId) || // If this is a matched node
                 // Or if any of its descendants is a matched node
                 nodeHierarchy[nodeId] && nodeHierarchy[nodeId].descendants.some(id => matchedNodes.has(id)))) {
                
                $expander.addClass('expanded');
                
                // Update icon
                var nodeType = nodeTypes[nodeId] || '';
                var collapseTooltip = getCollapseTooltip(nodeType);
                $expander.html('<i class="fas fa-chevron-down text-primary" data-toggle="tooltip" title="' + collapseTooltip + '"></i>');
                
                // Add to expanded nodes
                expandedNodes[nodeId] = true;
            }
        });
        
        // Highlight the search term in matched nodes
        matchedNodes.forEach(function(nodeId) {
            var $row = $('tr[data-node-id="' + nodeId + '"]');
            var $nameCell = $row.find('td:nth-child(2)');
            var html = $nameCell.html();
            
            // Create a temporary div to manipulate the HTML safely
            var $temp = $('<div>').html(html);
            
            // Find all text nodes and highlight the search term
            $temp.find('*').addBack().contents().filter(function() {
                return this.nodeType === 3; // Text node
            }).each(function() {
                var text = this.nodeValue;
                var lowerText = text.toLowerCase();
                var index = lowerText.indexOf(searchTerm);
                
                if (index >= 0) {
                    var before = text.substring(0, index);
                    var match = text.substring(index, index + searchTerm.length);
                    var after = text.substring(index + searchTerm.length);
                    
                    var $newNode = $('<span>' + before + '<span class="search-highlight">' + match + '</span>' + after + '</span>');
                    $(this).replaceWith($newNode);
                }
            });
            
            // Update the cell with the highlighted content
            $nameCell.html($temp.html());
        });
        
        // Save expanded state to localStorage
        localStorage.setItem('expandedNodes', JSON.stringify(expandedNodes));
        
        // Initialize tooltips for newly visible elements
        initTooltips();
    }
    
    // Function to reset search and show all nodes according to normal expansion state
    function resetSearch() {
        // Remove search parameter from URL
        updateUrlParameter('search', null);
        
        // Remove any "no results" message
        $('.no-results').remove();
        
        // Remove any existing highlights
        $('.search-highlight').each(function() {
            var text = $(this).text();
            $(this).replaceWith(text);
        });
        
        // Reset the tree to its normal state
        $('#tree-grid tbody tr').each(function() {
            var $row = $(this);
            var nodeId = $row.data('node-id');
            var parentId = $row.data('parent');
            
            if (!parentId) {
                // This is a root node, always show it
                $row.show();
            } else if (expandedNodes[parentId]) {
                // Parent is expanded, show this node
                $row.show();
            } else {
                // Parent is collapsed, hide this node
                $row.hide();
            }
        });
        
        // Re-initialize tooltips
        initTooltips();
    }
    
    // Function to reset UI state for all descendants of a node
    function resetDescendantUIState(nodeId) {
        // Find all descendants
        findAllDescendants(nodeId).forEach(function(descendantId) {
            // Find the expander for this descendant
            var $descendantExpander = $('tr[data-node-id="' + descendantId + '"] .node-expander');
            
            // Reset the expander UI
            if ($descendantExpander.length) {
                $descendantExpander.removeClass('expanded');
                
                var nodeType = nodeTypes[descendantId] || '';
                var expandTooltip = getExpandTooltip(nodeType);
                
                $descendantExpander.html('<i class="fas fa-chevron-right text-primary" data-toggle="tooltip" title="' + expandTooltip + '"></i>');
            }
            
            // Remove from expanded nodes
            delete expandedNodes[descendantId];
        });
    }
    
    // Function to find all descendants of a node
    function findAllDescendants(nodeId) {
        var descendants = [];
        
        // Find direct children
        var directChildren = [];
        $('tr[data-parent="' + nodeId + '"]').each(function() {
            var childId = $(this).data('node-id');
            directChildren.push(childId);
            descendants.push(childId);
        });
        
        // Recursively find descendants of each child
        directChildren.forEach(function(childId) {
            descendants = descendants.concat(findAllDescendants(childId));
        });
        
        return descendants;
    }
    
    // Function to initialize tooltips properly
    function initTooltips() {
        // First destroy any existing tooltips to prevent duplicates
        $('[data-toggle="tooltip"]').tooltip('dispose');
        
        // Then reinitialize with proper settings
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover',
            container: 'body',
            animation: false
        });
    }
    
   // Function to get expand tooltip based on node type
function getExpandTooltip(nodeType) {
    switch(nodeType) {
        case 'pilar':
            return 'Lihat isu strategis';
        case 'isu':
            return 'Lihat program pengembangan';
        case 'program':
            return 'Lihat program rektor';
        case 'rektor':
            return 'Lihat kegiatan';
        case 'kegiatan':
            return 'Lihat detail kegiatan';
        default:
            return 'Lihat detail';
    }
}

// Function to get collapse tooltip based on node type
function getCollapseTooltip(nodeType) {
    switch(nodeType) {
        case 'pilar':
            return 'Tutup isu strategis';
        case 'isu':
            return 'Tutup program pengembangan';
        case 'program':
            return 'Tutup program rektor';
        case 'rektor':
            return 'Tutup kegiatan';
        case 'kegiatan':
            return 'Tutup detail kegiatan';
        default:
            return 'Tutup detail';
    }
}
    
    // Function to collapse a node and all its children recursively
    function collapseNodeAndAllChildren(nodeId) {
        // First find all direct children
        var childNodeIds = [];
        $('tr[data-parent="' + nodeId + '"]').each(function() {
            var childId = $(this).data('node-id');
            childNodeIds.push(childId);
            
            // Remove from expanded nodes
            delete expandedNodes[childId];
        });
        
        // Recursively collapse each child and its descendants
        childNodeIds.forEach(function(childId) {
            collapseNodeAndAllChildren(childId);
        });
        
        // Finally hide all direct children
        $('tr[data-parent="' + nodeId + '"]').hide();
    }
    
    // Function to build complete node hierarchy
    function buildNodeHierarchy(treeData) {
        // Reset the hierarchy
        nodeHierarchy = {};
        
        // First pass: create entries for all nodes
        treeData.forEach(function(item) {
            nodeHierarchy[item.id] = {
                id: item.id,
                type: item.type,
                level: item.level,
                parent: item.parent || null,
                children: [],
                ancestors: [],
                descendants: []
            };
        });
        
        // Second pass: populate children arrays and build ancestor chains
        treeData.forEach(function(item) {
            if (item.parent && nodeHierarchy[item.parent]) {
                // Add this node to its parent's children
                nodeHierarchy[item.parent].children.push(item.id);
                
                // Build ancestor chain
                var currentParent = item.parent;
                var ancestors = [];
                
                while (currentParent) {
                    ancestors.push(currentParent);
                    currentParent = nodeHierarchy[currentParent].parent;
                }
                
                nodeHierarchy[item.id].ancestors = ancestors;
            }
        });
        
        // Third pass: populate descendant arrays
        for (var nodeId in nodeHierarchy) {
            var node = nodeHierarchy[nodeId];
            node.descendants = getDescendants(nodeId);
        }
        
        return nodeHierarchy;
    }
    
    // Helper function to get all descendants of a node
    function getDescendants(nodeId) {
        var descendants = [];
        var node = nodeHierarchy[nodeId];
        
        if (!node) return descendants;
        
        // Add direct children
        descendants = descendants.concat(node.children);
        
        // Add children's descendants
        node.children.forEach(function(childId) {
            descendants = descendants.concat(getDescendants(childId));
        });
        
        return descendants;
    }
    
    // Function to ensure parent nodes are expanded when children are expanded
    function ensureParentNodesExpanded() {
        // Create a set of nodes that need to be expanded
        var nodesToExpand = new Set();
        
        // For each expanded node, ensure all its ancestors are also expanded
        for (var nodeId in expandedNodes) {
            if (expandedNodes[nodeId] && nodeHierarchy[nodeId]) {
                // Add all ancestors to the set of nodes to expand
                nodeHierarchy[nodeId].ancestors.forEach(function(ancestorId) {
                    nodesToExpand.add(ancestorId);
                });
            }
        }
        
        // Add all these nodes to expandedNodes
        nodesToExpand.forEach(function(nodeId) {
            expandedNodes[nodeId] = true;
        });
    }
    
    // Function to preserve expanded state when changing tree levels
    function preserveExpandedStateAcrossLevels(previousLevel, newLevel) {
        // If moving from a lower level to a higher level (e.g., from kegiatan to program)
        // we need to ensure that all parent nodes of expanded nodes remain expanded
        if (levelHierarchy[previousLevel] > levelHierarchy[newLevel]) {
            // Create a map to track which nodes should remain expanded in the new view
            var expandedNodesInNewView = {};
            
            // For each currently expanded node
            for (var nodeId in expandedNodes) {
                if (expandedNodes[nodeId] && nodeHierarchy[nodeId]) {
                    // Get the node type
                    var nodeType = nodeTypes[nodeId];
                    
                    // If this node will still be visible in the new view, keep it expanded
                    if (levelHierarchy[nodeType] <= levelHierarchy[newLevel]) {
                        expandedNodesInNewView[nodeId] = true;
                    }
                    
                    // For nodes that won't be visible, ensure their ancestors are expanded
                    if (levelHierarchy[nodeType] > levelHierarchy[newLevel]) {
                        // Find ancestors that will be visible in the new view
                        nodeHierarchy[nodeId].ancestors.forEach(function(ancestorId) {
                            var ancestorType = nodeTypes[ancestorId];
                            if (levelHierarchy[ancestorType] <= levelHierarchy[newLevel]) {
                                expandedNodesInNewView[ancestorId] = true;
                            }
                        });
                    }
                }
            }
            
            // Update the expanded nodes
            expandedNodes = expandedNodesInNewView;
            
            // Save to localStorage
            localStorage.setItem('expandedNodes', JSON.stringify(expandedNodes));
        }
    }
    
    function loadTreeData(previousLevel, newLevel) {
        var renstraID = $('#renstraFilter').val();
        var treeLevel = $('#treeLevelFilter').val();
        var searchTerm = $('#searchFilter').val().trim();
        var status = '';
        var kegiatanID = '';
        
        // Only include status filter if tree level is 'kegiatan'
        if (treeLevel === 'kegiatan') {
            status = $('#statusFilter').val();
            kegiatanID = $('#kegiatanFilter').val();
        }
         if (!kegiatanID) {
        kegiatanID = $('#kegiatanFilter').val();
    }
        // If we're changing tree levels, preserve expanded state
        if (previousLevel && newLevel && previousLevel !== newLevel) {
            preserveExpandedStateAcrossLevels(previousLevel, newLevel);
        }
        
        $.ajax({
            url: '{{ route('pilars.index') }}',
            type: 'GET',
            data: {
                renstraID: renstraID,
                treeLevel: treeLevel,
                status: status,
                kegiatanID: kegiatanID,
                format: 'tree'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#tree-grid-container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
            },
            success: function(response) {
                // Clear container
                $('#tree-grid-container').html('<table id="tree-grid" class="table table-bordered"><thead><tr><th class="text-center" style="width: 5%;">No</th><th class="text-center">Nama</th><th class="text-center" style="width: 15%;">Actions</th></tr></thead><tbody></tbody></table>');
                
                $('#tree-grid th').addClass('text-dark');
                // Add rows to the table
                var treeData = response.data || [];
                allTreeData = treeData; // Store all tree data for searching
                var tableBody = $('#tree-grid tbody');
                
                if (treeData.length === 0) {
                                       tableBody.html('<tr><td colspan="3" class="text-center">No data available</td></tr>');
                    return;
                }
                
                // Reset node relationships and levels
                nodeRelationships = {};
                nodeLevels = {};
                nodeTypes = {};
                
                // Build node map for quick lookup
                var nodeMap = {};
                treeData.forEach(function(item) {
                    nodeMap[item.id] = item;
                    
                    // Store parent-child relationships
                    if (item.parent) {
                        nodeRelationships[item.id] = item.parent;
                    }
                    
                               // Store node levels
                    nodeLevels[item.id] = item.level;
                    
                    // Store node types
                    nodeTypes[item.id] = item.type;
                });
                // Build complete hierarchy information
                nodeHierarchy = buildNodeHierarchy(treeData);
                
                // Special handling for tree level changes
                if (previousLevel && newLevel && previousLevel !== newLevel) {
                    // When changing tree levels, we need to ensure parent nodes stay expanded
                    // if their children were expanded in the previous view
                    ensureParentNodesExpanded();
                }
                
                // Process tree data to update tooltips based on node type
                treeData.forEach(function(item) {
                    // Update tooltip based on node type
                    if (item.type === 'pilar') {
                        item.tooltip = 'Ini adalah Pilar';
                    } else if (item.type === 'isu') {
                        item.tooltip = 'Ini adalah Isu Strategis';
                    } else if (item.type === 'program') {
                        item.tooltip = 'Ini adalah Program Pengembangan';
                    } else if (item.type === 'rektor') {
                        item.tooltip = 'Ini adalah Program Rektor';
                    } else if (item.type === 'indikator') {
                        item.tooltip = 'Ini adalah Indikator Kinerja';
                    } else if (item.type === 'kegiatan') {
                        item.tooltip = 'Ini adalah Kegiatan';
                    }else if (item.type === 'subkegiatan') {
                        item.tooltip = 'Ini adalah Sub Kegiatan';
                    }else if (item.type === 'rab') {
                        item.tooltip = 'Ini adalah RAB';
                    }
                });
                
                // First pass: add all rows to the table
                treeData.forEach(function(item) {
                    var row = $('<tr></tr>');
                    
                    // Set data attributes
                    row.attr('data-node-id', item.id);
                    row.attr('data-parent', item.parent || '');
                    row.attr('data-level', item.level);
                    row.attr('data-has-children', item.has_children);
                    row.attr('data-node-type', item.type); // Add node type as data attribute
                    // Add node type class for styling
                    row.addClass('node-' + item.type);
                    
                    // Apply background color directly based on node type
                    if (item.type === 'pilar') {
                        row.css('background-color', 'rgba(231, 74, 59, 0.1)'); // Light red
                    } else if (item.type === 'isu') {
                        row.css('background-color', 'rgba(246, 194, 62, 0.1)'); // Light yellow
                    } else if (item.type === 'program') {
                        row.css('background-color', 'rgba(28, 200, 138, 0.1)'); // Light green
                    } else if (item.type === 'rektor') {
                        row.css('background-color', 'rgba(10, 63, 223, 0.1)'); // Light blue
                    } else if (item.type === 'indikator') {
                        row.css('background-color', 'rgba(2, 255, 251, 0.1)'); // Light info
                    } else if (item.type === 'kegiatan') {
                        row.css('background-color', 'rgba(156, 39, 176, 0.1)'); // Light purple
                    }else if (item.type === 'subkegiatan') {
                        row.css('background-color', 'rgba(255, 140, 0, 0.1)'); // Light orange
                    }else if (item.type === 'rab') {
                        row.css('background-color', 'rgba(0, 0, 0, 0.1)'); // Light black
                    }
                    
                    // Add level class for styling
                    row.addClass('level-' + item.level);
                    
                    // Add row class if provided
                    if (item.row_class) {
                        row.addClass(item.row_class);
                    }
                    
                    // Initially hide child nodes
                    if (item.parent) {
                        row.addClass('child-node').hide();
                    }
                    
                    // Add cells with proper styling
                    row.append('<td class="text-center" style="white-space:nowrap;width:1px;">' + (item.no || '') + '</td>');
                    
                    // Add expander if has children
                    var expander = '';
                    if (item.has_children) {
                        var isExpanded = expandedNodes[item.id];
                        var tooltipText = isExpanded ? 
                            getCollapseTooltip(item.type) : 
                            getExpandTooltip(item.type);
                        
                        var expanderIcon = isExpanded ? 
                            '<i class="fas fa-chevron-down text-primary" data-toggle="tooltip" title="' + tooltipText + '"></i>' : 
                            '<i class="fas fa-chevron-right text-primary" data-toggle="tooltip" title="' + tooltipText + '"></i>';
                        
                        expander = '<span class="node-expander ' + (isExpanded ? 'expanded' : '') + '" data-node-id="' + item.id + '">' + expanderIcon + '</span>';
                    }
                    
                    // Create name cell with tooltip that shows on hover
                    var nameText = '';
                    
                    // Add visual indentation markers based on level - with color gradient
                    var indentPrefix = '';
                    for (var i = 0; i < item.level; i++) {
                        indentPrefix += '<span class="tree-indent text-primary">- - -&nbsp;</span>';
                    }
                    
                    if (item.tooltip && item.type!== 'rab' && item.type!== 'subkegiatan') {
                        nameText = indentPrefix + '<span class="node-name" data-toggle="tooltip" title="' + item.tooltip + '">' + item.nama + '</span>';
                    } else {
                        nameText = indentPrefix + item.nama;
                    }
                    
                    var nameCell = '<td>' + nameText + "&nbsp;&nbsp;" + expander + '</td>';
                    row.append(nameCell);
                    row.append('<td class="text-center" style="white-space:nowrap;width:1px;">' + (item.actions || '') + '</td>');
                    
                    tableBody.append(row);
                });
                
                // Initialize tooltips
                initTooltips();
                
                // Clean up expandedNodes to remove any that no longer exist in the tree
                for (var nodeId in expandedNodes) {
                    if (!$('tr[data-node-id="' + nodeId + '"]').length) {
                        delete expandedNodes[nodeId];
                    }
                }
                
                // Apply the expanded state to the tree
                applyExpandedState();
                
                // Save the updated expanded state
                localStorage.setItem('expandedNodes', JSON.stringify(expandedNodes));
                
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
                
                // Apply search if there's a search term
                if (searchTerm) {
                    $('#searchFilter').val(searchTerm);
                    performSearch();
                }
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
                $('#tree-grid-container').html('<div class="alert alert-danger">Error loading data: ' + (xhr.responseJSON?.message || xhr.statusText) + '</div>');
            }
        });
    }
    
    // Function to apply the expanded state to the tree
    function applyExpandedState() {
        // First, identify all nodes that need to be expanded
        var nodesToExpand = [];
        
        // Add all nodes that are marked as expanded
        for (var nodeId in expandedNodes) {
            if (expandedNodes[nodeId] && $('tr[data-node-id="' + nodeId + '"]').length) {
                nodesToExpand.push(nodeId);
            }
        }
        
        // Sort nodes by level to ensure parents are expanded before children
        nodesToExpand.sort(function(a, b) {
            return (nodeLevels[a] || 0) - (nodeLevels[b] || 0);
        });
        
        // Expand each node
        nodesToExpand.forEach(function(nodeId) {
            expandNode(nodeId);
            var nodeType = nodeTypes[nodeId] || '';
            var collapseTooltip = getCollapseTooltip(nodeType);
            
            $('tr[data-node-id="' + nodeId + '"] .node-expander')
                .addClass('expanded')
                .html('<i class="fas fa-chevron-down text-primary" data-toggle="tooltip" title="' + collapseTooltip + '"></i>');
        });
        
        // Re-initialize tooltips
        initTooltips();
    }
    
    function expandNode(nodeId) {
        // Show all direct children of this node
        $('tr[data-parent="' + nodeId + '"]').show();
        
        // Also ensure that if any of these children are expanded, their children are shown too
        $('tr[data-parent="' + nodeId + '"]').each(function() {
            var childId = $(this).data('node-id');
            if (expandedNodes[childId]) {
                expandNode(childId);
            }
        });
    }
    
    function collapseNode(nodeId) {
        // First, recursively collapse all descendants
        $('tr[data-parent="' + nodeId + '"]').each(function() {
            var childId = $(this).data('node-id');
            collapseNode(childId);
            
            // Remove from expanded nodes
            delete expandedNodes[childId];
        });
        
        // Then hide direct children
        $('tr[data-parent="' + nodeId + '"]').hide();
    }

     // Function to initialize event handlers for dynamic content
     function initEventHandlers() {
        // Handle modal loading
        $('.load-modal').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent row click event
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
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content: ' + (xhr.responseJSON?.message || xhr.statusText) + '</div>');
                }
            });
        });
        
        // Prevent event propagation for action buttons
        $(document).on('click', '#tree-grid .btn', function(e) {
            e.stopPropagation();
        });
    }
    
    // Function to update URL parameters without page refresh
    function updateUrlParameter(key, value) {
        var url = new URL(window.location.href);
        
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        
        window.history.pushState({}, '', url.toString());
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
    
    // Function to ensure all parent nodes are expanded when a child is expanded
    function ensureParentExpanded(nodeId) {
        // Get the parent of this node
        var parentId = nodeRelationships[nodeId];
        
        // If there's a parent and it exists in the DOM
        if (parentId && $('tr[data-node-id="' + parentId + '"]').length) {
            // Make sure the parent is expanded
            var $parentExpander = $('tr[data-node-id="' + parentId + '"] .node-expander');
            
            if (!$parentExpander.hasClass('expanded')) {
                // Expand the parent
                expandNode(parentId);
                $parentExpander.addClass('expanded');
                
                // Update icon with appropriate tooltip
                var parentNodeType = nodeTypes[parentId] || '';
                var collapseTooltip = getCollapseTooltip(parentNodeType);
                $parentExpander.html('<i class="fas fa-chevron-down text-primary" data-toggle="tooltip" title="' + collapseTooltip + '"></i>');
                
                // Add to expanded nodes
                expandedNodes[parentId] = true;
            }
            
            // Recursively ensure this parent's parent is also expanded
            ensureParentExpanded(parentId);
        }
    }
    
    
    // Check for search parameter in URL on page load
    $(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);
        var searchParam = urlParams.get('search');
        var kegiatanParam = urlParams.get('kegiatanID');

        if (searchParam) {
            $('#searchFilter').val(searchParam);
            // Search will be performed after tree data is loaded
        }
        if (kegiatanParam) {
        $('#kegiatanFilter').val(kegiatanParam);
    }
    });
</script>
@endpush
