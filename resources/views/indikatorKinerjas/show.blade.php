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
            <th>{{ $yearLabels[4] ?? '2029' }}</th>
            <td>{!! nl2br($indikatorKinerja->Tahun5) !!}</td>
        </tr>
        <tr>
            <th>Mendukung IKU PT ?</th>
            <td>
                @if($indikatorKinerja->MendukungIKU == 'Y')
                    <span class="badge badge-success">Ya</span>
                @endif
                @if($indikatorKinerja->MendukungIKU == 'N')
                    <span class="badge badge-danger">Tidak</span>
                @endif
            </td>
        </tr>
            @if($indikatorKinerja->MendukungIKU == 'Y' && count($ikupts) > 0)
        <tr>
            <th>IKU PT</th>
            <td>
                <ul class="mb-0 pl-3">
                    @foreach($ikupts as $ikupt)
                        <li>{{ $ikupt->Nama }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif
        
          <tr>
            <th>Mendukung Kriteria Akreditasi ?</th>
            <td>
                @if($indikatorKinerja->MendukungKA == 'Y')
                    <span class="badge badge-success">Ya</span>
                @endif
                @if($indikatorKinerja->MendukungKA == 'N')
                    <span class="badge badge-danger">Tidak</span>
                @endif
            </td>
        </tr>
        
    
        @if($indikatorKinerja->MendukungKA == 'Y' && count($kriteriaAkreditasis) > 0)
        <tr>
            <th>Kriteria Akreditasi</th>
            <td>
                <ul class="mb-0 pl-3">
                    @foreach($kriteriaAkreditasis as $kriteria)
                        <li>{{ $kriteria->Nama }}</li>
                    @endforeach
                </ul>
            </td>
        </tr>
        @endif
        
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
        <tr>
            <th>Dibuat Pada</th>
            <td>{{ $indikatorKinerja->DCreated ? date('d-m-Y H:i:s', strtotime($indikatorKinerja->DCreated)) : '-' }}</td>
        </tr>
        <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $indikatorKinerja->createdBy ? $indikatorKinerja->createdBy->name : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Pada</th>
            <td>{{ $indikatorKinerja->DEdited ? date('d-m-Y H:i:s', strtotime($indikatorKinerja->DEdited)) : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Oleh</th>
            <td>{{ $indikatorKinerja->editedBy ? $indikatorKinerja->editedBy->name : '-' }}</td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>

