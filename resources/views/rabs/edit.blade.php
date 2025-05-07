<form action="{{ route('rabs.update', $rab->RABID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="KegiatanID">Kegiatan</label>
        <select name="KegiatanID" id="KegiatanID" class="form-control select2">
            <option value="">-- Pilih Kegiatan --</option>
            @foreach($kegiatans as $kegiatan)
                <option value="{{ $kegiatan->KegiatanID }}" {{ $rab->KegiatanID == $kegiatan->KegiatanID ? 'selected' : '' }}>
                    {{ $kegiatan->Nama }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">Pilih salah satu: Kegiatan atau Sub Kegiatan</small>
    </div>
    
    <div class="form-group">
        <label for="SubKegiatanID">Sub Kegiatan</label>
        <select name="SubKegiatanID" id="SubKegiatanID" class="form-control select2">
            <option value="">-- Pilih Sub Kegiatan --</option>
            @foreach($subKegiatans as $subKegiatan)
                <option value="{{ $subKegiatan->SubKegiatanID }}" 
                    {{ $rab->SubKegiatanID == $subKegiatan->SubKegiatanID ? 'selected' : '' }}
                    data-kegiatan="{{ $subKegiatan->KegiatanID }}">
                    {{ $subKegiatan->Nama }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="Komponen">Komponen <span class="text-danger">*</span></label>
        <textarea class="form-control" id="Komponen" name="Komponen" rows="3" required>{{ $rab->Komponen }}</textarea>
    </div>
    
    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="Volume">Volume <span class="text-danger">*</span></label>
            <input type="text" class="form-control number-format" id="Volume" name="Volume" value="{{ number_format($rab->Volume, 0, ',', '.') }}" required>
        </div>
        <div class="form-group col-md-4">
            <label for="Satuan">Satuan <span class="text-danger">*</span></label>
            <select name="Satuan" id="Satuan" class="form-control select2" required>
                <option value="">-- Pilih Satuan --</option>
                @foreach($satuans as $satuan)
                    <option value="{{ $satuan->SatuanID }}" {{ $rab->Satuan == $satuan->SatuanID ? 'selected' : '' }}>
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
                <input type="text" class="form-control number-format" id="HargaSatuan" name="HargaSatuan" value="{{ number_format($rab->HargaSatuan, 0, ',', '.') }}" required>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="Jumlah">Jumlah</label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
            </div>
            <input type="text" class="form-control number-format" id="Jumlah" value="{{ number_format($rab->Jumlah, 0, ',', '.') }}" readonly>
        </div>
        <small class="form-text text-muted">Jumlah akan dihitung otomatis (Volume × Harga Satuan)</small>
    </div>
    
    <div class="form-group">
        <label for="Status">Status <span class="text-danger">*</span></label>
        <select name="Status" id="Status" class="form-control" required>
            <option value="N" {{ $rab->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
            <option value="Y" {{ $rab->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
            <option value="T" {{ $rab->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
            <option value="R" {{ $rab->Status == 'R' ? 'selected' : '' }}>Revisi</option>
        </select>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Initialize select2
     
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
            }
        }
        
        // Filter sub kegiatans based on selected kegiatan
        $('#KegiatanID').on('change', function() {
            var kegiatanId = $(this).val();
            
            if (kegiatanId) {
                // Show only sub kegiatans for the selected kegiatan
                $('#SubKegiatanID option').each(function() {
                    if ($(this).data('kegiatan') == kegiatanId || $(this).val() == '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                // Show all sub kegiatans
                $('#SubKegiatanID option').show();
            }
            
            // Reset sub kegiatan selection
            $('#SubKegiatanID').val('').trigger('change');
        });
        
        // When sub kegiatan is selected, auto-select its parent kegiatan
        $('#SubKegiatanID').on('change', function() {
            var subKegiatanId = $(this).val();
            var kegiatanId = '';
            
            if (subKegiatanId) {
                kegiatanId = $('#SubKegiatanID option:selected').data('kegiatan');
                $('#KegiatanID').val(kegiatanId).trigger('change');
            }
        });
        
        // Trigger initial calculations
        calculateTotal();
    });
</script>
