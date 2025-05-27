@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Program Pengembangan (PP)</h1>
<p class="mb-4">Kelola Master Program Pengembangan (PP).</p>

<!-- Alert Container for AJAX responses -->
<div id="alertContainer"></div>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 w-100">Program Pengembangan List</h6>
        <div class="d-flex flex-wrap w-100 w-md-auto justify-content-start justify-content-md-end">
        <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('program-pengembangans.create') }}" data-title="Tambah Program Pengembangan (PP)">
                <i class="fas fa-plus fa-sm"></i> Tambah Program Pengembangan
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
        
        <div class="form-group mb-5">
            <select id="isuFilter" class="form-control select2-filter" {{ empty($selectedPilar) ? 'disabled' : '' }}>
                <option value="">-- Pilih Isu Strategis --</option>
                @foreach($isuStrategis as $isu)
                    <option value="{{ $isu->IsuID }}" {{ isset($selectedIsu) && $selectedIsu == $isu->IsuID ? 'selected' : '' }}>
                        {{ $isu->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered" id="programPengembanganTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th style="white-space:nowrap">No</th>
                        <th style="white-space:nowrap">Nama</th>
                        <th style="white-space:nowrap">NA</th>
                        <th style="white-space:nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable will populate this -->
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
    let programTable;
    var isFiltering = false;
    var selectedRenstraId = getCookie('selected_renstra') || "{{ $selectedRenstra ?? '' }}";
    var selectedPilarId = getCookie('selected_pilar') || "{{ $selectedPilar ?? '' }}";
    var selectedIsuId = getCookie('selected_isu') || "{{ $selectedIsu ?? '' }}";
    
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
        }
        
        // Initialize DataTable
        initDataTable();
        
        // Handle renstra filter change
        $('#renstraFilter').on('change', function() {
            var renstraID = $(this).val();
            
            // Store selected Renstra ID in global variable and cookie
            selectedRenstraId = renstraID;
            
            // Reset pilar and isu filters
            $('#pilarFilter').empty().append('<option value="">-- Pilih Pilar --</option>');
            $('#pilarFilter').val('').prop('disabled', true);
            
            $('#isuFilter').empty().append('<option value="">-- Pilih Isu Strategis --</option>');
            $('#isuFilter').val('').prop('disabled', true);
            
            // Clear selections if renstra is cleared
            if (!renstraID) {
                selectedPilarId = '';
                selectedIsuId = '';
                eraseCookie('selected_renstra');
                eraseCookie('selected_pilar');
                eraseCookie('selected_isu');
                updateUrlParameter('renstraID', null);
                updateUrlParameter('pilarID', null);
                updateUrlParameter('isuID', null);
                
                // Reload DataTable with no filters
                isFiltering = true;
                programTable.ajax.reload(function() {
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
                                eraseCookie('selected_pilar');
                                eraseCookie('selected_isu');
                                updateUrlParameter('pilarID', null);
                                updateUrlParameter('isuID', null);
                            }
                        }
                    }
                    
                    // Reload DataTable with new filter
                    programTable.ajax.reload(function() {
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
            
            // Reset isu filter
            $('#isuFilter').empty().append('<option value="">-- Pilih Isu Strategis --</option>');
            $('#isuFilter').val('').prop('disabled', true);
            
            // Clear isu selection if pilar is cleared
            if (!pilarID) {
                selectedIsuId = '';
                eraseCookie('selected_pilar');
                eraseCookie('selected_isu');
                updateUrlParameter('pilarID', null);
                updateUrlParameter('isuID', null);
                
                // Reload DataTable with current filters
                isFiltering = true;
                programTable.ajax.reload(function() {
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
            
            // Reload DataTable with new filter
            programTable.ajax.reload(function() {
                isFiltering = false;
            });
        });
        
        // Handle isu strategis filter change
        $('#isuFilter').on('change', function() {
            var isuID = $(this).val();
            
            // Store selected Isu ID in global variable and cookie
            selectedIsuId = isuID;
            
            if (isuID) {
                setCookie('selected_isu', isuID, 30);
                updateUrlParameter('isuID', isuID);
            } else {
                eraseCookie('selected_isu');
                updateUrlParameter('isuID', null);
            }
            
            // Set filtering flag to true
            isFiltering = true;
            
            // Reload DataTable with new filter
            programTable.ajax.reload(function() {
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
                        
                        // Reload only the DataTable
                        programTable.ajax.reload();
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
        $(document).on('click', '.delete-program', function(e) {
            e.preventDefault();
            var programId = $(this).data('id');
            var deleteUrl = "{{ route('program-pengembangans.destroy', ':id') }}".replace(':id', programId);
            
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
                                programTable.ajax.reload();
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: response.message || 'Item has been successfully deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete program pengembangan');
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
                        }
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Failed to load isu strategis');
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
    
    function initDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#programPengembanganTable')) {
            $('#programPengembanganTable').DataTable().destroy();
        }
        
        // Initialize DataTable with AJAX
        programTable = $('#programPengembanganTable').DataTable({
            processing: true,
            serverSide: true, // We're handling the data ourselves
            ajax: {
                url: '{{ route('program-pengembangans.index') }}',
                type: 'GET',
                data: function(d) {
                    d.renstraID = selectedRenstraId; // Use the global variable
                    d.pilarID = selectedPilarId; // Use the global variable
                    d.isuID = selectedIsuId; // Use the global variable
                    d.wantsJson = true;
                },
                // Show processing only during filtering
                beforeSend: function() {
                    if (!isFiltering) {
                        $('#programPengembanganTable_processing').hide();
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
                { 
                    data: 'na', 
                    className: 'text-center',
                    width: '1px',
                    render: function(data) {
                        return '<span style="white-space:nowrap;width:1px">' + data + '</span>';
                    }
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
            // Apply row class for inactive items
            createdRow: function(row, data, dataIndex) {
                if (data.row_class) {
                    $(row).addClass(data.row_class);
                }
            },
            // Hide processing indicator for all operations except filtering
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
            // Override the default processing display behavior
            preDrawCallback: function() {
                if (!isFiltering) {
                    $('#programPengembanganTable_processing').hide();
                }
                return true;
            }
        });
        
        // Additional override to hide processing indicator for pagination, sorting, etc.
        $('#programPengembanganTable').on('page.dt search.dt order.dt', function() {
            if (!isFiltering) {
                $('#programPengembanganTable_processing').hide();
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
            
            $('#mainModalLabel').text(title);
            $('#mainModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#mainModal').modal('show');
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#mainModal .modal-body').html(response);
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr);
                    $('#mainModal .modal-body').html('<div class="alert alert-danger">Error loading content</div>');
                }
            });
        });
        
        // Re-initialize delete confirmation for dynamically added buttons
        $('.delete-program').off('click').on('click', function(e) {
            e.preventDefault();
            var programId = $(this).data('id');
            var deleteUrl = "{{ route('program-pengembangans.destroy', ':id') }}".replace(':id', programId);
            
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
                                showAlert('success', response.message || 'Program Pengembangan berhasil dihapus');
                                
                                // Reload DataTable
                                programTable.ajax.reload();
                            } else {
                                // Show error message
                                showAlert('danger', response.message || 'Failed to delete Program Pengembangan');
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
            $('#programPengembanganTable_processing').hide();
        }
    });
    
    // Make sure processing indicator is hidden when page loads
    $(window).on('load', function() {
        if (!isFiltering) {
            setTimeout(function() {
                $('#programPengembanganTable_processing').hide();
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
                    // Update URL parameter
                    updateUrlParameter('isuID', storedIsuId);
                }, 1000);
            }
        }
    });
</script>
@endpush
