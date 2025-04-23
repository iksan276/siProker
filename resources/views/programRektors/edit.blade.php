<form action="{{ route('program-rektors.update', $programRektor->ProgramRektorID) }}" method="POST" class="modal-form" id="programRektorEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="ProgramPengembanganID">Program Pengembangan</label>
        <select name="ProgramPengembanganID" id="ProgramPengembanganID" class="form-control select2" >
            @foreach($programPengembangans as $program)
                <option value="{{ $program->ProgramPengembanganID }}" {{ $programRektor->ProgramPengembanganID == $program->ProgramPengembanganID ? 'selected' : '' }}>{{ $program->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $programRektor->Nama }}</textarea>
    </div>
 
    <div class="form-group">
        <label for="Tahun">Tahun</label>
        <select name="Tahun" id="Tahun" class="form-control ">
        <option value="" disabled selected></option>
            @for ($year = 2025; $year < 2030; $year++)
                <option value="{{ $year }}" {{  $programRektor->Tahun == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endfor
        </select>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" >
            <option value="Y" {{ $programRektor->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $programRektor->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
 
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>

<script>
document.getElementById('programRektorEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const programPengembanganID = document.getElementById('ProgramPengembanganID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    const tahun = document.getElementById('Tahun').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!programPengembanganID) {
        emptyFields.push('Program Pengembangan harus dipilih');
    }
    
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!tahun) {
        emptyFields.push('Tahun harus dipilih');
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
