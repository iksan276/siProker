<form action="{{ route('kegiatans.update', $kegiatan->KegiatanID) }}" method="POST" class="modal-form" id="kegiatanEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2" >
        <option value="" disabled selected></option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}" {{ $kegiatan->ProgramRektorID == $programRektor->ProgramRektorID ? 'selected' : '' }}>{{ $programRektor->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $kegiatan->Nama }}</textarea>
    </div>
    <div class="row">
    <div class="col-sm-6">
    <div class="form-group">
        <label for="TanggalMulai">Tanggal Mulai</label>
        <input type="date" name="TanggalMulai" id="TanggalMulai" class="form-control " value="{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('Y-m-d') }}" onchange="validateDatesEdit()">
    </div>
    </div>
    <div class="col-sm-6">
    <div class="form-group">
        <label for="TanggalSelesai">Tanggal Selesai</label>
        <input type="date" name="TanggalSelesai" id="TanggalSelesai" class="form-control " value="{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('Y-m-d') }}" onchange="validateDatesEdit()">
        <small id="dateErrorEdit" class="text-danger" style="display: none;">Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.</small>
    </div>
    </div>
    </div>
    <div class="form-group">
        <label for="RincianKegiatan">Rincian Kegiatan</label>
        <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4" >{{ $kegiatan->RincianKegiatan }}</textarea>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtnEdit">Ubah</button>
    </div>
</form>

<script>
function validateDatesEdit() {
    const tanggalMulai = new Date(document.getElementById('TanggalMulai').value);
    const tanggalSelesai = new Date(document.getElementById('TanggalSelesai').value);
    const errorElement = document.getElementById('dateErrorEdit');
    const submitBtn = document.getElementById('submitBtnEdit');
    
    if (document.getElementById('TanggalMulai').value && document.getElementById('TanggalSelesai').value) {
        if (tanggalSelesai < tanggalMulai) {
            errorElement.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            errorElement.style.display = 'none';
            submitBtn.disabled = false;
        }
    }
}

document.getElementById('kegiatanEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const programRektorID = document.getElementById('ProgramRektorID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    const tanggalMulai = document.getElementById('TanggalMulai').value.trim();
    const tanggalSelesai = document.getElementById('TanggalSelesai').value.trim();
    const rincianKegiatan = document.getElementById('RincianKegiatan').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!programRektorID) {
        emptyFields.push('Program Rektor harus dipilih');
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
    
    // Check if tanggal selesai is valid
    if (new Date(tanggalSelesai) < new Date(tanggalMulai)) {
        Swal.fire({
            title: 'Validasi Inputan',
            html: 'Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
});

// Run validation on page load
document.addEventListener('DOMContentLoaded', function() {
    validateDatesEdit();
});
</script>
