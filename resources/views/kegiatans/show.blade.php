<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $kegiatan->KegiatanID }}</td>
        </tr>
        <tr>
            <th>Indikator Kinerja</th>
            <td>{!! nl2br($kegiatan->indikatorKinerja->Nama) !!}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($kegiatan->Nama) !!}</td>
        </tr>
        <tr>
            <th>Tanggal Mulai</th>
            <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <th>Tanggal Selesai</th>
            <td>{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y H:i') }}</td>
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
