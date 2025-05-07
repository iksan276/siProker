@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Kegiatan</h1>
<p class="mb-4">Kelola Kegiatan dalam tampilan hierarki.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kegiatan Hierarki</h6>
        <div>
            <a href="{{ route('kegiatans.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('kegiatans.create') }}" data-title="Tambah Kegiatan">
                <i class="fas fa-plus fa-sm"></i> Tambah Kegiatan
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="form-group">
            <label for="renstraFilter">Filter Renstra:</label>
            <select id="renstraFilter" class="form-control select2-filter">
                <option value="">-- Pilih Renstra --</option>
                @foreach($renstras as $renstra)
                    <option value="{{ $renstra->RenstraID }}" {{ isset($selectedRenstra) && $selectedRenstra == $renstra->RenstraID ? 'selected' : '' }}>
                        {{ $renstra->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="pilarFilter">Filter Pilar:</label>
            <select id="pilarFilter" class="form-control select2-filter" {{ empty($selectedRenstra) ? 'disabled' : '' }}>
                <option value="">-- Pilih Pilar --</option>
                @foreach($pilars as $pilar)
                    <option value="{{ $pilar->PilarID }}" {{ isset($selectedPilar) && $selectedPilar == $pilar->PilarID ? 'selected' : '' }}>
                        {{ $pilar->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="isuFilter">Filter Isu Strategis:</label>
            <select id="isuFilter" class="form-control select2-filter" {{ empty($selectedPilar) ? 'disabled' : '' }}>
                <option value="">-- Pilih Isu Strategis --</option>
                @foreach($isuStrategis as $isu)
                    <option value="{{ $isu->IsuID }}" {{ isset($selectedIsu) && $selectedIsu == $isu->IsuID ? 'selected' : '' }}>
                        {{ $isu->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group">
            <label for="programPengembanganFilter">Filter Program Pengembangan:</label>
            <select id="programPengembanganFilter" class="form-control select2-filter" {{ empty($selectedIsu) ? 'disabled' : '' }}>
                <option value="">-- Pilih Program Pengembangan --</option>
                @foreach($programPengembangans as $programPengembangan)
                    <option value="{{ $programPengembangan->ProgramPengembanganID }}" {{ isset($selectedProgramPengembangan) && $selectedProgramPengembangan == $programPengembangan->ProgramPengembanganID ? 'selected' : '' }}>
                        {{ $programPengembangan->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group mb-5">
            <label for="programRektorFilter">Filter Program Rektor:</label>
            <select id="programRektorFilter" class="form-control select2-filter" {{ empty($selectedProgramPengembangan) ? 'disabled' : '' }}>
                <option value="">-- Pilih Program Rektor --</option>
                @foreach($programRektors as $programRektor)
                    <option value="{{ $programRektor->ProgramRektorID }}" {{ isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID ? 'selected' : '' }}>
                        {{ $programRektor->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="kegiatanTreeTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- TreeTable will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let kegiatanTreeTable;
    var isFiltering = false;
    var selectedRenstraId = getCookie('selected_renstra') || "{{ $selectedRenstra ?? '' }}";
    var selectedPilarId = getCookie('selected_pilar') || "{{ $selectedPilar ?? '' }}";
    var selectedIsuId = getCookie('selected_isu') || "{{ $selectedIsu ?? '' }}";
    var selectedProgramPengembanganId = getCookie('selected_program_pengembangan') || "{{ $selectedProgramPengembangan ?? '' }}";
    var selectedProgramRektorId = getCookie('selected_program_rektor') || "{{ $selectedProgramRektor ?? '' }}";
    
    $(document).ready(function () {
        // Set the filter values from cookies if available
        if (selectedRenstraId) {
            $('#renstraFilter').val(selectedRenstraId).trigger('change');
            loadPilarsForRenstra(selectedRenstraId, selectedPilarId);
        }
        
        if (selectedPilarId) {
            $('#pilarFilter').val(selectedPilarId).trigger('change');
            loadIsusForPilar(selectedPilarId, selectedIsuId);
        }
        
        if (selectedIsuId) {
            $('#isuFilter').val(selectedIsuId).trigger('change');
            loadProgramsForIsu(selectedIsuId, selectedProgramPengembanganId);
        }
        
        if (selectedProgramPengembanganId) {
            $('#programPengembanganFilter').val(selectedProgramPengembanganId).trigger('change');
            loadProgramRektorsForProgram(selectedProgramPengembanganId, selectedProgramRektorId);
        }
        
        if (selectedProgramRektorId) {
            $('#programRektorFilter').val(selectedProgramRektorId).trigger('change');
        }
        
        // Initialize TreeTable
        initTreeTable();
        
        // Handle renstra filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Store selected Renstra ID in global variable and cookie
            selectedRenstraId = renstraID;
            
            // Reset pilar, isu, program pengembangan, and program rektor filters
            $('#pilarFilter').empty().append('<option value="">-- Pilih Pilar --</option>');
            $('#pilarFilter').val('').prop('disabled', true);
            
            $('#isuFilter').empty().append('<option value="">-- Pilih Isu Strategis --</option>');
            $('#isuFilter').val('').prop('disabled', true);
            
            $('#programPengembanganFilter').empty().append('<option value="">-- Pilih Program Pengembangan --</option>');
            $('#programPengembanganFilter').val('').prop('disabled', true);
            
            $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
            $('#programRektorFilter').val('').prop('disabled', true);
            
            // Clear selections if renstra is cleared
            if (!renstraID) {
                selectedPilarId = '';
                selectedIsuId = '';
                selectedProgramPengembanganId = '';
                selectedProgramRektorId = '';
                eraseCookie('selected_renstra');
                eraseCookie('selected_pilar');
                eraseCookie('selected_isu');
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('renstraID', null);
                updateUrlParameter('pilarID', null);
                updateUrlParameter('isuID', null);
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with no filters
                isFiltering = true;
                kegiatanTreeTable.ajax.reload(function() {
                    isFiltering = false;
                });
                return;
            }
            
            // If renstra is selected, store it and enable pilar filter
            setCookie('selected_renstra', renstraID, 30);
            updateUrlParameter('renstraID', renstraID);
            isFiltering = true;
            
            // Load pilars for selected renstra via AJAX
            $.ajax({
                url: "{{ route('api.pilars-by-renstra') }}",
                type: 'GET',
                data: {
                    renstraID: renstraID
                },
                success: function(response) {
                    // Enable pilar filter
                    $('#pilarFilter').prop('disabled', false);
                    
                    // Populate pilar filter with options
                    if (response.pilars && response.pilars.length > 0) {
                        $.each(response.pilars, function(index, pilar) {
                            $('#pilarFilter').append('<option value="' + pilar.PilarID + '">' + pilar.Nama + '</option>');
                        });
                        
                        // If there was a previously selected pilar for this renstra, select it
                        if (selectedPilarId) {
                            // Check if the previously selected pilar exists in the new options
                            var pilarExists = false;
                            $.each(response.pilars, function(index, pilar) {
                                if (pilar.PilarID == selectedPilarId) {
                                    pilarExists = true;
                                    return false; // Break the loop
                                }
                            });
                            
                            if (pilarExists) {
                                $('#pilarFilter').val(selectedPilarId);
                                // Load isu strategis for this pilar
                                loadIsusForPilar(selectedPilarId, selectedIsuId);
                            } else {
                                // If pilar doesn't exist for this renstra, clear the selection
                                selectedPilarId = '';
                                selectedIsuId = '';
                                selectedProgramPengembanganId = '';
                                selectedProgramRektorId = '';
                                eraseCookie('selected_pilar');
                                eraseCookie('selected_isu');
                                eraseCookie('selected_program_pengembangan');
                                eraseCookie('selected_program_rektor');
                                updateUrlParameter('pilarID', null);
                                updateUrlParameter('isuID', null);
                                updateUrlParameter('programPengembanganID', null);
                                updateUrlParameter('programRektorID', null);
                            }
                        }
                    }
                    
                    // Reload TreeTable with new filter
                    kegiatanTreeTable.ajax.reload(function() {
                        isFiltering = false;
                    });
                },
                error: function() {
                    // Reset filtering flag
                    isFiltering = false;
                    showAlert('danger', 'Failed to load pilars');
                }
            });
        });
        
        // Handle pilar filter change
        $('#pilarFilter').on('change', function() {
            var pilarID = $(this).val();
            
            // Store selected Pilar ID in global variable and cookie
            selectedPilarId = pilarID;
            
            // Reset isu, program pengembangan, and program rektor filters
            $('#isuFilter').empty().append('<option value="">-- Pilih Isu Strategis --</option>');
            $('#isuFilter').val('').prop('disabled', true);
            
            $('#programPengembanganFilter').empty().append('<option value="">-- Pilih Program Pengembangan --</option>');
            $('#programPengembanganFilter').val('').prop('disabled', true);
            
            $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
            $('#programRektorFilter').val('').prop('disabled', true);
            
            // Clear isu, program pengembangan, and program rektor selections if pilar is cleared
            if (!pilarID) {
                selectedIsuId = '';
                selectedProgramPengembanganId = '';
                selectedProgramRektorId = '';
                eraseCookie('selected_pilar');
                eraseCookie('selected_isu');
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('pilarID', null);
                updateUrlParameter('isuID', null);
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with current filters
                isFiltering = true;
                kegiatanTreeTable.ajax.reload(function() {
                    isFiltering = false;
                });
                return;
            }
            
            // If pilar is selected, store it and load isu strategis
            setCookie('selected_pilar', pilarID, 30);
            updateUrlParameter('pilarID', pilarID);
            isFiltering = true;
            
            // Load isu strategis for selected pilar via AJAX
            loadIsusForPilar(pilarID, selectedIsuId);
            
            // Reload TreeTable with new filter
            kegiatanTreeTable.ajax.reload(function() {
                isFiltering = false;
            });
        });
        
        // Handle isu strategis filter change
        $('#isuFilter').on('change', function() {
            var isuID = $(this).val();
            
            // Store selected Isu ID in global variable and cookie
            selectedIsuId = isuID;
            
            // Reset program pengembangan and program rektor filters
            $('#programPengembanganFilter').empty().append('<option value="">-- Pilih Program Pengembangan --</option>');
            $('#programPengembanganFilter').val('').prop('disabled', true);
            
            $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
            $('#programRektorFilter').val('').prop('disabled', true);
            
            // Clear program pengembangan and program rektor selection if isu is cleared
            if (!isuID) {
                selectedProgramPengembanganId = '';
                selectedProgramRektorId = '';
                eraseCookie('selected_isu');
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('isuID', null);
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with current filters
                isFiltering = true;
                kegiatanTreeTable.ajax.reload(function() {
                    isFiltering = false;
                });
                return;
            }
            
            // If isu is selected, store it and load program pengembangans
            setCookie('selected_isu', isuID, 30);
            updateUrlParameter('isuID', isuID);
            isFiltering = true;
            
            // Load program pengembangans for selected isu via AJAX
            loadProgramsForIsu(isuID, selectedProgramPengembanganId);
            
            // Reload TreeTable with new filter
            kegiatanTreeTable.ajax.reload(function() {
                isFiltering = false;
            });
        });
        
        // Handle program pengembangan filter change
        $('#programPengembanganFilter').on('change', function() {
            var programPengembanganID = $(this).val();
            
            // Store selected Program Pengembangan ID in global variable and cookie
            selectedProgramPengembanganId = programPengembanganID;
            
            // Reset program rektor filter
            $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
            $('#programRektorFilter').val('').prop('disabled', true);
            
            // Clear program rektor selection if program pengembangan is cleared
            if (!programPengembanganID) {
                selectedProgramRektorId = '';
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with current filters
                isFiltering = true;
                kegiatanTreeTable.ajax.reload(function() {
                    isFiltering = false;
                });
                return;
            }
            
            // If program pengembangan is selected, store it and load program rektors
            setCookie('selected_program_pengembangan', programPengembanganID, 30);
            updateUrlParameter('programPengembanganID', programPengembanganID);
            isFiltering = true;
            
            // Load program rektors for selected program pengembangan via AJAX
            loadProgramRektorsForProgram(programPengembanganID, selectedProgramRektorId);
            
            // Reload TreeTable with new filter
            kegiatanTreeTable.ajax.reload(function() {
                isFiltering = false;
            });
        });
        
        // Handle program rektor filter change
        $('#programRektorFilter').on('change', function() {
            var programRektorID = $(this).val();
            
            // Store selected Program Rektor ID in global variable and cookie
            selectedProgramRektorId = programRektorID;
            
            if (programRektorID) {
                setCookie('selected_program_rektor', programRektorID, 30);
                updateUrlParameter('programRektorID', programRektorID);
            } else {
                eraseCookie('selected_program_rektor');
                updateUrlParameter('programRektorID', null);
            }
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Reload TreeTable with new filter
            kegiatanTreeTable.ajax.reload(function() {
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
                        
                        // Reload only the TreeTable
                        kegiatanTreeTable.ajax.reload();
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
                    form.find('button[type="submit"]').prop('disabled', false).html('Simpan');
                }
            });
        });
        
        // Handle delete button click
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
                                // Reload only the TreeTable
                                kegiatanTreeTable.ajax.reload();
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
    });
    
    // Function to initialize the TreeTable
    function initTreeTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#kegiatanTreeTable')) {
            $('#kegiatanTreeTable').DataTable().destroy();
        }
        
        // Initialize TreeTable with AJAX
        kegiatanTreeTable = $('#kegiatanTreeTable').DataTable({
            processing: true,
            serverSide: false, // We're handling the data ourselves
            ajax: {
                url: '{{ route('kegiatans.index') }}',
                type: 'GET',
                data: function(d) {
                    d.renstraID = selectedRenstraId;
                    d.pilarID = selectedPilarId;
                    d.isuID = selectedIsuId;
                    d.programPengembanganID = selectedProgramPengembanganId;
                    d.programRektorID = selectedProgramRektorId;
                    d.format = 'tree';
                    d.wantsJson = true;
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#kegiatanTreeTable_processing').hide();
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
                        return data ? '<span style="white-space:nowrap;width:1px">' + data + '</span>' : '';
                    }
                },
                { 
                    data: 'nama',
                    render: function(data, type, row) {
                        // Add indentation based on level
                        var padding = row.level * 20;
                        var icon = '';
                        
                        // Add appropriate icon based on type
                        if (row.type === 'kegiatan') {
                            icon = '<i class="fas fa-tasks text-primary mr-2"></i>';
                        } else if (row.type === 'subkegiatan') {
                            icon = '<i class="fas fa-clipboard-list text-info mr-2"></i>';
                        } else if (row.type === 'rab') {
                            icon = '<i class="fas fa-money-bill-wave text-success mr-2"></i>';
                        }
                        
                        // Add expand/collapse icon if has children
                        var expandIcon = '';
                        if (row.has_children) {
                            expandIcon = '<span class="tree-toggle mr-2" data-id="' + row.id + '">' +
                                         '<i class="fas fa-chevron-right"></i></span>';
                        } else {
                            expandIcon = '<span class="mr-4"></span>';
                        }
                        
                        return '<div class="d-flex align-items-center" style="padding-left: ' + padding + 'px;">' +
                               expandIcon + icon + 
                               '<span data-toggle="tooltip" title="' + row.tooltip + '">' + data + '</span></div>';
                    }
                },
                { 
                    data: 'actions', 
                    className: 'text-center',
                    width: '1px',
                    orderable: false,
                    render: function(data, type, row) {
                        return data ? '<span style="white-space:nowrap;width:1px">' + data + '</span>' : '';
                    }
                }
            ],
            responsive: true,
            drawCallback: function() {
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
                
                // Initialize tooltips
                $('[data-toggle="tooltip"]').tooltip();
                
                // Hide child rows initially
                $('.tree-child').hide();
            },
            // Hide processing indicator for all operations except filtering
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
            // Override the default processing display behavior
            preDrawCallback: function() {
                if (!isFiltering) {
                    $('#kegiatanTreeTable_processing').hide();
                }
                return true;
            },
            rowCallback: function(row, data) {
                // Add classes to rows for tree structure
                $(row).addClass('tree-item');
                $(row).attr('data-id', data.id);
                
                if (data.parent) {
                    $(row).addClass('tree-child');
                    $(row).attr('data-parent', data.parent);
                }
                
                // Add custom row class if specified
                if (data.row_class) {
                    $(row).addClass(data.row_class);
                }
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#kegiatanTreeTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#kegiatanTreeTable_processing').hide();
            }
        });
        
        // Handle tree toggle click
        $('#kegiatanTreeTable').on('click', '.tree-toggle', function() {
            var id = $(this).data('id');
            var $icon = $(this).find('i');
            var isExpanded = $icon.hasClass('fa-chevron-down');
            
            if (isExpanded) {
                // Collapse this node and all its children
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                collapseNode(id);
            } else {
                // Expand just this node's immediate children
                $icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
                expandNode(id);
            }
        });
    }
    
    // Function to expand a tree node
    function expandNode(id) {
        $('.tree-child[data-parent="' + id + '"]').show();
    }
    
    // Function to collapse a tree node and all its children
    function collapseNode(id) {
        // Hide immediate children
        var $children = $('.tree-child[data-parent="' + id + '"]');
        $children.hide();
        
        // Also collapse any expanded children recursively
        $children.each(function() {
            var childId = $(this).data('id');
            var $childIcon = $('.tree-toggle[data-id="' + childId + '"] i');
            
            if ($childIcon.hasClass('fa-chevron-down')) {
                $childIcon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
                collapseNode(childId);
            }
        });
    }
    
    // Function to load pilars for a renstra
    function loadPilarsForRenstra(renstraID, selectedPilarId) {
        // Enable pilar filter first
        $('#pilarFilter').prop('disabled', false);
        
        $.ajax({
            url: "{{ route('api.pilars-by-renstra') }}",
            type: 'GET',
            data: {
                renstraID: renstraID
            },
            success: function(response) {
                // Clear existing options
                $('#pilarFilter').empty().append('<option value="">-- Pilih Pilar --</option>');
                
                // Populate pilar filter with options
                if (response.pilars && response.pilars.length > 0) {
                    $.each(response.pilars, function(index, pilar) {
                        $('#pilarFilter').append('<option value="' + pilar.PilarID + '">' + pilar.Nama + '</option>');
                    });
                    
                    // If there was a previously selected pilar, select it
                    if (selectedPilarId) {
                        // Check if the previously selected pilar exists in the new options
                        var pilarExists = false;
                        $.each(response.pilars, function(index, pilar) {
                            if (pilar.PilarID == selectedPilarId) {
                                pilarExists = true;
                                return false; // Break the loop
                            }
                        });
                        
                        if (pilarExists) {
                            $('#pilarFilter').val(selectedPilarId);
                            // Load isu strategis for this pilar
                            loadIsusForPilar(selectedPilarId, selectedIsuId);
                        }
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load pilars');
            }
        });
    }
    
    // Function to load isu strategis for a pilar
    function loadIsusForPilar(pilarID, selectedIsuId) {
        // Enable isu filter first
        $('#isuFilter').prop('disabled', false);
        
        $.ajax({
            url: "{{ route('api.isus-by-pilar') }}",
            type: 'GET',
            data: {
                pilarID: pilarID
            },
            success: function(response) {
                // Clear existing options
                $('#isuFilter').empty().append('<option value="">-- Pilih Isu Strategis --</option>');
                
                // Populate isu filter with options
                if (response.isus && response.isus.length > 0) {
                    $.each(response.isus, function(index, isu) {
                        $('#isuFilter').append('<option value="' + isu.IsuID + '">' + isu.Nama + '</option>');
                    });
                    
                    // If there was a previously selected isu, select it
                    if (selectedIsuId) {
                        // Check if the previously selected isu exists in the new options
                        var isuExists = false;
                        $.each(response.isus, function(index, isu) {
                            if (isu.IsuID == selectedIsuId) {
                                isuExists = true;
                                return false; // Break the loop
                            }
                        });
                        
                        if (isuExists) {
                            $('#isuFilter').val(selectedIsuId);
                            // Load program pengembangans for this isu
                            loadProgramsForIsu(selectedIsuId, selectedProgramPengembanganId);
                        }
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load isu strategis');
            }
        });
    }
    
    // Function to load program pengembangans for an isu
    function loadProgramsForIsu(isuID, selectedProgramPengembanganId) {
        // Enable program pengembangan filter first
        $('#programPengembanganFilter').prop('disabled', false);
        
        $.ajax({
            url: "{{ route('api.programs-by-isu') }}",
            type: 'GET',
            data: {
                isuID: isuID
            },
            success: function(response) {
                // Clear existing options
                $('#programPengembanganFilter').empty().append('<option value="">-- Pilih Program Pengembangan --</option>');
                
                // Populate program pengembangan filter with options
                if (response.programs && response.programs.length > 0) {
                    $.each(response.programs, function(index, program) {
                        $('#programPengembanganFilter').append('<option value="' + program.ProgramPengembanganID + '">' + program.Nama + '</option>');
                    });
                    
                    // If there was a previously selected program, select it
                    if (selectedProgramPengembanganId) {
                        // Check if the previously selected program exists in the new options
                        var programExists = false;
                        $.each(response.programs, function(index, program) {
                            if (program.ProgramPengembanganID == selectedProgramPengembanganId) {
                                programExists = true;
                                return false; // Break the loop
                            }
                        });
                        
                        if (programExists) {
                            $('#programPengembanganFilter').val(selectedProgramPengembanganId);
                            // Load program rektors for this program pengembangan
                            loadProgramRektorsForProgram(selectedProgramPengembanganId, selectedProgramRektorId);
                        }
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load program pengembangans');
            }
        });
    }
    
    // Function to load program rektors for a program pengembangan
    function loadProgramRektorsForProgram(programPengembanganID, selectedProgramRektorId) {
        // Enable program rektor filter first
        $('#programRektorFilter').prop('disabled', false);
        
        $.ajax({
            url: "{{ route('api.programs-by-rektor') }}",
            type: 'GET',
            data: {
                programPengembanganID: programPengembanganID
            },
            success: function(response) {
                // Clear existing options
                $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
                
                // Populate program rektor filter with options
                if (response.programRektors && response.programRektors.length > 0) {
                    $.each(response.programRektors, function(index, programRektor) {
                        $('#programRektorFilter').append('<option value="' + programRektor.ProgramRektorID + '">' + programRektor.Nama + '</option>');
                    });
                    
                    // If there was a previously selected program rektor, select it
                    if (selectedProgramRektorId) {
                        // Check if the previously selected program rektor exists in the new options
                        var programRektorExists = false;
                        $.each(response.programRektors, function(index, programRektor) {
                            if (programRektor.ProgramRektorID == selectedProgramRektorId) {
                                programRektorExists = true;
                                return false; // Break the loop
                            }
                        });
                        
                        if (programRektorExists) {
                            $('#programRektorFilter').val(selectedProgramRektorId);
                        }
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load program rektors');
            }
        });
    }
    
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
            
            // Add selected filter IDs to the URL if they exist
            if (selectedRenstraId) {
                url = addOrUpdateQueryParam(url, 'renstraID', selectedRenstraId);
            }
            
            if (selectedPilarId) {
                url = addOrUpdateQueryParam(url, 'pilarID', selectedPilarId);
            }
            
            if (selectedIsuId) {
                url = addOrUpdateQueryParam(url, 'isuID', selectedIsuId);
            }
            
            if (selectedProgramPengembanganId) {
                url = addOrUpdateQueryParam(url, 'programPengembanganID', selectedProgramPengembanganId);
            }
            
            if (selectedProgramRektorId) {
                url = addOrUpdateQueryParam(url, 'programRektorID', selectedProgramRektorId);
            }
            
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
        $('.delete-kegiatan').off('click').on('click', function(e) {
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
                                // Reload TreeTable
                                kegiatanTreeTable.ajax.reload();
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
            $('#kegiatanTreeTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#kegiatanTreeTable_processing').hide();
            }, 200);
        }
    });

    // Apply the stored filter values when the page is loaded or refreshed
    $(window).on('pageshow', function(event) {
        // This event fires when the page is shown, including when navigating back to it
        if (event.originalEvent.persisted) {
            // Page was loaded from cache (e.g., back button)
            var storedRenstraId = getCookie('selected_renstra');
            var storedPilarId = getCookie('selected_pilar');
            var storedIsuId = getCookie('selected_isu');
            var storedProgramPengembanganId = getCookie('selected_program_pengembangan');
            var storedProgramRektorId = getCookie('selected_program_rektor');
            
            if (storedRenstraId) {
                selectedRenstraId = storedRenstraId;
                
                // Set the select value and trigger change
                if ($('#renstraFilter').val() !== storedRenstraId) {
                    $('#renstraFilter').val(storedRenstraId).trigger('change');
                }
                
                // Update URL parameter
                updateUrlParameter('renstraID', storedRenstraId);
            }
            
            if (storedPilarId) {
                selectedPilarId = storedPilarId;
                
                // Set the select value and trigger change after renstra is loaded
                setTimeout(function() {
                    if ($('#pilarFilter').val() !== storedPilarId) {
                        $('#pilarFilter').val(storedPilarId).trigger('change');
                    }
                    
                    // Update URL parameter
                    updateUrlParameter('pilarID', storedPilarId);
                }, 500);
            }
            
            if (storedIsuId) {
                selectedIsuId = storedIsuId;
                
                // Set the select value and trigger change after pilar is loaded
                setTimeout(function() {
                    if ($('#isuFilter').val() !== storedIsuId) {
                        $('#isuFilter').val(storedIsuId).trigger('change');
                    }
                    
                    // Update URL parameter
                    updateUrlParameter('isuID', storedIsuId);
                }, 1000);
            }
            
            if (storedProgramPengembanganId) {
                selectedProgramPengembanganId = storedProgramPengembanganId;
                
                // Set the select value and trigger change after isu is loaded
                setTimeout(function() {
                    if ($('#programPengembanganFilter').val() !== storedProgramPengembanganId) {
                        $('#programPengembanganFilter').val(storedProgramPengembanganId).trigger('change');
                    }
                    
                    // Update URL parameter
                    updateUrlParameter('programPengembanganID', storedProgramPengembanganId);
                }, 1500);
            }
            
            if (storedProgramRektorId) {
                selectedProgramRektorId = storedProgramRektorId;
                
                // Set the select value and trigger change after program pengembangan is loaded
                setTimeout(function() {
                    if ($('#programRektorFilter').val() !== storedProgramRektorId) {
                        $('#programRektorFilter').val(storedProgramRektorId).trigger('change');
                    }
                    
                    // Update URL parameter
                    updateUrlParameter('programRektorID', storedProgramRektorId);
                }, 2000);
            }
        }
    });
</script>

<style>
    /* Tree view styling */
    .tree-toggle {
        cursor: pointer;
        display: inline-block;
        width: 20px;
        text-align: center;
    }
    
    .tree-toggle i {
        transition: transform 0.2s;
    }
    
    /* Hover effect for tree rows */
    .tree-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    /* Different background colors for different levels */
    .tree-item[data-level="0"] {
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    .tree-item[data-level="1"] {
        background-color: rgba(23, 162, 184, 0.03);
    }
    
    .tree-item[data-level="2"] {
        background-color: rgba(40, 167, 69, 0.03);
    }
</style>
@endpush
