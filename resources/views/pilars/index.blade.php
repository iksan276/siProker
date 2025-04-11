@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Pilar Management</h1>
<p class="mb-4">Manage all Pilars in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pilar List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('pilars.create') }}" data-title="Tambah Pilar">
                <i class="fas fa-plus fa-sm"></i> Tambah Pilar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="pilarTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Renstra</th>
                        <th>Nama</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pilars as $index => $pilar)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pilar->renstra->Nama }}</td>
                        <td>{{ $pilar->Nama }}</td>
                        <td>
                          
                            @if($pilar->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($pilar->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                       
                        <td>
                            <button class="btn btn-info btn-circle btn-sm load-modal" data-url="{{ route('pilars.show', $pilar->PilarID) }}" data-title="Detail Pilar">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-circle btn-sm load-modal" data-url="{{ route('pilars.edit', $pilar->PilarID) }}" data-title="Edit Pilar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pilars.destroy', $pilar->PilarID) }}" method="POST" class="d-inline">
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
        $('#pilarTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
