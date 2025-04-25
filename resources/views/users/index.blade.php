@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">User Management</h1>
<p class="mb-4">Manage all users in the system.</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Users List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('users.create') }}" data-title="Tambah User">
                <i class="fas fa-user-plus fa-sm"></i> Tambah User
            </button>
            <a href="{{ route('users.export.excel') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            <a href="{{ route('users.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf fa-sm"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">Email</th>
                        <th style="white-space:nowrap">Level</th>
                        <th style="white-space:nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $u)
                    <tr>
                        <td style="white-space:nowrap;width:1px" class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $u->name }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $u->email }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                                @if($u->level == 1)
                                    <span class="badge badge-primary">Admin</span>
                                @endif
                                @if($u->level == 2)
                                    <span class="badge badge-secondary">User</span>
                                @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <a href="#" class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('users.show', $u->id) }}" data-title="View User">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('users.edit', $u->id) }}" data-title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-square btn-sm delete-user" data-id="{{ $u->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Form Template (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    let userTable;
    
    $(document).ready(function () {
        // Initialize DataTable
        userTable = $('#userTable').DataTable({
            responsive: true,
            processing: true,
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            }
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
                        
                        // Reload only the DataTable
                        reloadTable();
                    } else {
                        // Display error message
                        showAlert('danger', response.message || 'An error occurred');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';
                    
                    for (var field in errors) {
                        errorMessage += errors[field][0] + '<br>';
                    }
                    
                    showAlert('danger', errorMessage || 'An error occurred');
                },
                complete: function() {
                    // Re-enable submit button
                    form.find('button[type="submit"]').prop('disabled', false).html('Save');
                }
            });
        });
        
        // Handle delete button click
        $(document).on('click', '.delete-user', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            var deleteUrl = "{{ route('users.destroy', ':id') }}".replace(':id', userId);
            
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
                              
                                // Reload only the DataTable
                                reloadTable();
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete user');
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
    
    // Function to reload DataTable
  // Function to reload DataTable
function reloadTable() {
    $.ajax({
        url: "{{ route('users.index') }}",
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            // Extract the table HTML from the response
            var newTableHtml = $(response).find('#userTable tbody').html();
            
            // Clear the current table and add the new data
            userTable.clear().destroy();
            $('#userTable tbody').html(newTableHtml);
            
            // Reinitialize DataTable with the same settings as initial load
            userTable = $('#userTable').DataTable({
                responsive: true,
                processing: true,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                },
                columnDefs: [
                    // No column (index 0)
                    { 
                        targets: 0,
                        className: 'text-center',
                        width: '1px',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // Email column (index 2)
                    { 
                        targets: 2,
                        className: 'text-center',
                        width: '1px',
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // Actions column (index 3)
                    { 
                        targets: 3,
                        className: 'text-center',
                        width: '1px',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    }
                ]
            });
        },
        error: function() {
            showAlert('danger', 'Failed to reload data');
        }
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
</script>

@endpush
