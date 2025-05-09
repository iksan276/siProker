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

    <div class="form-group">
        <label for="Feedback">Feedback </label>
        <textarea class="form-control" id="Feedback" name="Feedback" rows="3"></textarea>
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
        // If a kegiatan is pre-selected, disable the select field but keep the value for submission
        if ($("#KegiatanID").val()) {
            $("#KegiatanID").prop('disabled', false);
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

