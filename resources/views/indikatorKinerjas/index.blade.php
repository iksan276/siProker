@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2">Indikator Kinerja Management</h1>
<p class="mb-4">Manage all Indikator Kinerja in the system.</p>

<!-- DataTales Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Indikator Kinerja List</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2">
                <select id="programRektorFilter" class="form-control  select2-filter">
                    <option value="">-- Pilih Program Rektor --</option>
                    @foreach($programRektors as $programRektor)
                        <option value="{{ $programRektor->ProgramRektorID }}" {{ isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID ? 'selected' : '' }}>
                            {{ $programRektor->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mr-2">
                <select id="unitFilter" class="form-control  select2-filter">
                    <option value="">-- Pilih Unit Terkait --</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->UnitID }}" {{ isset($selectedUnit) && $selectedUnit == $unit->UnitID ? 'selected' : '' }}>
                            {{ $unit->Nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
            <button class="btn btn-primary btn-sm load-modal" data-url="{{ route('indikator-kinerjas.create') }}" data-title="Tambah Indikator Kinerja">
                <i class="fas fa-plus fa-sm"></i> Tambah Indikator Kinerja
            </button>
            <a href="{{ route('indikator-kinerjas.export.excel', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel fa-sm"></i> Export Excel
            </a>
           </div>
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
                        <th>Satuan</th>
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
                        <td class="text-center">{{ $indikatorKinerja->Bobot }}%</td>
                        <td class="text-center" style="white-space:nowrap;width:1px">{{ $indikatorKinerja->satuan->Nama }}</td>
                        <td class="text-center">Rp {{ number_format($indikatorKinerja->HargaSatuan, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($indikatorKinerja->Jumlah, 0, ',', '.') }}</td>
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
                        <td>
                            @php
                                $unitTerkaits = \App\Models\Unit::whereIn('UnitID', explode(',', $indikatorKinerja->UnitTerkaitID))->pluck('Nama')->toArray();
                            @endphp
                            <ul class="mb-0">
                                @foreach($unitTerkaits as $unitTerkait)
                                    <li>{{ $unitTerkait }}</li>
                                @endforeach
                            </ul>
                        </td>
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
        // Initialize DataTable
        var table = $('#indikatorKinerjaTable').DataTable({
            responsive: true
        });
        
        // Handle Program Rektor filter change
        $('#programRektorFilter').on('change', function() {
            var programRektorID = $(this).val();
            var url = new URL(window.location.href);
            
            // Update or remove the query parameter
            if (programRektorID) {
                url.searchParams.set('programRektorID', programRektorID);
            } else {
                url.searchParams.delete('programRektorID');
            }
            
            // Preserve the unit filter if it exists
            var unitID = $('#unitFilter').val();
            if (unitID) {
                url.searchParams.set('unitID', unitID);
            }
            
            // Navigate to the filtered URL
            window.location.href = url.toString();
        });
        
        // Handle Unit filter change
        $('#unitFilter').on('change', function() {
            var unitID = $(this).val();
            var url = new URL(window.location.href);
            
            // Update or remove the query parameter
            if (unitID) {
                url.searchParams.set('unitID', unitID);
            } else {
                url.searchParams.delete('unitID');
            }
            
            // Preserve the program rektor filter if it exists
            var programRektorID = $('#programRektorFilter').val();
            if (programRektorID) {
                url.searchParams.set('programRektorID', programRektorID);
            }
            
            // Navigate to the filtered URL
            window.location.href = url.toString();
        });
    });
</script>
@endpush
