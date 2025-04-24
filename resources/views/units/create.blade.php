<form action="{{ route('units.store') }}" method="POST" class="modal-form" id="unitForm">
    @csrf
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control">
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
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
</form>

<script>
document.getElementById('unitForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const nama = document.getElementById('Nama').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
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
