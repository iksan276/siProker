<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th width="30%">ID</th>
            <td>{{ $ikupt->IKUPTID }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $ikupt->Nama }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($ikupt->NA == 'Y')
                    <span class="badge badge-danger">Non Aktif</span>
                @endif

                @if($ikupt->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Dibuat Pada</th>
            <td>{{ $ikupt->DCreated ? date('d-m-Y H:i:s', strtotime($ikupt->DCreated)) : '-' }}</td>
        </tr>
        <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $ikupt->createdBy ? $ikupt->createdBy->name : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Pada</th>
            <td>{{ $ikupt->DEdited ? date('d-m-Y H:i:s', strtotime($ikupt->DEdited)) : '-' }}</td>
        </tr>
        <tr>
            <th>Diubah Oleh</th>
            <td>{{ $ikupt->editedBy ? $ikupt->editedBy->name : '-' }}</td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
