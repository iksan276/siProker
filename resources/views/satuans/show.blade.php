<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>{{ $satuan->SatuanID }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $satuan->Nama }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                @if($satuan->NA == 'Y')
                    <span class="badge badge-danger">Non-Aktif</span>
                @endif

                @if($satuan->NA == 'N')
                    <span class="badge badge-success">Aktif</span>
                @endif
            </td>
        </tr>
        
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
</div>
