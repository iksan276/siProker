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
        <h6 class="m-0 font-weight-bold text-primary">Keterangan Warna</h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between">
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(231, 74, 59, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Pilar</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(246, 194, 62, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Isu Strategis</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(28, 200, 138, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Program Pengembangan</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(10, 63, 223, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Program Rektor</span>
                </div>
            </div>
            <div class="mb-2">
                <div class="d-flex align-items-center">
                    <div class="tree-legend-line" style="background-color: rgba(156, 39, 176, 0.1); height: 5px; width: 30px;"></div>
                    <span class="ml-2">Kegiatan</span>
                </div>
            </div>
        </div>
    </div>
</div>


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
</style>
@endpush


@push('scripts')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    // Add this to your document ready function in the scripts section
// Replace the existing mouseenter and mouseleave event handlers with this code
// Replace the existing mouseenter and mouseleave event handlers with this code
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
    var activeAccordion = null;
    
    $(document).ready(function() {
        $('#tree-grid th').addClass('text-dark');

        loadTreeData();
        
        // Handle filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Update URL without page refresh
            updateUrlParameter('renstraID', renstraID);
            
            // Reload tree data
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
                // Clear container
                $('#tree-grid-container').html('<table id="tree-grid" class="table table-bordered"><thead><tr><th class="text-center" style="width: 5%;">No</th><th class="text-center">Nama</th><th class="text-center" style="width: 15%;">Actions</th></tr></thead><tbody></tbody></table>');
                
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
                    
                    if (item.tooltip) {
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
