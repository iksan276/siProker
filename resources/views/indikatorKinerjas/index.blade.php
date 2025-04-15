@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Indikator Kinerja Management</h1>
<p class="mb-4">Manage all Indikator Kinerja in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Indikator Kinerja List</h6>
        <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('indikator-kinerjas.create') }}" data-title="Tambah Indikator Kinerja">
                <i class="fas fa-plus fa-sm"></i> Tambah Indikator Kinerja
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="indikatorKinerjaTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center text-dark">
                        <th>No</th>
                        <th>Nama</th>
                        <th>Program Rektor</th>
                        <th>Bobot</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Meta Anggaran</th>
                        <th>Unit Terkait</th>
                        <th>NA</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($indikatorKinerjas as $index => $indikatorKinerja)
                    <tr class="{{ $indikatorKinerja->NA == 'Y' ? 'bg-light text-muted' : '' }}">
                        <td style="white-space:nowrap;width:1px" class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $indikatorKinerja->Nama }}</td>
                        <td>{{ $indikatorKinerja->programRektor->Nama }}</td>
                        <td class="text-center">{{ $indikatorKinerja->Bobot }}</td>
                        <td class="text-right">{{ number_format($indikatorKinerja->HargaSatuan, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $indikatorKinerja->Jumlah }}</td>
                        <td>
                             @php
                                $metaAnggarans = \App\Models\MetaAnggaran::whereIn('MetaAnggaranID', explode(',', $indikatorKinerja->MetaAnggaranID))->pluck('Nama')->toArray();
                            @endphp
                            <ul class="mb-0">
                                @foreach($metaAnggarans as $metaAnggaran)
                                    <li>{{ $metaAnggaran }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $indikatorKinerja->unitTerkait->Nama }}</td>
                        <td class="text-center text-dark" style="white-space:nowrap;width:1px">
                            @if($indikatorKinerja->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif

                            @if($indikatorKinerja->NA == 'N')
                                <span class="badge badge-success">Aktif</span>
                            @endif
                        </td>
                        <td class="text-center" style="white-space:nowrap;width:1px">
                            <button class="btn btn-info btn-square btn-sm load-modal" data-url="{{ route('indikator-kinerjas.show', $indikatorKinerja->IndikatorKinerjaID) }}" data-title="Detail Indikator Kinerja">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-square btn-sm load-modal" data-url="{{ route('indikator-kinerjas.edit', $indikatorKinerja->IndikatorKinerjaID) }}" data-title="Edit Indikator Kinerja">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('indikator-kinerjas.destroy', $indikatorKinerja->IndikatorKinerjaID) }}" method="POST" class="d-inline">
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
        $('#indikatorKinerjaTable').DataTable({
            responsive: true
        });
    });
</script>
@endpush
