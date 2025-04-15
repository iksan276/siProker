@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Satuan Management</h1>
<p class="mb-4">Manage all Satuan in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Satuan List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('satuans.create') }}" data-title="Tambah Satuan">
                <i class="fas fa-plus fa-sm"></i> Tambah Satuan
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="satuanTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($satuans as $index => $satuan)
                    <tr class="{{ $satuan->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td style="white-space:nowrap;width:1px" class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $satuan->Nama }}</td>
                        <td class="text-center text-dark" style="white-space:nowrap;width:1px">
                            @if($satuan->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif

                            @if($satuan->NA == 'N')
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('satuans.show', $satuan->SatuanID) }}" data-title="Detail Satuan">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('satuans.edit', $satuan->SatuanID) }}" data-title="Edit Satuan">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('satuans.destroy', $satuan->SatuanID) }}" method="POST" class="d-inline">
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
        $('#satuanTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
