<form action="{{ route('program-pengembangans.update', $programPengembangan->ProgramPengembanganID) }}" method="POST" class="modal-form" id="programEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="IsuID">Isu Strategis</label>
        <select name="IsuID" id="IsuID" class="form-control select2" >
            @foreach($isuStrategis as $isu)
                <option value="{{ $isu->IsuID }}" {{ $programPengembangan->IsuID == $isu->IsuID ? 'selected' : '' }}>{{ $isu->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $programPengembangan->Nama }}</textarea>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" >
            <option value="Y" {{ $programPengembangan->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $programPengembangan->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
 
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>

<script>
document.getElementById('programEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const isuID = document.getElementById('IsuID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!isuID) {
        emptyFields.push('Isu Strategis harus dipilih');
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
