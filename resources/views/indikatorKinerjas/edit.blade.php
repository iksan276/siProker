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
        <div class="col-sm-12">
            <div class="form-group">
                <label for="Baseline">Baseline</label>
                <textarea name="Baseline" id="Baseline" class="form-control" rows="3">{{ $indikatorKinerja->Baseline }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="Tahun1" id="tahun1Label">{{ $yearLabels[0] ?? '2025' }}</label>
                <input type="number" name="Tahun1" id="Tahun1" class="form-control" step="0.01" value="{{ $indikatorKinerja->Tahun1 }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="Tahun2" id="tahun2Label">{{ $yearLabels[1] ?? '2026' }}</label>
                <input type="number" name="Tahun2" id="Tahun2" class="form-control" step="0.01" value="{{ $indikatorKinerja->Tahun2 }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
            <label for="Tahun3" id="tahun3Label">{{ $yearLabels[2] ?? '2027' }}</label>
                <input type="number" name="Tahun3" id="Tahun3" class="form-control" step="0.01" value="{{ $indikatorKinerja->Tahun3 }}">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="Tahun4" id="tahun4Label">{{ $yearLabels[3] ?? '2028' }}</label>
                <input type="number" name="Tahun4" id="Tahun4" class="form-control" step="0.01" value="{{ $indikatorKinerja->Tahun4 }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="MendukungIKU">Mendukung IKU PT / Kriteria Akreditasi</label>
                <select name="MendukungIKU" id="MendukungIKU" class="form-control">
                    <option value="Y" {{ $indikatorKinerja->MendukungIKU == 'Y' ? 'selected' : '' }}>Ya</option>
                    <option value="N" {{ $indikatorKinerja->MendukungIKU == 'N' ? 'selected' : '' }}>Tidak</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6">
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
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Update</button>
    </div>
</form>

<script>
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
    
    // If validation passes, submit the form via AJAX
    const form = this;
    const formData = new FormData(form);
    
    // Convert FormData to URL-encoded string
    const urlEncodedData = new URLSearchParams(formData).toString();
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: urlEncodedData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            $('#mainModal').modal('hide');
            
            // Show success message
            showAlert('success', data.message || 'Operation completed successfully');
            
            // Reload DataTable
            if (typeof indikatorKinerjaTable !== 'undefined') {
                indikatorKinerjaTable.ajax.reload();
            }
        } else {
            // Display error message
            showAlert('danger', data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while processing your request');
    });
});
</script>
