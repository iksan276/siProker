<form action="{{ route('indikator-kinerjas.update', $indikatorKinerja->IndikatorKinerjaID) }}" method="POST" class="modal-form" id="indikatorKinerjaEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $indikatorKinerja->Nama }}</textarea>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="SatuanID">Satuan</label>
                <select name="SatuanID" id="SatuanID" class="form-control select2">
                    <option value="" disabled selected></option>
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->SatuanID }}" {{ $indikatorKinerja->SatuanID == $satuan->SatuanID ? 'selected' : '' }}>{{ $satuan->Nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Baseline">Baseline</label>
                <textarea name="Baseline" id="Baseline" class="form-control" rows="1">{{ $indikatorKinerja->Baseline }}</textarea>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Tahun1" id="tahun1Label">{{ $yearLabels[0] ?? '2025' }}</label>
                <input type="text" name="Tahun1" id="Tahun1" class="form-control"  value="{{ $indikatorKinerja->Tahun1 }}">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Tahun2" id="tahun2Label">{{ $yearLabels[1] ?? '2026' }}</label>
                <input type="text" name="Tahun2" id="Tahun2" class="form-control"  value="{{ $indikatorKinerja->Tahun2 }}">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Tahun3" id="tahun3Label">{{ $yearLabels[2] ?? '2027' }}</label>
                <input type="text" name="Tahun3" id="Tahun3" class="form-control"  value="{{ $indikatorKinerja->Tahun3 }}">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Tahun4" id="tahun4Label">{{ $yearLabels[3] ?? '2028' }}</label>
                <input type="text" name="Tahun4" id="Tahun4" class="form-control"  value="{{ $indikatorKinerja->Tahun4 }}">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label for="Tahun5" id="tahun5Label">{{ $yearLabels[4] ?? '2029' }}</label>
                <input type="text" name="Tahun5" id="Tahun5" class="form-control"  value="{{ $indikatorKinerja->Tahun5 }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="MendukungIKU">Mendukung IKU PT / Kriteria Akreditasi</label>
                <select name="MendukungIKU" id="MendukungIKU" class="form-control">
                    <option value="Y" {{ $indikatorKinerja->MendukungIKU == 'Y' ? 'selected' : '' }}>Ya</option>
                    <option value="N" {{ $indikatorKinerja->MendukungIKU == 'N' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
        </div>
    </div>
    
    <div id="ikuptSection" style="display: {{ $indikatorKinerja->MendukungIKU == 'Y' ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="IKUPTID">IKU PT</label>
                    <select name="IKUPTID[]" id="IKUPTID" class="form-control select2" multiple>
                        @foreach($ikupts as $ikupt)
                            <option value="{{ $ikupt->IKUPTID }}" {{ in_array($ikupt->IKUPTID, $selectedIKUPTIds) ? 'selected' : '' }}>{{ $ikupt->Nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div id="kriteriaSection" style="display: {{ $indikatorKinerja->MendukungIKU == 'N' ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="KriteriaAkreditasiID">Kriteria Akreditasi</label>
                    <select name="KriteriaAkreditasiID[]" id="KriteriaAkreditasiID" class="form-control select2" multiple>
                        @foreach($kriteriaAkreditasis as $kriteria)
                            <option value="{{ $kriteria->KriteriaAkreditasiID }}" {{ in_array($kriteria->KriteriaAkreditasiID, $selectedKriteriaIds) ? 'selected' : '' }}>{{ $kriteria->Nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="NA">Status</label>
                <select name="NA" id="NA" class="form-control">
                    <option value="Y" {{ $indikatorKinerja->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
                    <option value="N" {{ $indikatorKinerja->NA == 'N' ? 'selected' : '' }}>Aktif</option>
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Ubah</button>
    </div>
</form>

<script>
    // Toggle IKU PT / Kriteria Akreditasi sections based on MendukungIKU selection
document.getElementById('MendukungIKU').addEventListener('change', function() {
    toggleSections(this.value);
});

// Function to toggle sections based on MendukungIKU value
function toggleSections(value) {
    const ikuptSection = document.getElementById('ikuptSection');
    const kriteriaSection = document.getElementById('kriteriaSection');
    
    if (value === 'Y') {
        ikuptSection.style.display = 'block';
        kriteriaSection.style.display = 'none';
        
        // Reset the other select when switching
        if (typeof $('#KriteriaAkreditasiID').select2 !== 'undefined') {
            $('#KriteriaAkreditasiID').val(null).trigger('change');
        }
    } else {
        ikuptSection.style.display = 'none';
        kriteriaSection.style.display = 'block';
        
        // Reset the other select when switching
        if (typeof $('#IKUPTID').select2 !== 'undefined') {
            $('#IKUPTID').val(null).trigger('change');
        }
    }
}

document.getElementById('indikatorKinerjaEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const nama = document.getElementById('Nama').value.trim();
    const satuanID = document.getElementById('SatuanID').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!satuanID) {
        emptyFields.push('Satuan harus dipilih');
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
    
  

// Toggle IKU PT / Kriteria Akreditasi sections based on MendukungIKU selection
document.getElementById('MendukungIKU').addEventListener('change', function() {
    const ikuptSection = document.getElementById('ikuptSection');
    const kriteriaSection = document.getElementById('kriteriaSection');
    
    if (this.value === 'Y') {
        ikuptSection.style.display = 'block';
        kriteriaSection.style.display = 'none';
        
        // Reset the other select when switching
        if (typeof $('#KriteriaAkreditasiID').select2 !== 'undefined') {
            $('#KriteriaAkreditasiID').val(null).trigger('change');
        }
    } else {
        ikuptSection.style.display = 'none';
        kriteriaSection.style.display = 'block';
        
        // Reset the other select when switching
        if (typeof $('#IKUPTID').select2 !== 'undefined') {
            $('#IKUPTID').val(null).trigger('change');
        }
    }
});
});

</script>
