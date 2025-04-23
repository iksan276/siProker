<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $indikatorKinerja->IndikatorKinerjaID }}</td>
        </tr>
        <tr>
            <th>Program Rektor</th>
            <td>{!! nl2br($indikatorKinerja->programRektor->Nama) !!}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($indikatorKinerja->Nama) !!}</td>
        </tr>
        <tr>
            <th>Satuan</th>
            <td>{{ $indikatorKinerja->satuan->Nama }}</td>
        </tr>
        <tr>
            <th>Bobot</th>
            <td>{{ $indikatorKinerja->Bobot }}%</td>
        </tr>
        <tr>
            <th>Harga Satuan</th>
            <td>Rp{{ number_format($indikatorKinerja->HargaSatuan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ number_format($indikatorKinerja->Jumlah, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Meta Anggaran</th>
            <td>
                @foreach($metaAnggarans as $metaAnggaran)
                    <span class="badge badge-primary">{{ $metaAnggaran->Nama }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Unit Terkait</th>
            <td>
                @foreach($unitTerkaits as $unitTerkait)
                    <span class="badge badge-info">{{ $unitTerkait->Nama }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($indikatorKinerja->NA == 'Y')
                    <span class="badge badge-danger">Non Aktif</span>
                @endif

                @if($indikatorKinerja->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
</div>
