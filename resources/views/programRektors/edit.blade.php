<form action="{{ route('program-rektors.update', $programRektor->ProgramRektorID) }}" method="POST" class="modal-form" id="programRektorEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="ProgramPengembanganID">Program Pengembangan</label>
        <select name="ProgramPengembanganID" id="ProgramPengembanganID" class="form-control select2">
            @foreach($programPengembangans as $program)
                <option value="{{ $program->ProgramPengembanganID }}" {{ $programRektor->ProgramPengembanganID == $program->ProgramPengembanganID ? 'selected' : '' }}>{{ $program->Nama }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="IndikatorKinerjaID">Indikator Kinerja</label>
        <select name="IndikatorKinerjaID" id="IndikatorKinerjaID" class="form-control select2">
            @foreach($indikatorKinerjas as $indikatorKinerja)
                <option value="{{ $indikatorKinerja->IndikatorKinerjaID }}" {{ $programRektor->IndikatorKinerjaID == $indikatorKinerja->IndikatorKinerjaID ? 'selected' : '' }}>{{ $indikatorKinerja->Nama }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $programRektor->Nama }}</textarea>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="Output">Output</label>
                <textarea name="Output" id="Output" class="form-control" rows="3">{{ $programRektor->Output }}</textarea>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="Outcome">Outcome</label>
                <textarea name="Outcome" id="Outcome" class="form-control" rows="3">{{ $programRektor->Outcome }}</textarea>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="JenisKegiatanID">Jenis Kegiatan</label>
                <select name="JenisKegiatanID" id="JenisKegiatanID" class="form-control select2">
                    @foreach($jenisKegiatans as $jenisKegiatan)
                        <option value="{{ $jenisKegiatan->JenisKegiatanID }}" {{ $programRektor->JenisKegiatanID == $jenisKegiatan->JenisKegiatanID ? 'selected' : '' }}>{{ $jenisKegiatan->Nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="MataAnggaranID" class="d-block">Mata Anggaran</label>
                <select name="MataAnggaranID[]" id="MataAnggaranID" class="form-control select2" multiple>
                    @foreach($mataAnggarans as $mataAnggaran)
                        <option value="{{ $mataAnggaran->MataAnggaranID }}" {{ in_array($mataAnggaran->MataAnggaranID, $selectedMataAnggarans) ? 'selected' : '' }}>{{ $mataAnggaran->Nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="JumlahKegiatan">Jumlah Kegiatan</label>
                <input type="text" name="JumlahKegiatan" id="JumlahKegiatan" class="form-control number-input" value="{{ number_format($programRektor->JumlahKegiatan, 0, ',', '.') }}" onkeyup="validateNumericInput(this, 'jumlahKegiatanError'); calculateTotal()">
                <small id="jumlahKegiatanError" class="text-danger" style="display: none;">Field Jumlah Kegiatan hanya boleh diisi dengan angka!</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
            <label for="SatuanID">Satuan</label>
                <select name="SatuanID" id="SatuanID" class="form-control select2">
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->SatuanID }}" {{ $programRektor->SatuanID == $satuan->SatuanID ? 'selected' : '' }}>{{ $satuan->Nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="HargaSatuan">Harga Satuan</label>
                <input type="text" name="HargaSatuan" id="HargaSatuan" class="form-control currency-input" value="{{ number_format($programRektor->HargaSatuan, 0, ',', '.') }}" onkeyup="validateNumericInput(this, 'hargaSatuanError'); calculateTotal()">
                <small id="hargaSatuanError" class="text-danger" style="display: none;">Field Harga Satuan hanya boleh diisi dengan angka!</small>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="Total">Total</label>
                <input type="text" name="Total" id="Total" class="form-control currency-input" value="{{ number_format($programRektor->Total, 0, ',', '.') }}">
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="PenanggungJawabID">Penanggung Jawab</label>
                <select name="PenanggungJawabID" id="PenanggungJawabID" class="form-control select2">
                    @foreach($units as $unit)
                        <option value="{{ $unit['UnitID'] }}" {{ $programRektor->PenanggungJawabID == $unit['UnitID'] ? 'selected' : '' }}>{{ $unit['Nama'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="PelaksanaID" class="d-block">Pelaksana</label>
                <select name="PelaksanaID[]" id="PelaksanaID" class="form-control select2" multiple>
                    @foreach($units as $unit)
                        <option value="{{ $unit['UnitID'] }}" {{ in_array($unit['UnitID'], $selectedPelaksanas) ? 'selected' : '' }}>{{ $unit['Nama'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="NA">Status</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y" {{ $programRektor->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $programRektor->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Ubah</button>
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

function calculateTotal() {
    const jumlahKegiatan = document.getElementById('JumlahKegiatan').value.replace(/\./g, '');
    const hargaSatuan = document.getElementById('HargaSatuan').value.replace(/\./g, '');
    
    if (jumlahKegiatan && hargaSatuan) {
        const total = parseInt(jumlahKegiatan) * parseInt(hargaSatuan);
        document.getElementById('Total').value = new Intl.NumberFormat('id-ID').format(total);
    } else {
        document.getElementById('Total').value = '';
    }
}

$(document).ready(function() {
    // Before form submission, remove formatting
    $('.modal-form').on('submit', function() {
        $('.number-input, .currency-input').each(function() {
            $(this).val($(this).val().replace(/\./g, ''));
        });
    });
    
    // Calculate total on page load
    calculateTotal();
});

document.getElementById('programRektorEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const programPengembanganID = document.getElementById('ProgramPengembanganID').value.trim();
    const indikatorKinerjaID = document.getElementById('IndikatorKinerjaID').value.trim(); // Added validation
    const nama = document.getElementById('Nama').value.trim();
    const output = document.getElementById('Output').value.trim();
    const outcome = document.getElementById('Outcome').value.trim();
    const jenisKegiatanID = document.getElementById('JenisKegiatanID').value.trim();
    const mataAnggaranID = $('#MataAnggaranID').val();
    const jumlahKegiatan = document.getElementById('JumlahKegiatan').value.trim();
    const satuanID = document.getElementById('SatuanID').value.trim();
    const hargaSatuan = document.getElementById('HargaSatuan').value.trim();
    const total = document.getElementById('Total').value.trim();
    const penanggungJawabID = document.getElementById('PenanggungJawabID').value.trim();
    const pelaksanaID = $('#PelaksanaID').val();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!programPengembanganID) {
        emptyFields.push('Program Pengembangan harus dipilih');
    }
    
    if (!indikatorKinerjaID) {
        emptyFields.push('Indikator Kinerja harus dipilih');
    }
    
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!output) {
        emptyFields.push('Output harus diisi');
    }
    
    if (!outcome) {
        emptyFields.push('Outcome harus diisi');
    }
    
    if (!jenisKegiatanID) {
        emptyFields.push('Jenis Kegiatan harus dipilih');
    }
    
    if (!mataAnggaranID || mataAnggaranID.length === 0) {
        emptyFields.push('Mata Anggaran harus dipilih');
    }
    
    if (!jumlahKegiatan) {
        emptyFields.push('Jumlah Kegiatan harus diisi');
    }
    
    if (!satuanID) {
        emptyFields.push('Satuan harus dipilih');
    }
    
    if (!hargaSatuan) {
        emptyFields.push('Harga Satuan harus diisi');
    }
    
    if (!penanggungJawabID) {
        emptyFields.push('Penanggung Jawab harus dipilih');
    }
    
    if (!pelaksanaID || pelaksanaID.length === 0) {
        emptyFields.push('Pelaksana harus dipilih');
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
