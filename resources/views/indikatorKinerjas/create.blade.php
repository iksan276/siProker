<form action="{{ route('indikator-kinerjas.store') }}" method="POST" class="modal-form" id="indikatorKinerjaForm">
    @csrf
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2">
        <option value="" disabled selected></option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}">{{ $programRektor->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3"></textarea>
    </div>
    <div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="SatuanID">Satuan</label>
            <select name="SatuanID" id="SatuanID" class="form-control select2">
            <option value="" disabled selected></option>
                @foreach($satuans as $satuan)
                    <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="Bobot">Bobot</label>
            <input type="text" name="Bobot" id="Bobot" class="form-control number-input " onkeyup="validateNumericInput(this, 'bobotError')">
            <small id="bobotError" class="text-danger" style="display: none;">Field Bobot hanya boleh diisi dengan angka!</small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="HargaSatuan">Harga Satuan</label>
            <input type="text" name="HargaSatuan" id="HargaSatuan" class="form-control currency-input " onkeyup="validateNumericInput(this, 'hargaSatuanError')">
            <small id="hargaSatuanError" class="text-danger" style="display: none;">Field Harga Satuan hanya boleh diisi dengan angka!</small>
        </div>
    </div>
    </div>
    <div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="Jumlah">Jumlah</label>
            <input type="text" name="Jumlah" id="Jumlah" class="form-control number-input " onkeyup="validateNumericInput(this, 'jumlahError')">
            <small id="jumlahError" class="text-danger" style="display: none;">Field Jumlah hanya boleh diisi dengan angka!</small>
        </div>
        </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="MetaAnggaranID" class="d-block">Meta Anggaran</label>
            <select name="MetaAnggaranID[]" id="MetaAnggaranID" class="form-control select2" multiple>
                @foreach($metaAnggarans as $metaAnggaran)
                    <option value="{{ $metaAnggaran->MetaAnggaranID }}">{{ $metaAnggaran->Nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="UnitTerkaitID" class="d-block">Unit Terkait</label>
            <select name="UnitTerkaitID[]" id="UnitTerkaitID" class="form-control select2" multiple>
                @foreach($units as $unit)
                    <option value="{{ $unit->UnitID }}">{{ $unit->Nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    </div>
    <div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="NA">Status</label>
            <select name="NA" id="NA" class="form-control">
                <option value="Y">Non Aktif</option>
                <option value="N" selected>Aktif</option>
            </select>
        </div>
    </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Simpan</button>
    </div>
</form>

<script>
function validateNumericInput(input, errorId) {
    const errorElement = document.getElementById(errorId);
    const submitBtn = document.getElementById('submitBtn');
    
    // Check if input contains non-numeric characters
    if (/[^\d\.]/.test(input.value)) {
        errorElement.style.display = 'block';
        submitBtn.disabled = true;
    } else {
        errorElement.style.display = 'none';
        submitBtn.disabled = false;
    }
    
    // Remove non-numeric characters except dots
    let value = input.value.replace(/[^\d]/g, '');
    
    // Format with thousand separator
    if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }
}

$(document).ready(function() {
    // Before form submission, remove formatting
    $('.modal-form').on('submit', function() {
        $('.number-input, .currency-input').each(function() {
            $(this).val($(this).val().replace(/\./g, ''));
        });
    });
});

document.getElementById('indikatorKinerjaForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const nama = document.getElementById('Nama').value.trim();
    const programRektorID = document.getElementById('ProgramRektorID').value.trim();
    const satuanID = document.getElementById('SatuanID').value.trim();
    const bobot = document.getElementById('Bobot').value.trim();
    const hargaSatuan = document.getElementById('HargaSatuan').value.trim();
    const jumlah = document.getElementById('Jumlah').value.trim();
    const metaAnggaranID = $('#MetaAnggaranID').val();
    const unitTerkaitID = $('#UnitTerkaitID').val();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!programRektorID) {
        emptyFields.push('Program Rektor harus dipilih');
    }
    
    if (!satuanID) {
        emptyFields.push('Satuan harus dipilih');
    }
    
    if (!bobot) {
        emptyFields.push('Bobot harus diisi');
    }
    
    if (!hargaSatuan) {
        emptyFields.push('Harga Satuan harus diisi');
    }
    
    if (!jumlah) {
        emptyFields.push('Jumlah harus diisi');
    }
    
    if (!metaAnggaranID || metaAnggaranID.length === 0) {
        emptyFields.push('Meta Anggaran harus dipilih');
    }
    
    if (!unitTerkaitID || unitTerkaitID.length === 0) {
        emptyFields.push('Unit Terkait harus dipilih');
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
