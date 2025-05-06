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
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
