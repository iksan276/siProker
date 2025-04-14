@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Unit Management</h1>
<p class="mb-4">Manage all Units in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Unit List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('units.create') }}" data-title="Tambah Unit">
                <i class="fas fa-plus fa-sm"></i> Tambah Unit
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="unitTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $index => $unit)
                    <tr class="{{ $unit->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td style="white-space:nowrap;width:1px" class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $unit->Nama }}</td>
                        <td class="text-center text-dark" style="white-space:nowrap;width:1px">
                            @if($unit->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif

                            @if($unit->NA == 'N')
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('units.show', $unit->UnitID) }}" data-title="Detail Unit">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('units.edit', $unit->UnitID) }}" data-title="Edit Unit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('units.destroy', $unit->UnitID) }}" method="POST" class="d-inline">
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
        $('#unitTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
