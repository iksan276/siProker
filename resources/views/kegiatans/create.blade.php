<form action="{{ route('kegiatans.store') }}" method="POST" class="modal-form" id="kegiatanForm">
    @csrf
        
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2">
            <option value="" disabled {{ !isset($selectedProgramRektorObj) ? 'selected' : '' }}></option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}" 
                    {{ (isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID) || 
                       (isset($selectedProgramRektorObj) && $selectedProgramRektorObj->ProgramRektorID == $programRektor->ProgramRektorID) ? 'selected' : '' }}>
                    {{ $programRektor->Nama }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3"></textarea>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="TanggalMulai">Tanggal Mulai</label>
                <input type="datetime-local" name="TanggalMulai" id="TanggalMulai" class="form-control" onchange="validateDates()">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="TanggalSelesai">Tanggal Selesai</label>
                <input type="datetime-local" name="TanggalSelesai" id="TanggalSelesai" class="form-control" onchange="validateDates()">
                <small id="dateError" class="text-danger" style="display: none;">Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.</small>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label for="RincianKegiatan">Rincian Kegiatan</label>
        <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4"></textarea>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Get the selected values from cookies if not already set
    
    
    if (!$('#ProgramRektorID').val()) {
        var programRektorCookie = getCookie('selected_program_rektor');
        if (programRektorCookie) {
            $('#ProgramRektorID').val(programRektorCookie).trigger('change');
        }
    }
    
 
  
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
  
});

function validateDates() {
    const tanggalMulai = new Date(document.getElementById('TanggalMulai').value);
    const tanggalSelesai = new Date(document.getElementById('TanggalSelesai').value);
    const errorElement = document.getElementById('dateError');
    const submitBtn = document.getElementById('submitBtn');
    
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

document.getElementById('kegiatanForm').addEventListener('submit', function(event) {
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
    validateDates();
});
</script>
