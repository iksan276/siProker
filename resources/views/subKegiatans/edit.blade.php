<form action="{{ route('sub-kegiatans.update', $subKegiatan->SubKegiatanID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    
    <div class="form-group">
        <label for="KegiatanID">Kegiatan <span class="text-danger">*</span></label>
        <select name="KegiatanID" id="KegiatanID" class="form-control select2" required>
            <option value="">-- Pilih Kegiatan --</option>
            @foreach($kegiatans as $kegiatan)
                <option value="{{ $kegiatan->KegiatanID }}" {{ $subKegiatan->KegiatanID == $kegiatan->KegiatanID ? 'selected' : '' }}>
                    {{ $kegiatan->Nama }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="Nama">Nama Sub Kegiatan <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="Nama" name="Nama" value="{{ $subKegiatan->Nama }}" required>
    </div>
    
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="JadwalMulai">Jadwal Mulai <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="JadwalMulai" name="JadwalMulai" value="{{ $subKegiatan->JadwalMulai }}" required>
        </div>
        <div class="form-group col-md-6">
            <label for="JadwalSelesai">Jadwal Selesai <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="JadwalSelesai" name="JadwalSelesai" value="{{ $subKegiatan->JadwalSelesai }}" required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="Status">Status <span class="text-danger">*</span></label>
        <select name="Status" id="Status" class="form-control" required>
            <option value="N" {{ $subKegiatan->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
            <option value="Y" {{ $subKegiatan->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
            <option value="T" {{ $subKegiatan->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
            <option value="R" {{ $subKegiatan->Status == 'R' ? 'selected' : '' }}>Revisi</option>
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
        $('.select2').select2({
            dropdownParent: $('#mainModal'),
            width: '100%'
        });
        
        // Date validation
        $('#JadwalMulai, #JadwalSelesai').on('change', function() {
            var startDate = $('#JadwalMulai').val();
            var endDate = $('#JadwalSelesai').val();
            
            if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
                alert('Jadwal Selesai tidak boleh lebih awal dari Jadwal Mulai');
                $('#JadwalSelesai').val('');
            }
        });
    });
</script>
