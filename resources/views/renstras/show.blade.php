<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Nama</th>
            <td>{{ $renstra->Nama }}</td>
        </tr>
        <tr>
            <th>Periode Mulai</th>
            <td>{{ $renstra->PeriodeMulai }}</td>
        </tr>
        <tr>
            <th>Periode Selesai</th>
            <td>{{ $renstra->PeriodeSelesai }}</td>
        </tr>
        <tr>
            <th>NA</th>
            <td>
            @if($renstra->NA == 'Y')
                                <span class="badge badge-danger">Non Aktif</span>
                            @endif
                            @if($renstra->NA == 'N')
                              <span class="badge badge-success">Aktif</span>
                            @endif
            </td>
        </tr>
  
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
