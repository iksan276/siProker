<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th width="30%">ID</th>
            <td>{{ $kriteria->KriteriaAkreditasiID }}</td>
        </tr>
         <tr>
            <th>Key</th>
            <td>{{ $kriteria->Key }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $kriteria->Nama }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($kriteria->NA == 'Y')
                    <span class="badge badge-danger">Non Aktif</span>
                @endif

                @if($kriteria->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Dibuat Pada</th>
            <td>{{ $kriteria->DCreated ? date('d-m-Y H:i:s', strtotime($kriteria->DCreated)) : '-' }}</td>
        </tr>
        <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $kriteria->createdBy ? $kriteria->createdBy->name : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Pada</th>
            <td>{{ $kriteria->DEdited ? date('d-m-Y H:i:s', strtotime($kriteria->DEdited)) : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Oleh</th>
            <td>{{ $kriteria->editedBy ? $kriteria->editedBy->name : '-' }}</td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
