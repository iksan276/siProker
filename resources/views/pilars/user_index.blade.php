@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Pilar</h1>
<p class="mb-4">Kelola pilar.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Struktur Pilar</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2">
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
    </div>
    <div class="card-body">
        <div id="tree-grid-container">
            <table id="tree-grid" class="table table-bordered">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Nama</th>
                        <th width="15%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- TreeGrid data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Main Modal -->
<div class="modal fade" id="mainModal" tabindex="-1" role="dialog" aria-labelledby="mainModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mainModalLabel">Modal Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Modal content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('vendor/treegrid/css/jquery.treegrid.css') }}" rel="stylesheet">
<style>
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
        background-color: rgba(0,0,0,.075);
    }
    
    /* Different background colors for different levels */
    tr.level-0 {
        background-color: #f8f9fa;
    }
    
    tr.level-1 {
        background-color: #f1f8ff;
    }
    
    tr.level-2 {
        background-color: #f5f5f5;
    }
    
    tr.level-3 {
        background-color: #fff8e1;
    }
    
    tr.level-4 {
        background-color: #f1f8e9;
    }
    
    tr.level-5 {
        background-color: #fce4ec;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/treegrid/js/jquery.treegrid.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    var isFiltering = false;
    
    $(document).ready(function() {
        loadTreeData();
        
        // Handle filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Update URL without page refresh
            updateUrlParameter('renstraID', renstraID);
            
            // Reload tree data
            loadTreeData();
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
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                },
                success: function(response) {
                    console.log(response);
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
                            console.log(response);
                            if (response.success) {
                                // Show success message
                                showAlert('success', response.message || 'Kegiatan berhasil dihapus');
                                
                                // Reload tree data
                                loadTreeData();
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
    });
    
    function loadTreeData() {
        var renstraID = $('#renstraFilter').val();
        
        $.ajax({
            url: '{{ route('pilars.index') }}',
            type: 'GET',
            data: {
                renstraID: renstraID,
                format: 'tree'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#tree-grid-container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
            },
            success: function(response) {
                console.log('Tree data response:', response);
                
                // Clear container
                $('#tree-grid-container').html('<table id="tree-grid" class="table table-bordered"><thead><tr><th width="5%">#</th><th>Nama</th><th width="15%">Actions</th></tr></thead><tbody></tbody></table>');
                
                // Add rows to the table
                var treeData = response.data || [];
                var tableBody = $('#tree-grid tbody');
                
                if (treeData.length === 0) {
                    tableBody.html('<tr><td colspan="3" class="text-center">No data available</td></tr>');
                    return;
                }
                
                $.each(treeData, function(i, item) {
                    var row = $('<tr></tr>');
                    
                    // Set TreeGrid attributes
                    row.addClass('treegrid-' + item.id);
                    if (item.parent) {
                        row.addClass('treegrid-parent-' + item.parent);
                    }
                    
                    // Add level class for styling
                    row.addClass('level-' + item.level);
                    
                    // Add row class if provided
                    if (item.row_class) {
                        row.addClass(item.row_class);
                    }
                    
                    // Add cells
                    row.append('<td>' + (item.no || '') + '</td>');
                    
                    // Add icon based on type
                    var icon = '';
                    switch(item.type) {
                        case 'pilar':
                            icon = '<i class="fas fa-building mr-2"></i>';
                            break;
                        case 'isu':
                            icon = '<i class="fas fa-lightbulb mr-2"></i>';
                            break;
                        case 'program':
                            icon = '<i class="fas fa-project-diagram mr-2"></i>';
                            break;
                        case 'rektor':
                            icon = '<i class="fas fa-user-tie mr-2"></i>';
                            break;
                        case 'indikator':
                            icon = '<i class="fas fa-chart-line mr-2"></i>';
                            break;
                        case 'kegiatan':
                            icon = '<i class="fas fa-tasks mr-2"></i>';
                            break;
                    }
                    
                    row.append('<td>' + icon + item.nama + '</td>');
                    row.append('<td>' + (item.actions || '') + '</td>');
                    
                    tableBody.append(row);
                });
                
                // Initialize TreeGrid
                $('#tree-grid').treegrid({
                    expanderExpandedClass: 'fas fa-minus-square',
                    expanderCollapsedClass: 'fas fa-plus-square',
                    initialState: 'expanded' // Start with all nodes expanded
                });
                
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
            },
            error: function(xhr) {
                console.error('Error loading data:', xhr);
                $('#tree-grid-container').html('<div class="alert alert-danger">Error loading data: ' + (xhr.responseJSON?.message || xhr.statusText) + '</div>');
            },
            complete: function() {
                isFiltering = false;
            }
        });
    }

    // Function to initialize event handlers for dynamic content
    function initEventHandlers() {
        // Handle modal loading
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
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content: ' + (xhr.responseJSON?.message || xhr.statusText) + '</div>');
                }
            });
        });
    }
    
    // Initialize Select2 in modals
    function initModalSelect2() {
        if ($.fn.select2) {
            $('.modal .select2').select2({
                dropdownParent: $('#mainModal')
            });
        }
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
</script>
@endpush

