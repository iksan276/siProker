@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Program Pengembangan Management</h1>
<p class="mb-4">Manage all Program Pengembangan in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Program Pengembangan List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('program-pengembangans.create') }}" data-title="Tambah Program Pengembangan">
                <i class="fas fa-plus fa-sm"></i> Tambah Program Pengembangan
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="programPengembanganTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Isu Strategis</th>
                        <th>Nama</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programPengembangans as $index => $program)
                    <tr class="{{ $program->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $program->isuStrategis->Nama }}</td>
                        <td>{{ $program->Nama }}</td>
                        <td>
                           
                            @if($program->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($program->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-info btn-circle btn-sm load-modal" data-url="{{ route('program-pengembangans.show', $program->ProgramPengembanganID) }}" data-title="Detail Program Pengembangan">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-circle btn-sm load-modal" data-url="{{ route('program-pengembangans.edit', $program->ProgramPengembanganID) }}" data-title="Edit Program Pengembangan">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('program-pengembangans.destroy', $program->ProgramPengembanganID) }}" method="POST" class="d-inline">
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
        $('#programPengembanganTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
