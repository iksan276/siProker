<form action="{{ route('renstras.update', $renstra->RenstraID) }}" method="POST" class="modal-form" id="renstraEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $renstra->Nama }}">
    </div>
    <div class="row">
        <div class="col-sm-4">
        <div class="form-group">
        <label for="PeriodeMulai">Periode Mulai</label>
        <select name="PeriodeMulai" id="PeriodeMulai" class="form-control " onchange="validatePeriodsEdit()">
        <option value="" disabled selected></option>
            @for ($year = 2025; $year < 2030; $year++)
                <option value="{{ $year }}" {{ $renstra->PeriodeMulai == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
        </select>
    </div>
        </div>
        <div class="col-sm-4">
        <div class="form-group">
        <label for="PeriodeSelesai">Periode Selesai</label>
        <select name="PeriodeSelesai" id="PeriodeSelesai" class="form-control " onchange="validatePeriodsEdit()">
        <option value="" disabled selected></option>
            @for ($year = 2025; $year < 2030; $year++)
                <option value="{{ $year }}" {{ $renstra->PeriodeSelesai == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
        </select>
        <small id="periodErrorEdit" class="text-danger" style="display: none;">Periode Selesai harus lebih besar atau sama dengan Periode Mulai.</small>
    </div>
        </div>
        <div class="col-sm-4">
        <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y" {{ $renstra->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $renstra->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
        </div>
    </div>
  
  
   
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtnEdit">Ubah</button>
    </div>
</form>

<script>
function validatePeriodsEdit() {
    const periodeMulai = parseInt(document.getElementById('PeriodeMulai').value);
    const periodeSelesai = parseInt(document.getElementById('PeriodeSelesai').value);
    const errorElement = document.getElementById('periodErrorEdit');
    const submitBtn = document.getElementById('submitBtnEdit');
    
    if (periodeSelesai < periodeMulai) {
        errorElement.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        errorElement.style.display = 'none';
        submitBtn.disabled = false;
    }
}

document.getElementById('renstraEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const nama = document.getElementById('Nama').value.trim();
    const periodeMulai = document.getElementById('PeriodeMulai').value;
    const periodeSelesai = document.getElementById('PeriodeSelesai').value;
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!periodeMulai) {
        emptyFields.push('Periode Mulai harus dipilih');
    }
    
    if (!periodeSelesai) {
        emptyFields.push('Periode Selesai harus dipilih');
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
    
    // Check if periode selesai is valid
    if (parseInt(periodeSelesai) < parseInt(periodeMulai)) {
        Swal.fire({
            title: 'Validasi Inputan',
            html: 'Periode Selesai harus lebih besar atau sama dengan Periode Mulai.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return false;
    }

});

// Run validation on page load
document.addEventListener('DOMContentLoaded', function() {
    validatePeriodsEdit();
});
</script>
