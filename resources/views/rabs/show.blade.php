<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Kegiatan</th>
                            <td>{{ $rab->kegiatan ? $rab->kegiatan->Nama : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sub Kegiatan</th>
                            <td>{{ $rab->subKegiatan ? $rab->subKegiatan->Nama : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Komponen</th>
                            <td>{{ $rab->Komponen }}</td>
                        </tr>
                        <tr>
                            <th>Volume</th>
                            <td>{{ number_format($rab->Volume, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Satuan</th>
                            <td>{{ $rab->satuanRelation ? $rab->satuanRelation->Nama : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Harga Satuan</th>
                            <td>Rp {{ number_format($rab->HargaSatuan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($rab->Jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Feedback</th>
                            <td>{!! nl2br($rab->Feedback) !!}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{!! $rab->status_label !!}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $rab->createdBy ? $rab->createdBy->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $rab->DCreated ? \Carbon\Carbon::parse($rab->DCreated)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diubah Oleh</th>
                            <td>{{ $rab->editedBy ? $rab->editedBy->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diubah</th>
                            <td>{{ $rab->DEdited ? \Carbon\Carbon::parse($rab->DEdited)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
