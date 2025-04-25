<form action="{{ route('isu-strategis.update', $isuStrategis->IsuID) }}" method="POST" class="modal-form" id="isuStrategisEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="PilarID">Pilar</label>
        <select name="PilarID" id="PilarID" class="form-control select2">
            @foreach($pilars as $pilar)
                <option value="{{ $pilar->PilarID }}" {{ $isuStrategis->PilarID == $pilar->PilarID ? 'selected' : '' }}>{{ $pilar->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $isuStrategis->Nama }}</textarea>
    </div>
 
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y" {{ $isuStrategis->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $isuStrategis->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit">Ubah</button>
    </div>
</form>

<script>
document.getElementById('isuStrategisEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const pilarID = document.getElementById('PilarID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!pilarID) {
        emptyFields.push('Pilar harus dipilih');
    }
    
    if (!nama) {
        emptyFields.push('Nama harus diisi');
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
