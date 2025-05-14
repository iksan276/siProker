@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Kegiatan</h1>
<p class="mb-4">Kelola Kegiatan dalam tampilan hierarki.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- Color Legend Card -->
<div class="card shadow mb-3">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Keterangan Warna</h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between">
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(156, 39, 176, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Kegiatan</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(255, 140, 0, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Sub Kegiatan</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(0, 0, 0, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">RAB</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 w-100">Kegiatan List</h6>
        <div class="d-flex flex-wrap w-100 w-md-auto justify-content-start justify-content-md-end">
        <a href="{{ route('kegiatans.export.excel', request()->query()) }}" class="btn btn-success btn-sm mr-1">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('kegiatans.create') }}" data-title="Tambah Kegiatan" data-toggle="tooltip" title="Tambah kegiatan baru">
                <i class="fas fa-plus fa-sm"></i> Tambah Kegiatan
            </button>
        </div>
    </div>
</div>
    <div class="card-body">
        <!-- Filters -->
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
        
        <div class="form-group">
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
           <select id="programRektorFilter" class="form-control select2-filter" {{ empty($selectedProgramPengembangan) ? 'disabled' : '' }}>
                <option value="">-- Pilih Program Rektor --</option>
                @foreach($programRektors as $programRektor)
                    <option value="{{ $programRektor->ProgramRektorID }}" {{ isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID ? 'selected' : '' }}>
                        {{ $programRektor->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <!-- Tree Grid Table -->
        <div id="tree-grid-container">
            <table id="tree-grid" class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th class="text-center">Nama</th>
                        <th class="text-center" style="width: 20%;">Actions</th>
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
    
    /* Hover effect */
    #tree-grid tbody tr:hover {
        background-color: rgba(156, 39, 176, 0.1); /* light-primary color */
    }
    
    /* Different background colors for different node types */
    tr.node-kegiatan {
        background-color: rgba(156, 39, 176, 0.1); /* Light blue for Kegiatan */
    }
    
    tr.node-subkegiatan {
        background-color: rgba(255, 140, 0, 0.1); /* Light info color for Sub Kegiatan */
    }
    
    tr.node-rab {
        background-color: rgba(0, 0, 0, 0.1); /* Light green for RAB */
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

    /* Ensure all tooltips are visible */
    .tooltip {
        z-index: 9999;
    }
    
    /* Action button group styling */
    .action-btn-group {
        display: flex;
        gap: 5px;
    }
    
    .action-btn-group .btn {
        margin-right: 2px;
    }
    
    /* Divider for action button groups */
    .action-divider {
        width: 1px;
        background-color: #e3e6f0;
        margin: 0 5px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    // Store expanded state
    var expandedNodes = JSON.parse(localStorage.getItem('expandedNodesKegiatan') || '{}');
    var nodeRelationships = {}; // To store parent-child relationships
    var nodeLevels = {}; // To store node levels
    var nodeTypes = {}; // To store node types
    var isFiltering = false;
    var selectedRenstraId = getCookie('selected_renstra') || "{{ $selectedRenstra ?? '' }}";
    var selectedPilarId = getCookie('selected_pilar') || "{{ $selectedPilar ?? '' }}";
    var selectedIsuId = getCookie('selected_isu') || "{{ $selectedIsu ?? '' }}";
    var selectedProgramPengembanganId = getCookie('selected_program_pengembangan') || "{{ $selectedProgramPengembangan ?? '' }}";
    var selectedProgramRektorId = getCookie('selected_program_rektor') || "{{ $selectedProgramRektor ?? '' }}";
    
    // Function to load pilars for a selected renstra
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
    
    // Function to get node type tooltip
    function getNodeTypeTooltip(nodeType) {
        switch(nodeType) {
            case 'kegiatan':
                return 'Ini adalah Kegiatan';
            case 'subkegiatan':
                return 'Ini adalah Sub Kegiatan';
            case 'rab':
                return 'Ini adalah RAB';
            default:
                return 'Ini adalah item';
        }
    }
    
    // Function to get expand tooltip based on node type
    function getExpandTooltip(nodeType) {
        switch(nodeType) {
            case 'kegiatan':
                return 'Lihat sub kegiatan dan RAB';
            case 'subkegiatan':
                return 'Lihat RAB sub kegiatan';
            case 'rab':
                return 'Lihat detail RAB';
            default:
                return 'Lihat detail';
        }
    }
    
    // Function to get collapse tooltip based on node type
    function getCollapseTooltip(nodeType) {
        switch(nodeType) {
            case 'kegiatan':
                return 'Tutup sub kegiatan dan RAB';
            case 'subkegiatan':
                return 'Tutup RAB sub kegiatan';
            case 'rab':
                return 'Tutup detail RAB';
            default:
                return 'Tutup detail';
        }
    }
    
    $(document).ready(function () {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
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
        
        $('#tree-grid th').addClass('text-dark');

        loadTreeData();
        
        // Handle filter changes
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
                loadTreeData();
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
                    loadTreeData();
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
            
            // Clear selections if pilar is cleared
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
                
                // Reload TreeTable with only renstra filter
                isFiltering = true;
                loadTreeData();
                return;
            }
            
            // If pilar is selected, store it and enable isu filter
            setCookie('selected_pilar', pilarID, 30);
            updateUrlParameter('pilarID', pilarID);
            isFiltering = true;
            loadIsusForPilar(pilarID, selectedIsuId);
            loadTreeData();
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
            
            // Clear selections if isu is cleared
            if (!isuID) {
                selectedProgramPengembanganId = '';
                selectedProgramRektorId = '';
                eraseCookie('selected_isu');
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('isuID', null);
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with renstra and pilar filters
                // Reload TreeTable with renstra and pilar filters
                isFiltering = true;
                loadTreeData();
                return;
            }
            
            // If isu is selected, store it and enable program pengembangan filter
            setCookie('selected_isu', isuID, 30);
            updateUrlParameter('isuID', isuID);
            isFiltering = true;
            
            // Load program pengembangans for selected isu via AJAX
            loadProgramsForIsu(isuID, selectedProgramPengembanganId);
            loadTreeData();
        });
        
        // Handle program pengembangan filter change
        $('#programPengembanganFilter').on('change', function() {
            var programPengembanganID = $(this).val();
            
            // Store selected Program Pengembangan ID in global variable and cookie
            selectedProgramPengembanganId = programPengembanganID;
            
            // Reset program rektor filter
            $('#programRektorFilter').empty().append('<option value="">-- Pilih Program Rektor --</option>');
            $('#programRektorFilter').val('').prop('disabled', true);
            
            // Clear selections if program pengembangan is cleared
            if (!programPengembanganID) {
                selectedProgramRektorId = '';
                eraseCookie('selected_program_pengembangan');
                eraseCookie('selected_program_rektor');
                updateUrlParameter('programPengembanganID', null);
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with renstra, pilar, and isu filters
                isFiltering = true;
                loadTreeData();
                return;
            }
            
            // If program pengembangan is selected, store it and enable program rektor filter
            setCookie('selected_program_pengembangan', programPengembanganID, 30);
            updateUrlParameter('programPengembanganID', programPengembanganID);
            isFiltering = true;
            loadProgramRektorsForProgram(programPengembanganID, selectedProgramRektorId);
            
            loadTreeData();
        });
        
        // Handle program rektor filter change
        $('#programRektorFilter').on('change', function() {
            var programRektorID = $(this).val();
            
            // Store selected Program Rektor ID in global variable and cookie
            selectedProgramRektorId = programRektorID;
            
            // Clear selections if program rektor is cleared
            if (!programRektorID) {
                eraseCookie('selected_program_rektor');
                updateUrlParameter('programRektorID', null);
                
                // Reload TreeTable with renstra, pilar, isu, and program pengembangan filters
                isFiltering = true;
                loadTreeData();
                return;
            }
            
            // If program rektor is selected, store it
            setCookie('selected_program_rektor', programRektorID, 30);
            updateUrlParameter('programRektorID', programRektorID);
            isFiltering = true;
            
            // Reload tree data with program rektor filter
            loadTreeData();
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
            e.stopPropagation();
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
                // Add to expanded nodes
                expandedNodes[nodeId] = true;
            }
            
            // Initialize tooltips for newly added elements
            initTooltips();
            
            // Save expanded state to localStorage
            localStorage.setItem('expandedNodesKegiatan', JSON.stringify(expandedNodes));
        });
        
        // Handle row click to toggle expansion
        $(document).on('click', '#tree-grid tbody tr', function(e) {
            // Only if the click was not on a button or other interactive element
            if (!$(e.target).closest('button, a, input, select').length) {
                $(this).find('.node-expander').trigger('click');
            }
        });
    });
    
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
            animation: false,
            html: true
        });
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
    
    function loadTreeData() {
        var filters = {
            renstraID: selectedRenstraId,
            pilarID: selectedPilarId,
            isuID: selectedIsuId,
            programPengembanganID: selectedProgramPengembanganId,
            programRektorID: selectedProgramRektorId,
            format: 'tree'
        };
        
        $.ajax({
            url: '{{ route('kegiatans.index') }}',
            type: 'GET',
            data: filters,
            dataType: 'json',
            beforeSend: function() {
                $('#tree-grid-container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
            },
            success: function(response) {
                // Clear container
                $('#tree-grid-container').html('<table id="tree-grid" class="table table-bordered"><thead><tr><th class="text-center" style="width: 5%;">No</th><th class="text-center">Nama</th><th class="text-center" style="width: 20%;">Actions</th></tr></thead><tbody></tbody></table>');
                
                $('#tree-grid th').addClass('text-dark');
                // Add rows to the table
                var treeData = response.data || [];
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
                    if (item.type === 'kegiatan') {
                        row.css('background-color', 'rgba(156, 39, 176, 0.1)'); // Light blue
                    } else if (item.type === 'subkegiatan') {
                        row.css('background-color', 'rgba(255, 140, 0, 0.1)'); // Light info
                    } else if (item.type === 'rab') {
                        row.css('background-color', 'rgba(0, 0, 0, 0.1)'); // Light green
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
                    
                    // Create tooltip text that includes both the original tooltip and the node type information
                    var tooltipText = getNodeTypeTooltip(item.type);
                    if (item.tooltip) {
                        tooltipText += '<br>' + item.tooltip;
                    }
                    
                    nameText = indentPrefix + '<span class="node-name" data-toggle="tooltip" data-html="true" title="' + tooltipText + '">' + item.nama + '</span>';
                    
                    var nameCell = '<td>' + nameText + "&nbsp;&nbsp;" + expander + '</td>';
                    row.append(nameCell);
                    
                    // Add action buttons based on node type
                    var actions = '';
                    
                    if (item.type === 'kegiatan') {
                        var kegiatanId = item.id.replace('kegiatan_', '');
                        // Add view, edit, delete buttons for kegiatan
                        // Plus add new buttons for adding sub-kegiatan and RAB directly
                        actions = '<div class="action-btn-group">' +
                                  '<button class="btn btn-primary btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('sub-kegiatans.create') }}?kegiatanID=" + kegiatanId + '" ' +
                                  'data-title="Tambah Sub Kegiatan" data-toggle="tooltip" title="Tambah sub kegiatan baru">' +
                                  '<i class="fas fa-plus"></i>' +
                                  '</button> ' +
                                  '<button class="btn btn-success btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('rabs.create') }}?kegiatanID=" + kegiatanId + '" ' +
                                  'data-title="Tambah RAB" data-toggle="tooltip" title="Tambah RAB baru untuk kegiatan ini">' +
                                  '<i class="fas fa-plus"></i>' +
                                  '</button> '+
                                  '<button class="btn btn-info btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('kegiatans.show', ':id') }}".replace(':id', kegiatanId) + '" ' +
                                  'data-title="Detail Kegiatan" data-toggle="tooltip" title="Lihat detail kegiatan">' +
                                  '<i class="fas fa-eye"></i>' +
                                  '</button> ' +
                                  '<button class="btn btn-warning btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('kegiatans.edit', ':id') }}".replace(':id', kegiatanId) + '" ' +
                                  'data-title="Edit Kegiatan" data-toggle="tooltip" title="Edit kegiatan">' +
                                  '<i class="fas fa-edit"></i>' +
                                  '</button> ' +
                                  '<button type="button" class="btn btn-danger btn-square btn-sm delete-kegiatan" ' +
                                  'data-id="' + kegiatanId + '" data-toggle="tooltip" title="Hapus kegiatan">' +
                                  '<i class="fas fa-trash"></i>' +
                                  '</button>' +
                                  '<span class="action-divider"></span>' +
                                  '</div>';
                    } else if (item.type === 'subkegiatan') {
                        // Add view, edit, delete buttons for subkegiatan
                        // Plus add new button for adding RAB to this subkegiatan
                        var subKegiatanId = item.id.replace('subkegiatan_', '');
                        actions = '<div class="action-btn-group">' +
                                 '<button class="btn btn-success btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('rabs.create') }}?subKegiatanID=" + subKegiatanId + '" ' +
                                  'data-title="Tambah RAB" data-toggle="tooltip" title="Tambah RAB baru untuk sub kegiatan ini">' +
                                  '<i class="fas fa-plus"></i>' +
                                  '</button> ' +
                                  '<button class="btn btn-info btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('sub-kegiatans.show', ':id') }}".replace(':id', subKegiatanId) + '" ' +
                                  'data-title="Detail Sub Kegiatan" data-toggle="tooltip" title="Lihat detail sub kegiatan">' +
                                  '<i class="fas fa-eye"></i>' +
                                  '</button> ' +
                                  '<button class="btn btn-warning btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('sub-kegiatans.edit', ':id') }}".replace(':id', subKegiatanId) + '" ' +
                                  'data-title="Edit Sub Kegiatan" data-toggle="tooltip" title="Edit sub kegiatan">' +
                                  '<i class="fas fa-edit"></i>' +
                                  '</button> ' +
                                  '<button type="button" class="btn btn-danger btn-square btn-sm delete-sub-kegiatan" ' +
                                  'data-id="' + subKegiatanId + '" data-toggle="tooltip" title="Hapus sub kegiatan">' +
                                  '<i class="fas fa-trash"></i>' +
                                  '</button>' +
                                  '<span class="action-divider"></span>' +
                                  '</div>';
                    } else if (item.type === 'rab') {
                        // Add view, edit, and delete buttons for RAB
                        var rabId = '';
                        if (item.id.startsWith('rab_sub_')) {
                            rabId = item.id.replace('rab_sub_', '');
                        } else {
                            rabId = item.id.replace('rab_', '');
                        }
                        
                        actions = '<div class="action-btn-group">' +
                                  '<button class="btn btn-info btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('rabs.show', ':id') }}".replace(':id', rabId) + '" ' +
                                  'data-title="Detail RAB" data-toggle="tooltip" title="Lihat detail RAB">' +
                                  '<i class="fas fa-eye"></i>' +
                                  '</button> ' +
                                  '<button class="btn btn-warning btn-square btn-sm load-modal" ' +
                                  'data-url="' + "{{ route('rabs.edit', ':id') }}".replace(':id', rabId) + '" ' +
                                  'data-title="Edit RAB" data-toggle="tooltip" title="Edit RAB">' +
                                  '<i class="fas fa-edit"></i>' +
                                  '</button> ' +
                                  '<button type="button" class="btn btn-danger btn-square btn-sm delete-rab" ' +
                                  'data-id="' + rabId + '" data-toggle="tooltip" title="Hapus RAB">' +
                                  '<i class="fas fa-trash"></i>' +
                                  '</button>' +
                                  '</div>';
                    }
                    
                    row.append('<td class="text-center" style="white-space:nowrap;width:1px;">' + actions + '</td>');
                    
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
                localStorage.setItem('expandedNodesKegiatan', JSON.stringify(expandedNodes));
                
                // Re-initialize event handlers for dynamic content
                initEventHandlers();
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
            $('#mainModal .modal-body').empty();
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
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    function eraseCookie(name) {
        document.cookie = name + '=; Max-Age=-99999999; path=/';
    }
    
    // Add this to handle mouse hover effects
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
</script>
@endpush
