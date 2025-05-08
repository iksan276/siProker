@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Rencana Strategi (Renstra)</h1>
<p class="mb-4">Kelola Master Rencana Strategi (Renstra).</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
    <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 w-100">Renstra List</h6>
    <div class="d-flex flex-wrap w-100 w-md-auto justify-content-start justify-content-md-end">
          <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('renstras.create') }}" data-title="Tambah Rencana Strategi (Renstra)">
                <i class="fas fa-plus fa-sm"></i> Tambah Rencana Strategi (Renstra)
            </button>
        </div>
    </div>
</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="renstraTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap;width:1px">Periode Mulai</th>
                        <th style="white-space:nowrap;width:1px">Periode Selesai</th>
                        <th style="white-space:nowrap">NA</th>
                        <th style="white-space:nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renstras as $index => $renstra)
                    <tr class="{{ $renstra->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $index + 1 }}</td>
                        <td>{{ $renstra->Nama }}</td>
                        <td class="text-center">{{ $renstra->PeriodeMulai }}</td>
                        <td class="text-center">{{ $renstra->PeriodeSelesai }}</td>
                        <td style="white-space:nowrap;width:1px" class="text-center">
                            @if($renstra->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($renstra->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;width:1px" class="text-center">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('renstras.show', $renstra->RenstraID) }}" data-title="Detail Rencana Strategi (Renstra)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('renstras.edit', $renstra->RenstraID) }}" data-title="Edit Rencana Strategi (Renstra)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-square btn-sm delete-renstra" data-id="{{ $renstra->RenstraID }}">
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
    let renstraTable;
    
    $(document).ready(function () {
        // Initialize DataTable
        renstraTable = $('#renstraTable').DataTable({
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
        
        // Handle delete button click
        $(document).on('click', '.delete-renstra', function(e) {
            e.preventDefault();
            var renstraId = $(this).data('id');
            var deleteUrl = "{{ route('renstras.destroy', ':id') }}".replace(':id', renstraId);
            
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
                              
                                // Reload only the DataTable
                                reloadTable();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
        
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete renstra');
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
        url: "{{ route('renstras.index') }}",
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            // Extract the table HTML from the response
            var newTableHtml = $(response).find('#renstraTable tbody').html();
            
            // Clear the current table and add the new data
            renstraTable.clear().destroy();
            $('#renstraTable tbody').html(newTableHtml);
            
            // Reinitialize DataTable with the same settings as initial load
            renstraTable = $('#renstraTable').DataTable({
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
                        orderable: true,
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // Periode Mulai column (index 2)
                    { 
                        targets: 2,
                        className: 'text-center',
                        width: '1px',
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // Periode Selesai column (index 3)
                    { 
                        targets: 3,
                        className: 'text-center',
                        width: '1px',
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // NA column (index 4)
                    { 
                        targets: 4,
                        className: 'text-center',
                        width: '1px',
                        render: function(data, type, row, meta) {
                            return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                        }
                    },
                    // Actions column (index 5)
                    { 
                        targets: 5,
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
