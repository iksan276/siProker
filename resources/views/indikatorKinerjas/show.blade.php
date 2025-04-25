<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th width="30%">ID</th>
            <td>{{ $indikatorKinerja->IndikatorKinerjaID }}</td>
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
            <th>Baseline</th>
            <td>{!! nl2br($indikatorKinerja->Baseline) !!}</td>
        </tr>
        <tr>
            <th>{{ $yearLabels[0] ?? '2025' }}</th>
            <td>{!! nl2br($indikatorKinerja->Tahun1) !!}</td>
        </tr>
        <tr>
            <th>{{ $yearLabels[1] ?? '2026' }}</th>
            <td>{!! nl2br($indikatorKinerja->Tahun2) !!}</td>
        </tr>
        <tr>
            <th>{{ $yearLabels[2] ?? '2027' }}</th>
            <td>{!! nl2br($indikatorKinerja->Tahun3) !!}</td>
        </tr>
        <tr>
            <th>{{ $yearLabels[3] ?? '2028' }}</th>
            <td>{!! nl2br($indikatorKinerja->Tahun4) !!}</td>
        </tr>
        <tr>
            <th>Mendukung IKU PT / Kriteria Akreditasi</th>
            <td>
                @if($indikatorKinerja->MendukungIKU == 'Y')
                    <span class="badge badge-success">Ya</span>
                @endif
                @if($indikatorKinerja->MendukungIKU == 'N')
                    <span class="badge badge-danger">Tidak</span>
                @endif
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
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
