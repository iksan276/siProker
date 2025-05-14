
    <div class="row">
        <div class="col-md-12">
            <!-- Kegiatan Info Display -->
            <div id="kegiatanInfo" class="alert alert-info mt-2 py-2 mb-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge badge-primary">Info Kegiatan</span>
                    </div>
                    <div class="d-flex flex-wrap">
                        <div class="mr-3"><small><strong>Program Rektor:</strong> <span id="infoProgramRektor">-</span></small></div>
                        <div class="mr-3"><small><strong>Total Anggaran:</strong> <span id="infoTotalAnggaran">-</span></small></div>
                        <div class="mr-3"><small><strong>Sisa Anggaran:</strong> <span id="infoSisaAnggaran">-</span></small></div>
                    </div>
                </div>
            </div>

            <!-- Program Rektor Info Display -->
            <div id="programRektorInfo" class="alert alert-info mt-2 py-2 mb-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge badge-primary">Info Program Rektor</span>
                    </div>
                    <div class="d-flex flex-wrap">
                        <div class="mr-3"><small><strong>Jumlah:</strong> <span id="infoJumlahKegiatan">-</span></small></div>
                        <div class="mr-3"><small><strong>Satuan:</strong> <span id="infoSatuan">-</span></small></div>
                        <div class="mr-3"><small><strong>Harga:</strong> <span id="infoHargaSatuan">-</span></small></div>
                        <div class="mr-3"><small><strong>Total:</strong> <span id="infoTotal">-</span></small></div>
                        <div><small><strong>Penanggung Jawab:</strong> <span id="infoPenanggungJawab">-</span></small></div>
                    </div>
                </div>
            </div>

            <div id="loadingIndicator" style="display: none;" class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Memuat data...</p>
            </div>

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
<div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
</div>

<script>
    $(document).ready(function() {
        // Global variables to store budget information
        let programRektorTotal = 0;
        let availableBudget = 0;
        let currentKegiatanID = "{{ $subKegiatan->KegiatanID }}";
        let currentProgramRektorID = null;

        // Load kegiatan details on page load
        if (currentKegiatanID) {
            loadKegiatanDetails(currentKegiatanID);
        } else {
            // Hide info panels if no kegiatan
            $('#kegiatanInfo').hide();
            $('#programRektorInfo').hide();
        }

        // Function to load kegiatan details
        function loadKegiatanDetails(kegiatanID) {
            $('#loadingIndicator').show();
            $('#kegiatanInfo').hide();
            $('#programRektorInfo').hide();
            
            $.ajax({
                url: `/api/kegiatan-details/${kegiatanID}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#loadingIndicator').hide();
                    
                    // Update kegiatan info
                    $('#infoProgramRektor').text(data.programRektorNama);
                    $('#infoTotalAnggaran').text('Rp ' + parseInt(data.totalAnggaran).toLocaleString('id-ID'));
                    $('#infoSisaAnggaran').text('Rp ' + parseInt(data.sisaAnggaran).toLocaleString('id-ID'));
                    
                    // Store values
                    availableBudget = parseInt(data.sisaAnggaran);
                    programRektorTotal = parseInt(data.programRektorTotal);
                    currentProgramRektorID = data.programRektorID;
                    
                    // Show kegiatan info panel
                    $('#kegiatanInfo').show();
                    
                    // Load program rektor details
                    loadProgramRektorDetails(currentProgramRektorID);
                },
                error: function() {
                    $('#loadingIndicator').hide();
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat informasi Kegiatan',
                        icon: 'error'
                    });
                }
            });
        }

        // Function to load program rektor details
        function loadProgramRektorDetails(programRektorID) {
            if (!programRektorID) return;
            
            $.ajax({
                url: `/api/program-rektor-details/${programRektorID}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Format currency values
                    const hargaSatuan = parseInt(data.hargaSatuan).toLocaleString('id-ID');
                    const total = parseInt(data.total).toLocaleString('id-ID');
                    
                    // Update program rektor info
                    $('#infoJumlahKegiatan').text(data.jumlahKegiatan || '-');
                    $('#infoSatuan').text(data.satuan || '-');
                    $('#infoHargaSatuan').text('Rp ' + hargaSatuan);
                    $('#infoTotal').text('Rp ' + total);
                    $('#infoPenanggungJawab').text(data.penanggungJawab || '-');
                    
                    // Show program rektor info panel
                    $('#programRektorInfo').show();
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Gagal memuat informasi Program Rektor',
                        icon: 'error'
                    });
                }
            });
        }
    });
</script>
