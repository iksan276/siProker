<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Pilar</th>
            <td>{!! nl2br($isuStrategis->pilar->Nama) !!}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{!! nl2br($isuStrategis->Nama) !!}</td>
        </tr>
        <tr>
            <th>NA</th>
            <td>
                @if($isuStrategis->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($isuStrategis->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
            </td>
        </tr>
       
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
