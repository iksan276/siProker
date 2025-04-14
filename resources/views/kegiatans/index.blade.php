@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Kegiatan Management</h1>
<p class="mb-4">Manage all Kegiatan in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kegiatan List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('kegiatans.create') }}" data-title="Tambah Kegiatan">
                <i class="fas fa-plus fa-sm"></i> Tambah Kegiatan
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="kegiatanTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Indikator Kinerja</th>
                        <th>Nama</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Rincian Kegiatan</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatans as $index => $kegiatan)
                    <tr>
                        <td style="white-space:nowrap;width:1px" class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $kegiatan->indikatorKinerja->Nama }}</td>
                        <td>{{ $kegiatan->Nama }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y') }}</td>
                        <td>{{ Str::limit($kegiatan->RincianKegiatan, 50) }}</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('kegiatans.show', $kegiatan->KegiatanID) }}" data-title="Detail Kegiatan">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('kegiatans.edit', $kegiatan->KegiatanID) }}" data-title="Edit Kegiatan">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('kegiatans.destroy', $kegiatan->KegiatanID) }}" method="POST" class="d-inline">
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
        $('#kegiatanTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
