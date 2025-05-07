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
    </table>
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
                    <td>{!! $subKegiatan->StatusLabel !!}</td>
                </tr>
                @if($subKegiatan->rabs->count() > 0)
                <tr>
                    <td colspan="5" class="bg-light">
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
                                        <td>{!! $rab->StatusLabel !!}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="font-weight-bold">
                                        <td colspan="5" class="text-right">Total</td>
                                        <td class="text-right">Rp {{ number_format($subKegiatan->rabs->sum('Jumlah'), 0, ',', '.') }}</td>
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
                    <td>{!! $rab->StatusLabel !!}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <td colspan="5" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($kegiatan->rabs->whereNull('SubKegiatanID')->sum('Jumlah'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="mt-4">
    <label class="font-weight-bold">Total Anggaran</label>
    <div class="alert alert-info">
        <h4 class="mb-0">{{ $kegiatan->FormattedTotalRABAmount }}</h4>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
