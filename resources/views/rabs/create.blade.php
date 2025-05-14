<form action="{{ route('rabs.store') }}" method="POST" class="modal-form">
    @csrf
    
    @if($selectedSubKegiatan < 1 )
    <div class="form-group">
        <label for="KegiatanID">Kegiatan {{$selectedSubKegiatan}}</label>
        <select name="KegiatanID" id="KegiatanID" class="form-control select2" {{ isset($selectedKegiatan) ? 'readonly' : '' }}>
            <option value="" disabled selected></option>
            @foreach($kegiatans as $kegiatan)
                <option value="{{ $kegiatan->KegiatanID }}" {{ (isset($selectedKegiatan) && $selectedKegiatan == $kegiatan->KegiatanID) ? 'selected' : '' }}>
                    {{ $kegiatan->Nama }}
                </option>
            @endforeach
        </select>
  </div>
    @endif

    @if($selectedSubKegiatan > 0)
    <div class="form-group">
        <label for="SubKegiatanID">Sub Kegiatan</label>
        <select name="SubKegiatanID" id="SubKegiatanID" class="form-control select2" {{ isset($selectedSubKegiatan) ? 'readonly' : '' }}>
            <option value="" disabled selected></option>
            @foreach($subKegiatans as $subKegiatan)
                <option value="{{ $subKegiatan->SubKegiatanID }}" 
                    {{ (isset($selectedSubKegiatan) && $selectedSubKegiatan == $subKegiatan->SubKegiatanID) ? 'selected' : '' }}
                    data-kegiatan="{{ $subKegiatan->KegiatanID }}">
                    {{ $subKegiatan->Nama }} 
                </option>
            @endforeach
        </select>
    </div>
    @endif

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

    <!-- Program Rektor Info Display -->
    <div id="programRektorInfo" class="alert alert-info mt-2 py-2" style="display: none;">
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

    <!-- Budget warning alert -->
    <div id="budgetWarning" class="alert alert-danger mt-2 py-2" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <strong>Peringatan Anggaran!</strong> Total anggaran melebihi batas yang tersedia.
                <div>Total RAB: <span id="currentTotal">Rp 0</span> | Batas: <span id="budgetLimit">Rp 0</span></div>
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
        <label for="Komponen">Komponen <span class="text-danger">*</span></label>
        <textarea class="form-control" id="Komponen" name="Komponen" rows="3" required></textarea>
    </div>
    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="Volume">Volume <span class="text-danger">*</span></label>
            <input type="text" class="form-control number-format" id="Volume" name="Volume" required>
        </div>
        <div class="form-group col-md-4">
        <label for="Satuan">Satuan <span class="text-danger">*</span></label>
            <select name="Satuan" id="Satuan" class="form-control select2" required>
                <option value="" disabled selected></option>
                @foreach($satuans as $satuan)
                    <option value="{{ $satuan->SatuanID }}">
                        {{ $satuan->Nama }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="HargaSatuan">Harga Satuan <span class="text-danger">*</span></label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Rp</span>
                </div>
                <input type="text" class="form-control number-format" id="HargaSatuan" name="HargaSatuan" required>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="Jumlah">Jumlah</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control number-format" id="Jumlah" readonly>
        </div>
        <small class="form-text text-muted">Jumlah akan dihitung otomatis (Volume × Harga Satuan)</small>
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
    let programRektorTotal = 0;
    let availableBudget = 0;
    let currentKegiatanID = null;
    let currentSubKegiatanID = null;
    let currentProgramRektorID = null;

    // If a kegiatan or subkegiatan is pre-selected, disable the select field but keep the value for submission
    if ($("#KegiatanID").val()) {
        $("#KegiatanID").prop('disabled', false);
        currentKegiatanID = $("#KegiatanID").val();
        loadKegiatanDetails(currentKegiatanID);
    } else if ($("#SubKegiatanID").val()) {
        // If KegiatanID is empty but SubKegiatanID has a value, load the sub-kegiatan details directly
        $("#SubKegiatanID").prop('disabled', false);
        currentSubKegiatanID = $("#SubKegiatanID").val();
        loadSubKegiatanDetails(currentSubKegiatanID);
    }
    
    // Format numbers with thousand separator
    $('.number-format').on('input', function() {
        var value = $(this).val().replace(/[^\d]/g, '');
        if (value !== '') {
            value = parseInt(value, 10);
            $(this).val(value.toLocaleString('id-ID'));
        }
        calculateTotal();
    });
    
    // Calculate total amount
    function calculateTotal() {
        var volume = $('#Volume').val().replace(/\./g, '');
        var hargaSatuan = $('#HargaSatuan').val().replace(/\./g, '');
        
        if (volume !== '' && hargaSatuan !== '') {
            var total = parseInt(volume) * parseInt(hargaSatuan);
            $('#Jumlah').val(total.toLocaleString('id-ID'));
            $('#currentTotal').text('Rp ' + total.toLocaleString('id-ID'));
            
            // Validate budget
            validateBudget(total);
        }
    }

    // Validate budget against available amount
   function validateBudget(total) {
    if (total > availableBudget) {
        $('#budgetWarning').show();
        $('#submitBtn').prop('disabled', true);
        
        // Tambahkan SweetAlert untuk validasi budget
        Swal.fire({
            title: 'Peringatan Anggaran!',
            html: `Total anggaran melebihi batas yang tersedia.<br><br>
                  <div class="text-left">
                    <strong>Total RAB:</strong> Rp ${total.toLocaleString('id-ID')}<br>
                    <strong>Batas:</strong> Rp ${availableBudget.toLocaleString('id-ID')}<br>
                    <strong>Selisih:</strong> Rp ${(total - availableBudget).toLocaleString('id-ID')}
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


    // Load kegiatan details when a kegiatan is selected
    $('#KegiatanID').on('change', function() {
        currentKegiatanID = $(this).val();
        currentSubKegiatanID = null;
        $('#SubKegiatanID').val('').trigger('change');
        
        if (currentKegiatanID) {
            loadKegiatanDetails(currentKegiatanID);
        } else {
            // Hide info panels if no kegiatan selected
            $('#kegiatanInfo').hide();
            $('#programRektorInfo').hide();
            $('#budgetWarning').hide();
        }
    });

    // Load sub kegiatan details when a sub kegiatan is selected
    $('#SubKegiatanID').on('change', function() {
        currentSubKegiatanID = $(this).val();
        
        if (currentSubKegiatanID) {
            // If sub kegiatan is selected, we need its parent kegiatan
            currentKegiatanID = $('#SubKegiatanID option:selected').data('kegiatan');
            loadSubKegiatanDetails(currentSubKegiatanID);
        } else if (!currentKegiatanID) {
            // Hide info panels if no sub kegiatan or kegiatan selected
            $('#kegiatanInfo').hide();
            $('#programRektorInfo').hide();
            $('#budgetWarning').hide();
        }
    });

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
                
                // Store values for validation
                availableBudget = parseInt(data.sisaAnggaran);
                programRektorTotal = parseInt(data.programRektorTotal);
                currentProgramRektorID = data.programRektorID;
                
                // Update budget limit display
                $('#budgetLimit').text('Rp ' + availableBudget.toLocaleString('id-ID'));
                
                // Show kegiatan info panel
                $('#kegiatanInfo').show();
                
                // Load program rektor details
                loadProgramRektorDetails(currentProgramRektorID);
                
                // Validate current total against available budget
                calculateTotal();
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

    // Function to load sub kegiatan details
 // Function to load sub kegiatan details
    function loadSubKegiatanDetails(subKegiatanID) {
        $('#loadingIndicator').show();
        $('#kegiatanInfo').hide();
        $('#programRektorInfo').hide();
        
        $.ajax({
            url: `/api/sub-kegiatan-details/${subKegiatanID}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#loadingIndicator').hide();
                
                // Update kegiatan info from sub-kegiatan data
                $('#infoProgramRektor').text(data.programRektorNama);
                $('#infoTotalAnggaran').text('Rp ' + parseInt(data.totalAnggaran).toLocaleString('id-ID'));
                $('#infoSisaAnggaran').text('Rp ' + parseInt(data.sisaAnggaran).toLocaleString('id-ID'));
                
                // Store values for validation
                availableBudget = parseInt(data.sisaAnggaran);
                programRektorTotal = parseInt(data.programRektorTotal);
                currentProgramRektorID = data.programRektorID;
                currentKegiatanID = data.kegiatanID;
                
                // Update budget limit display
                $('#budgetLimit').text('Rp ' + availableBudget.toLocaleString('id-ID'));
                
                // Show kegiatan info panel
                $('#kegiatanInfo').show();
                
                // Load program rektor details
                loadProgramRektorDetails(currentProgramRektorID);
                
                // Validate current total against available budget
                calculateTotal();
                
                // Explicitly trigger budget validation with current values
                var volume = $('#Volume').val().replace(/\./g, '');
                var hargaSatuan = $('#HargaSatuan').val().replace(/\./g, '');
                
                if (volume !== '' && hargaSatuan !== '') {
                    var total = parseInt(volume) * parseInt(hargaSatuan);
                    validateBudget(total);
                }
            },
            error: function() {
                $('#loadingIndicator').hide();
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat informasi Sub Kegiatan',
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

    // Trigger initial calculations and filters
    calculateTotal();
    
    // Form submission validation
    $('form.modal-form').on('submit', function(e) {
        // Get the current total
        var jumlahStr = $('#Jumlah').val().replace(/\./g, '');
        var jumlah = jumlahStr ? parseInt(jumlahStr) : 0;
        
        // Check if total exceeds available budget
        if (availableBudget > 0 && jumlah > availableBudget) {
            e.preventDefault();
            Swal.fire({
                title: 'Peringatan Anggaran!',
                html: `Total anggaran melebihi batas yang tersedia.<br><br>
                      <div class="text-left">
                        <strong>Total RAB:</strong> Rp ${jumlah.toLocaleString('id-ID')}<br>
                        <strong>Batas:</strong> Rp ${availableBudget.toLocaleString('id-ID')}<br>
                        <strong>Selisih:</strong> Rp ${(jumlah - availableBudget).toLocaleString('id-ID')}
                      </div>`,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Mengerti'
            });
            return false;
        }
        
        // Validate that either Kegiatan or SubKegiatan is selected
        if (!$('#KegiatanID').val() && !$('#SubKegiatanID').val()) {
            e.preventDefault();
            Swal.fire({
                title: 'Validasi',
                text: 'Harus memilih Kegiatan atau Sub Kegiatan',
                icon: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return false;
        }
    });
});

</script>
