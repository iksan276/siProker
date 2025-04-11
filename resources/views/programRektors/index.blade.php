@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Program Rektor Management</h1>
<p class="mb-4">Manage all Program Rektor in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Program Rektor List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('program-rektors.create') }}" data-title="Tambah Program Rektor">
                <i class="fas fa-plus fa-sm"></i> Tambah Program Rektor
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="programRektorTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Program Pengembangan</th>
                        <th>Nama</th>
                        <th>Tahun</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programRektors as $index => $program)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $program->programPengembangan->Nama }}</td>
                        <td>{{ $program->Nama }}</td>
                        <td>{{ $program->Tahun }}</td>
                        <td>
                          
                            @if($program->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($program->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-info btn-circle btn-sm load-modal" data-url="{{ route('program-rektors.show', $program->ProgramRektorID) }}" data-title="Detail Program Rektor">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-circle btn-sm load-modal" data-url="{{ route('program-rektors.edit', $program->ProgramRektorID) }}" data-title="Edit Program Rektor">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('program-rektors.destroy', $program->ProgramRektorID) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-danger btn-circle btn-sm delete-confirm">
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
        $('#programRektorTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
