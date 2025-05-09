<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Kegiatan</th>
                            <td>{{ $subKegiatan->kegiatan ? $subKegiatan->kegiatan->Nama : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Sub Kegiatan</th>
                            <td>{!! nl2br($subKegiatan->Nama) !!}</td>
                        </tr>
                        <tr>
                            <th>Jadwal Mulai</th>
                            <td>{{ \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Jadwal Selesai</th>
                            <td>{{ \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y') }}</td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{!! nl2br($subKegiatan->Catatan) !!}</td>
                        </tr>
                        <tr>
                            <th>Feedback</th>
                            <td>{!! nl2br($subKegiatan->Feedback) !!}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{!! $subKegiatan->status_label !!}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Oleh</th>
                            <td>{{ $subKegiatan->createdBy ? $subKegiatan->createdBy->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $subKegiatan->DCreated ? \Carbon\Carbon::parse($subKegiatan->DCreated)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Diubah Oleh</th>
                            <td>{{ $subKegiatan->editedBy ? $subKegiatan->editedBy->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Diubah</th>
                            <td>{{ $subKegiatan->DEdited ? \Carbon\Carbon::parse($subKegiatan->DEdited)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($subKegiatan->rabs->count() > 0)
            <div class="mt-4">
                <h5>Rincian Anggaran Biaya (RAB)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Komponen</th>
                                <th>Volume</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Feedback</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($subKegiatan->rabs as $index => $rab)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $rab->Komponen }}</td>
                                <td>{{ number_format($rab->Volume, 0, ',', '.') }}</td>
                                <td>{{ $rab->satuanRelation ? $rab->satuanRelation->Nama : 'N/A' }}</td>
                                <td>Rp {{ number_format($rab->HargaSatuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($rab->Jumlah, 0, ',', '.') }}</td>
                                <td>{!! nl2br($rab->Feedback) !!}</td>
                                <td>{!! $rab->status_label !!}</td>
                            </tr>
                            @php $total += $rab->Jumlah; @endphp
                            @endforeach
                            <tr class="font-weight-bold">
                                <td colspan="5" class="text-right">Total</td>
                                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>
