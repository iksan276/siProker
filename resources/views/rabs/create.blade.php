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
    <small class="form-text text-muted">Pilih salah satu: Kegiatan atau Sub Kegiatan</small>
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
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // If a kegiatan or subkegiatan is pre-selected, disable the select field but keep the value for submission
        if ($("#KegiatanID").val()) {
            $("#KegiatanID").prop('disabled', false);
        }
        
        if ($("#SubKegiatanID").val()) {
            $("#SubKegiatanID").prop('disabled', false);
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
            }
        }
   
        // Trigger initial calculations and filters
        calculateTotal();
        $('#KegiatanID').trigger('change');
    });
</script>
