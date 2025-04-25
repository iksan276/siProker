<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $mataAnggaran->MataAnggaranID }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $mataAnggaran->Nama }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($mataAnggaran->NA == 'Y')
                    <span class="badge badge-danger">Non Aktif</span>
                @endif

                @if($mataAnggaran->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
</div>
