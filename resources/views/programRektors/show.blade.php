<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Program Pengembangan</th>
            <td>{{ $programRektor->programPengembangan->Nama }}</td>
        </tr>
        <tr>
            <th>Nama</th>
            <td>{{ $programRektor->Nama }}</td>
        </tr>
        <tr>
            <th>Tahun</th>
            <td>{{ $programRektor->Tahun }}</td>
        </tr>
        <tr>
            <th>NA</th>
            <td>
            @if($programRektor->NA == 'Y')
                <span class="badge badge-danger">Non Aktif</span>
            @endif
            @if($programRektor->NA == 'N')
                <span class="badge badge-success">Aktif</span>
            @endif
            </td>
        </tr>
       
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
</div>
