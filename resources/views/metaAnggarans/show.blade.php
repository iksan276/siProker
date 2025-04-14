<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $metaAnggaran->MetaAnggaranID }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $metaAnggaran->Nama }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($metaAnggaran->NA == 'Y')
                    <span class="badge badge-danger">Non Aktif</span>
                @endif

                @if($metaAnggaran->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
</div>
