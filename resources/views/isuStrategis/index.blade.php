@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Isu Strategis Management</h1>
<p class="mb-4">Manage all Isu Strategis in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Isu Strategis List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('isu-strategis.create') }}" data-title="Tambah Isu Strategis">
                <i class="fas fa-plus fa-sm"></i> Tambah Isu Strategis
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="isuStrategisTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Pilar</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($isuStrategis as $index => $isu)
                    <tr class="{{ $isu->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $index + 1 }}</td>
                        <td>{{ $isu->Nama }}</td>
                        <td>{{ $isu->pilar->Nama }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            @if($isu->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @else
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('isu-strategis.show', $isu->IsuID) }}" data-title="Detail Isu Strategis">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('isu-strategis.edit', $isu->IsuID) }}" data-title="Edit Isu Strategis">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('isu-strategis.destroy', $isu->IsuID) }}" method="POST" class="d-inline">
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
        $('#isuStrategisTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
