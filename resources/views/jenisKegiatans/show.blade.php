<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($jenisKegiatan->Nama) !!}</td>
        </tr>
        <tr>
            <th>NA</th>
            <td>
            @if($jenisKegiatan->NA == 'Y')
                <span class="badge badge-danger">Non Aktif</span>
            @endif
            @if($jenisKegiatan->NA == 'N')
                <span class="badge badge-success">Aktif</span>
            @endif
            </td>
        </tr>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
