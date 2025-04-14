<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $indikatorKinerja->IndikatorKinerjaID }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $indikatorKinerja->Nama }}</td>
        </tr>
        <tr>
            <th>Program Rektor</th>
            <td>{{ $indikatorKinerja->programRektor->Nama }}</td>
        </tr>
        <tr>
            <th>Satuan</th>
            <td>{{ $indikatorKinerja->satuan->Nama }}</td>
        </tr>
        <tr>
            <th>Bobot</th>
            <td>{{ $indikatorKinerja->Bobot }}</td>
        </tr>
        <tr>
            <th>Harga Satuan</th>
            <td>{{ number_format($indikatorKinerja->HargaSatuan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td>{{ $indikatorKinerja->Jumlah }}</td>
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
            <td>{{ $indikatorKinerja->unitTerkait->Nama }}</td>
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
