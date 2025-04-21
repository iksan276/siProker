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


</style>
@endpush


@push('scripts')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    // Add this to your document ready function in the scripts section
$(document).on('mouseenter', '#tree-grid tbody tr', function() {
    $(this).css('background-color', '#edf1ff');
    $(this).css('cursor', 'pointer');
});

$(document).on('mouseleave', '#tree-grid tbody tr', function() {
    $(this).css('background-color', '');
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
        
           
        // Handle tooltip clicks
        $(document).on('click', '.node-name', function(e) {
            e.stopPropagation();
            
            // Hide any existing tooltips
            $('.tooltip').remove();
            
            // Show tooltip for this element
            var $this = $(this);
            $this.tooltip('show');
            
            // Hide tooltip when clicking elsewhere
            $(document).one('click', function() {
                $this.tooltip('hide');
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
                    // Disable submit button and show loading indicator
                    form.find('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
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
        
        // Handle tree node expansion/collapse
        $(document).on('click', '.node-expander', function(e) {
            e.stopPropagation();
            var nodeId = $(this).closest('tr').data('node-id');
            var isExpanded = $(this).hasClass('expanded');
            var level = $(this).closest('tr').data('level');
            var nodeType = nodeTypes[nodeId] || '';
            
            if (isExpanded) {
                // Collapse this node
                collapseNode(nodeId);
                $(this).removeClass('expanded');
                $(this).html('<i class="fas fa-chevron-right text-primary"></i>');
                
                // Remove from expanded nodes
                delete expandedNodes[nodeId];
            } else {
                // Collapse all other nodes at the same level with the same parent
                var parentId = nodeRelationships[nodeId];
                
                // Find all nodes at the same level with the same parent
                $('tr[data-level="' + level + '"]').each(function() {
                    var otherNodeId = $(this).data('node-id');
                    var otherParentId = nodeRelationships[otherNodeId];
                    
                    // If it's a different node but has the same parent (or both are top-level)
                    if (otherNodeId !== nodeId && otherParentId === parentId) {
                        // Find the expander for this node
                        var $otherExpander = $(this).find('.node-expander');
                        
                        // If it's expanded, collapse it
                        if ($otherExpander.hasClass('expanded')) {
                            collapseNodeAndAllChildren(otherNodeId);
                            $otherExpander.removeClass('expanded');
                            $otherExpander.html('<i class="fas fa-chevron-right text-primary"></i>');
                            
                            // Remove from expanded nodes
                            delete expandedNodes[otherNodeId];
                        }
                    }
                });
                
                // Expand this node
                expandNode(nodeId);
                $(this).addClass('expanded');
                $(this).html('<i class="fas fa-chevron-down text-primary"></i>');
                
                // Add to expanded nodes
                expandedNodes[nodeId] = true;
            }
            
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
                
                // Process tree data to update tooltips based on has_children
                treeData.forEach(function(item) {
                    // Update tooltip based on node type and has_children
                    if (item.type === 'pilar') {
                        item.tooltip = item.has_children ? 'Lihat isu strategis' : 'Belum ada isu strategis';
                    } else if (item.type === 'isu') {
                        item.tooltip = item.has_children ? 'Lihat program pengembangan' : 'Belum ada program pengembangan';
                    } else if (item.type === 'program') {
                        item.tooltip = item.has_children ? 'Lihat program rektor' : 'Belum ada program rektor';
                    } else if (item.type === 'rektor') {
                        item.tooltip = item.has_children ? 'Lihat indikator kinerja' : 'Belum ada indikator kinerja';
                    } else if (item.type === 'indikator') {
                        item.tooltip = item.has_children ? 'Lihat kegiatan' : 'Belum ada kegiatan';
                    } else if (item.type === 'kegiatan') {
                        item.tooltip = 'Detail kegiatan';
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
                    
                    // Add expander if has children (without type-specific icons)
                    var expander = '';
                    if (item.has_children) {
                        var expanderIcon = expandedNodes[item.id] ? 
                            '<i class="fas fa-chevron-down text-primary"></i>' : 
                            '<i class="fas fa-chevron-right text-primary"></i>';
                        expander = '<span class="node-expander ' + (expandedNodes[item.id] ? 'expanded' : '') + '" data-node-id="' + item.id + '">' + expanderIcon + '</span>';
                    }
                    
                    // Create name cell with tooltip that shows on click
                    var nameText = '';
                    var indentPrefix = '';
                    
                    // Add visual indentation markers based on level
                    for (var i = 0; i < item.level; i++) {
                        indentPrefix += '<span class="tree-indent">&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                    }
                    
                    if (item.tooltip) {
                        nameText = indentPrefix + '<span class="node-name" data-toggle="tooltip" data-trigger="manual" title="' + item.tooltip + '">' + item.nama + '</span>';
                    } else {
                        nameText = indentPrefix + item.nama;
                    }
                    
                    var nameCell = '<td>' + nameText + "&nbsp;&nbsp;" + expander + '</td>';
                    row.append(nameCell);
                    row.append('<td class="text-center" style="white-space:nowrap;width:1px;">' + (item.actions || '') + '</td>');
                    
                    tableBody.append(row);
                });
                
                // Initialize tooltips with manual trigger
                $('.node-name[data-toggle="tooltip"]').tooltip({
                    trigger: 'manual'
                });
                
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
            $('tr[data-node-id="' + nodeId + '"] .node-expander')
                .addClass('expanded')
                .html('<i class="fas fa-chevron-down text-primary"></i>');
        });
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
