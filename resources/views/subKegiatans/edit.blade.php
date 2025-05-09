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
        <textarea class="form-control" id="Nama" name="Nama" rows="3" required>{{ $subKegiatan->Nama }}</textarea>
    </div>
    
    <div class="form-group">
        <label for="daterange">Jadwal <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="text" class="form-control" id="daterange" name="daterange" required>
            <input type="hidden" id="JadwalMulai" name="JadwalMulai" value="{{ $subKegiatan->JadwalMulai }}">
            <input type="hidden" id="JadwalSelesai" name="JadwalSelesai" value="{{ $subKegiatan->JadwalSelesai }}">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="Catatan">Catatan </label>
        <textarea class="form-control" id="Catatan" name="Catatan" rows="3" required>{{ $subKegiatan->Catatan }}</textarea>
    </div>

    @if(auth()->user()->isAdmin())
    <div class="form-group">
        <label for="Status">Status <span class="text-danger">*</span></label>
        <select name="Status" id="Status" class="form-control" required>
            <option value="N" {{ $subKegiatan->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
            <option value="Y" {{ $subKegiatan->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
            <option value="T" {{ $subKegiatan->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
            <option value="R" {{ $subKegiatan->Status == 'R' ? 'selected' : '' }}>Revisi</option>
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
        // Initialize date range picker
        $('#daterange').daterangepicker({
            opens: 'left',
            autoApply: true,
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
        }, function(start, end, label) {
            // Set values to hidden fields when date range is selected
            $('#JadwalMulai').val(start.format('YYYY-MM-DD'));
            $('#JadwalSelesai').val(end.format('YYYY-MM-DD'));
        });

        // Set initial values from the model
        var startDate = $('#JadwalMulai').val();
        var endDate = $('#JadwalSelesai').val();
        
        if (startDate && endDate) {
            $('#daterange').data('daterangepicker').setStartDate(startDate);
            $('#daterange').data('daterangepicker').setEndDate(endDate);
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
        });
    });
</script>
