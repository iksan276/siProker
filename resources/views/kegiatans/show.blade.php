<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $kegiatan->KegiatanID }}</td>
        </tr>
        <tr>
            <th>Renstra</th>
            <td>{{ $kegiatan->programRektor->programPengembangan->isuStrategis->pilar->renstra->Nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Pilar</th>
            <td>{{ $kegiatan->programRektor->programPengembangan->isuStrategis->pilar->Nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Isu Strategis</th>
            <td>{{ $kegiatan->programRektor->programPengembangan->isuStrategis->Nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Program Pengembangan</th>
            <td>{{ $kegiatan->programRektor->programPengembangan->Nama ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Program Rektor</th>
            <td>{!! nl2br($kegiatan->programRektor->Nama) !!}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($kegiatan->Nama) !!}</td>
        </tr>
        <tr>
            <th>Tanggal Mulai</th>
            <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Selesai</th>
            <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Rincian Kegiatan</th>
            <td>{!! nl2br($kegiatan->RincianKegiatan) !!}</td>
        </tr>
     
            <tr>
                <th>Feedback</th>
                <td>{!! nl2br($kegiatan->Feedback) !!}</td>
            </tr>
    </table>
</div>


<!-- Kegiatan Info Display -->
<div class="alert alert-info mt-2 py-2">
    <div class="d-flex align-items-center">
        <div class="mr-3">
            <span class="badge badge-primary">Info Kegiatan</span>
        </div>
        <div class="d-flex flex-wrap">
            <div class="mr-3"><small><strong>Total Keseluruhan Anggaran RAB:</strong> {{ $kegiatan->FormattedTotalRABAmount }}</small></div>
            <div class="mr-3"><small><strong>Sisa Anggaran Untuk Pengajuan RAB:</strong> Rp {{ number_format(($kegiatan->programRektor->Total ?? 0) - $kegiatan->getTotalRABAmount(), 0, ',', '.') }}</small></div>
        </div>
    </div>
</div>

<!-- Program Rektor Info Display -->
<div class="alert alert-info mt-2 py-2">
    <div class="d-flex align-items-center">
        <div class="mr-3">
            <span class="badge badge-primary">Info Program Rektor</span>
        </div>
        <div class="d-flex flex-wrap">
            <div class="mr-3"><small><strong>Jumlah:</strong> {{ $kegiatan->programRektor->JumlahKegiatan ?? '-' }}</small></div>
            <div class="mr-3"><small><strong>Satuan:</strong> {{ $kegiatan->programRektor->satuan->Nama ?? '-' }}</small></div>
            <div class="mr-3"><small><strong>Harga:</strong> Rp {{ number_format($kegiatan->programRektor->HargaSatuan ?? 0, 0, ',', '.') }}</small></div>
            <div class="mr-3"><small><strong>Total:</strong> Rp {{ number_format($kegiatan->programRektor->Total ?? 0, 0, ',', '.') }}</small></div>
            <div><small><strong>Penanggung Jawab:</strong> {{ $penanggungJawabName }}</small></div>
        </div>
    </div>
</div>


@if($kegiatan->subKegiatans->count() > 0)
<div class="mt-4">
    <label class="font-weight-bold">Sub Kegiatan</label>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jadwal Mulai</th>
                    <th>Jadwal Selesai</th>
                    <th>Catatan</th>
                    <th>Feedback</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan->subKegiatans as $index => $subKegiatan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{!! nl2br($subKegiatan->Nama) !!}</td>
                    <td>{{ \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y') }}</td>
                    <td>{!! nl2br($subKegiatan->Catatan) !!}</td>
                    <td>{!! nl2br($subKegiatan->Feedback) !!}</td>
                    <td>{!! $subKegiatan->StatusLabel !!}</td>
                </tr>
                @if($subKegiatan->rabs->count() > 0)
                <tr>
                    <td colspan="7" class="bg-light">
                        <strong>RAB untuk Sub Kegiatan: {{ $subKegiatan->Nama }}</strong>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Komponen</th>
                                        <th>Volume</th>
                                        <th>Satuan</th>
                                        <th>Harga Satuan</th>
                                        <th>Jumlah</th>
                                        <th>Feedback</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subKegiatan->rabs as $rabIndex => $rab)
                                    <tr>
                                        <td>{{ $rabIndex + 1 }}</td>
                                        <td>{{ $rab->Komponen }}</td>
                                        <td class="text-right">{{ number_format($rab->Volume, 0, ',', '.') }}</td>
                                        <td>{{ $rab->satuanRelation->Nama ?? '' }}</td>
                                        <td class="text-right">{{ $rab->FormattedHargaSatuan }}</td>
                                        <td class="text-right">{{ $rab->FormattedJumlah }}</td>
                                        <td>{!! nl2br($rab->Feedback) !!}</td>
                                        <td>{!! $rab->StatusLabel !!}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="5" class="text-right">Total</td>
                                        <td class="text-right">Rp {{ number_format($subKegiatan->rabs->whereIn('Status', ['Y', 'N'])->sum('Jumlah'), 0, ',', '.') }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($kegiatan->rabs->whereNull('SubKegiatanID')->count() > 0)
<div class="mt-4">
    <label class="font-weight-bold">RAB Kegiatan</label>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Komponen</th>
                    <th>Volume</th>
                    <th>Satuan</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Feedback</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan->rabs->whereNull('SubKegiatanID') as $index => $rab)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $rab->Komponen }}</td>
                    <td class="text-right">{{ number_format($rab->Volume, 0, ',', '.') }}</td>
                    <td>{{ $rab->satuanRelation->Nama ?? '' }}</td>
                    <td class="text-right">{{ $rab->FormattedHargaSatuan }}</td>
                    <td class="text-right">{{ $rab->FormattedJumlah }}</td>
                    <td>{!! nl2br($rab->Feedback) !!}</td>
                    <td>{!! $rab->StatusLabel !!}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($kegiatan->rabs->whereNull('SubKegiatanID')->whereIn('Status', ['Y', 'N'])->sum('Jumlah'), 0, ',', '.') }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="mt-4">
    <label class="font-weight-bold">Total Keseluruhan Anggaran RAB</label>
    <div class="alert alert-info">
        <h4 class="mb-0">{{ $kegiatan->FormattedTotalRABAmount }}</h4>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>

<script>
     setTimeout(function() {
            $('.alert').alert('close');
        }, 10000000);
</script>