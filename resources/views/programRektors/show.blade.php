<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Program Pengembangan</th>
            <td>{!! nl2br($programRektor->programPengembangan->Nama) !!}</td>
        </tr>
        <tr>
            <th>Indikator Kinerja</th>
            <td>{!! nl2br($programRektor->indikatorKinerja->Nama) !!}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($programRektor->Nama) !!}</td>
        </tr>
        <tr>
            <th>Output</th>
            <td>{!! nl2br($programRektor->Output) !!}</td>
        </tr>
        <tr>
            <th>Outcome</th>
            <td>{!! nl2br($programRektor->Outcome) !!}</td>
        </tr>
        <tr>
            <th>Jenis Kegiatan</th>
            <td>{{ $programRektor->jenisKegiatan->Nama }}</td>
        </tr>
        <tr>
            <th>Mata Anggaran</th>
            <td>
                <ul class="mb-0 pl-3">
                    @foreach($mataAnggarans as $mataAnggaran)
                        <li>{{ $mataAnggaran->Nama }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <th>Jumlah Kegiatan</th>
            <td>{{ $programRektor->JumlahKegiatan }}</td>
        </tr>
        <tr>
            <th>Satuan</th>
            <td>{{ $programRektor->satuan->Nama }}</td>
        </tr>
        <tr>
            <th>Harga Satuan</th>
            <td>Rp {{ number_format($programRektor->HargaSatuan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td>Rp {{ number_format($programRektor->Total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Penanggung Jawab</th>
            <td>{{ $penanggungJawab['Nama'] }}</td>
        </tr>
        <tr>
            <th>Pelaksana</th>
            <td>
                <ul class="mb-0 pl-3">
                    @foreach($pelaksanas as $pelaksana)
                        <li>{{ $pelaksana['Nama'] }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
            @if($programRektor->NA == 'Y')
                <span class="badge badge-danger">Non Aktif</span>
            @endif
            @if($programRektor->NA == 'N')
                <span class="badge badge-success">Aktif</span>
            @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
