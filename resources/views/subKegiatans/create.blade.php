<form action="{{ route('sub-kegiatans.store') }}" method="POST" class="modal-form">
    @csrf
    
    <div class="form-group">
    <label for="KegiatanID">Kegiatan <span class="text-danger">*</span></label>
    <select name="KegiatanID" id="KegiatanID" class="form-control select2" required {{ isset($selectedKegiatan) ? 'readonly' : '' }}>
        <option value="" disabled selected></option>
        @foreach($kegiatans as $kegiatan)
            <option value="{{ $kegiatan->KegiatanID }}" {{ (isset($selectedKegiatan) && $selectedKegiatan == $kegiatan->KegiatanID) ? 'selected' : '' }}>
                {{ $kegiatan->Nama }}
            </option>
        @endforeach
    </select>
</div>

    <!-- Kegiatan Info Display -->
    <div id="kegiatanInfo" class="alert alert-info mt-2 py-2" style="display: none;">
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

    <!-- Budget warning alert -->
    <div id="budgetWarning" class="alert alert-danger mt-2 py-2" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <strong>Peringatan Anggaran!</strong> Tidak ada sisa anggaran untuk kegiatan ini.
            </div>
        </div>
    </div>

    <div id="loadingIndicator" style="display: none;" class="text-center my-4">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2">Memuat data...</p>
    </div>
    
    <div class="form-group">
        <label for="Nama">Nama Sub Kegiatan <span class="text-danger">*</span></label>
        <textarea class="form-control" id="Nama" name="Nama" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="daterange">Jadwal <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="text" class="form-control" id="daterange" name="daterange" required>
            <input type="hidden" id="JadwalMulai" name="JadwalMulai">
            <input type="hidden" id="JadwalSelesai" name="JadwalSelesai">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="Catatan">Catatan </label>
        <textarea class="form-control" id="Catatan" name="Catatan" rows="3"></textarea>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="form-group">
        <label for="Feedback">Feedback </label>
        <textarea class="form-control" id="Feedback" name="Feedback" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label for="Status">Status <span class="text-danger">*</span></label>
        <select name="Status" id="Status" class="form-control" required>
            <option value="N">Menunggu</option>
            <option value="Y">Disetujui</option>
            <option value="T">Ditolak</option>
            <option value="R">Revisi</option>
        </select>
    </div>
    @endif
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Global variables to store budget information
        let availableBudget = 0;
        let currentKegiatanID = null;

        // If a kegiatan is pre-selected, disable the select field but keep the value for submission
        if ($("#KegiatanID").val()) {
            $("#KegiatanID").prop('disabled', false);
            currentKegiatanID = $("#KegiatanID").val();
            loadKegiatanDetails(currentKegiatanID);
        }

        // Initialize date range picker with empty initial value
        $('#daterange').attr('placeholder', 'DD/MM/YYYY - DD/MM/YYYY');
        $('#daterange').val('');
        
        $('#daterange').daterangepicker({
            opens: 'left',
            autoApply: true,
            autoUpdateInput: false, // Prevent automatic update with current date
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Pilih',
                cancelLabel: 'Batal',
                fromLabel: 'Dari',
                toLabel: 'Sampai',
                customRangeLabel: 'Custom',
                weekLabel: 'W',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1
            }
        });
        
        // Handle the apply event
        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            $('#JadwalMulai').val(picker.startDate.format('YYYY-MM-DD'));
            $('#JadwalSelesai').val(picker.endDate.format('YYYY-MM-DD'));
        });
        
        // Handle the cancel event
        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            $('#JadwalMulai').val('');
            $('#JadwalSelesai').val('');
        });

        // Set initial values if they exist
        var startDate = $('#JadwalMulai').val();
        var endDate = $('#JadwalSelesai').val();
        
        if (startDate && endDate) {
            var start = moment(startDate);
            var end = moment(endDate);
            
            $('#daterange').data('daterangepicker').setStartDate(start);
            $('#daterange').data('daterangepicker').setEndDate(end);
            $('#daterange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        }

        // Load kegiatan details when a kegiatan is selected
        $('#KegiatanID').on('change', function() {
            currentKegiatanID = $(this).val();
            
            if (currentKegiatanID) {
                loadKegiatanDetails(currentKegiatanID);
            } else {
                // Hide info panels if no kegiatan selected
                $('#kegiatanInfo').hide();
                $('#budgetWarning').hide();
            }
        });

        // Function to load kegiatan details
        function loadKegiatanDetails(kegiatanID) {
            $('#loadingIndicator').show();
            $('#kegiatanInfo').hide();
            
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
                    
                    // Store values for validation
                    availableBudget = parseInt(data.sisaAnggaran);
                    
                    // Show kegiatan info panel
                    $('#kegiatanInfo').show();
                    
                    // Validate budget
                    validateBudget();
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

        // Validate budget against available amount
        function validateBudget() {
            if (availableBudget <= 0) {
                $('#budgetWarning').show();
                $('#submitBtn').prop('disabled', true);
                
                // Tambahkan SweetAlert untuk validasi budget
                Swal.fire({
                    title: 'Peringatan Anggaran!',
                    html: `Tidak ada sisa anggaran untuk kegiatan ini.<br><br>
                          <div class="text-left">
                            <strong>Sisa Anggaran:</strong> Rp ${availableBudget.toLocaleString('id-ID')}
                          </div>`,
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Mengerti'
                });
            } else {
                $('#budgetWarning').hide();
                $('#submitBtn').prop('disabled', false);
            }
        }

        // Form validation before submit
        $('.modal-form').on('submit', function(e) {
            var startDate = $('#JadwalMulai').val();
            var endDate = $('#JadwalSelesai').val();
            
            if (!startDate || !endDate) {
                e.preventDefault();
                alert('Silakan pilih jadwal mulai dan selesai');
                return false;
            }
            
            if (new Date(endDate) < new Date(startDate)) {
                e.preventDefault();
                alert('Jadwal Selesai tidak boleh lebih awal dari Jadwal Mulai');
                return false;
            }

            // Check if kegiatan is selected
            if (!$('#KegiatanID').val()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi',
                    text: 'Harus memilih Kegiatan',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Check if budget is available
            if (availableBudget <= 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Peringatan Anggaran!',
                    html: `Tidak dapat membuat Sub Kegiatan karena tidak ada sisa anggaran.<br><br>
                          <div class="text-left">
                            <strong>Sisa Anggaran:</strong> Rp ${availableBudget.toLocaleString('id-ID')}
                          </div>`,
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Mengerti'
                });
                return false;
            }
        });
    });
</script>
