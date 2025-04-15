@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Program Rektor Management</h1>
<p class="mb-4">Manage all Program Rektor in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Program Rektor List</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <select id="programPengembanganFilter" class="form-control  select2-filter">
                    <option value="">-- Pilih Program Pengembangan --</option>
                    @foreach($programPengembangans as $programPengembangan)
                        <option value="{{ $programPengembangan->ProgramPengembanganID }}" {{ isset($selectedProgramPengembangan) && $selectedProgramPengembangan == $programPengembangan->ProgramPengembanganID ? 'selected' : '' }}>
                            {{ $programPengembangan->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('program-rektors.create') }}" data-title="Tambah Program Rektor">
                <i class="fas fa-plus fa-sm"></i> Tambah Program Rektor
            </button>
            <a href="{{ route('program-rektors.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="programRektorTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Program Pengembangan</th>
                        <th>Tahun</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programRektors as $index => $program)
                    <tr class="{{ $program->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $index + 1 }}</td>
                        <td>{{ $program->Nama }}</td>
                        <td>{{ $program->programPengembangan->Nama }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $program->Tahun }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                          
                            @if($program->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($program->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('program-rektors.show', $program->ProgramRektorID) }}" data-title="Detail Program Rektor">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('program-rektors.edit', $program->ProgramRektorID) }}" data-title="Edit Program Rektor">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('program-rektors.destroy', $program->ProgramRektorID) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-square btn-sm delete-confirm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        var table = $('#programRektorTable').DataTable({
            responsive: true
        });
        
        // Handle Program Pengembangan filter change
        $('#programPengembanganFilter').on('change', function() {
            var programPengembanganID = $(this).val();
            var url = new URL(window.location.href);
            
            // Update or remove the query parameter
            if (programPengembanganID) {
                url.searchParams.set('programPengembanganID', programPengembanganID);
            } else {
                url.searchParams.delete('programPengembanganID');
            }
            
            // Navigate to the filtered URL
            window.location.href = url.toString();
        });
    });
</script>
@endpush
