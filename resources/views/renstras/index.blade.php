@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Renstra Management</h1>
<p class="mb-4">Manage all Renstra in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Renstra List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('renstras.create') }}" data-title="Tambah Renstra">
                <i class="fas fa-plus fa-sm"></i> Tambah Renstra
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="renstraTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Periode Mulai</th>
                        <th>Periode Selesai</th>
                        <th>NA</th>
                   
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($renstras as $index => $renstra)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $renstra->Nama }}</td>
                        <td>{{ $renstra->PeriodeMulai }}</td>
                        <td>{{ $renstra->PeriodeSelesai }}</td>
                        <td>
                            @if($renstra->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($renstra->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                    
                        <td>
                            <button class="btn btn-info btn-circle btn-sm load-modal" data-url="{{ route('renstras.show', $renstra->RenstraID) }}" data-title="Detail Renstra">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-circle btn-sm load-modal" data-url="{{ route('renstras.edit', $renstra->RenstraID) }}" data-title="Edit Renstra">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('renstras.destroy', $renstra->RenstraID) }}" method="POST" class="d-inline">
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
        $('#renstraTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
