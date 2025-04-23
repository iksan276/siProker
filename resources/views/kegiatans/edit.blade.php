<form action="{{ route('kegiatans.update', $kegiatan->KegiatanID) }}" method="POST" class="modal-form" id="kegiatanEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="IndikatorKinerjaID">Indikator Kinerja</label>
        <select name="IndikatorKinerjaID" id="IndikatorKinerjaID" class="form-control select2" >
        <option value="" disabled selected></option>
            @foreach($indikatorKinerjas as $indikatorKinerja)
                <option value="{{ $indikatorKinerja->IndikatorKinerjaID }}" {{ $kegiatan->IndikatorKinerjaID == $indikatorKinerja->IndikatorKinerjaID ? 'selected' : '' }}>{{ $indikatorKinerja->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $kegiatan->Nama }}</textarea>
    </div>
    <div class="form-group">
        <label for="TanggalMulai">Tanggal Mulai</label>
        <input type="datetime-local" name="TanggalMulai" id="TanggalMulai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('Y-m-d\TH:i') }}" >
    </div>
    <div class="form-group">
        <label for="TanggalSelesai">Tanggal Selesai</label>
        <input type="datetime-local" name="TanggalSelesai" id="TanggalSelesai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('Y-m-d\TH:i') }}" >
    </div>
    <div class="form-group">
        <label for="RincianKegiatan">Rincian Kegiatan</label>
        <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4" >{{ $kegiatan->RincianKegiatan }}</textarea>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>

<script>
document.getElementById('kegiatanEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const indikatorKinerjaID = document.getElementById('IndikatorKinerjaID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    const tanggalMulai = document.getElementById('TanggalMulai').value.trim();
    const tanggalSelesai = document.getElementById('TanggalSelesai').value.trim();
    const rincianKegiatan = document.getElementById('RincianKegiatan').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!indikatorKinerjaID) {
        emptyFields.push('Indikator Kinerja harus dipilih');
    }
    
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!tanggalMulai) {
        emptyFields.push('Tanggal Mulai harus diisi');
    }
    
    if (!tanggalSelesai) {
        emptyFields.push('Tanggal Selesai harus diisi');
    }
    
    if (!rincianKegiatan) {
        emptyFields.push('Rincian Kegiatan harus diisi');
    }
    
    // If there are empty fields, show the error message
    if (emptyFields.length > 0) {
        const errorList = '<ul style="text-align:left;margin-left:40px;margin-right:50px" class="text-danger">' + 
            emptyFields.map(error => `<li>${error}</li>`).join('') + 
            '</ul>';
            
        Swal.fire({
            title: 'Validasi Inputan',
            html: errorList,
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
});
</script>
