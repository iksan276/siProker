@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Kegiatan Management</h1>
<p class="mb-4">Manage all Kegiatan in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Kegiatan List</h6>
        <div class="d-flex align-items-center">
          <div class="mr-2">
                <select id="indikatorKinerjaFilter" class="form-control  select2-filter">
                    <option value="">-- Pilih Indikator Kinerja --</option>
                    @foreach($indikatorKinerjas as $indikatorKinerja)
                        <option value="{{ $indikatorKinerja->IndikatorKinerjaID }}" {{ isset($selectedIndikatorKinerja) && $selectedIndikatorKinerja == $indikatorKinerja->IndikatorKinerjaID ? 'selected' : '' }}>
                            {{ $indikatorKinerja->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('kegiatans.create') }}" data-title="Tambah Kegiatan">
                <i class="fas fa-plus fa-sm"></i> Tambah Kegiatan
            </button>
            <a href="{{ route('kegiatans.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
            </div>
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
                        <td class="text-center">{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y') }}</td>
                        <td>{!! nl2br(Str::limit($kegiatan->RincianKegiatan, 50)) !!}</td>
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
        // Initialize DataTable
        var table = $('#kegiatanTable').DataTable({
            responsive: true
        });
        
        // Handle filter change
        $('#indikatorKinerjaFilter').on('change', function() {
            var indikatorKinerjaID = $(this).val();
            var url = new URL(window.location.href);
            
            // Update or remove the query parameter
            if (indikatorKinerjaID) {
                url.searchParams.set('indikatorKinerjaID', indikatorKinerjaID);
            } else {
                url.searchParams.delete('indikatorKinerjaID');
            }
            
            // Navigate to the filtered URL
            window.location.href = url.toString();
        });
    });
</script>
@endpush
