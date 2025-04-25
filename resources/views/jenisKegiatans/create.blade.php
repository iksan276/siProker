<form action="{{ route('jenis-kegiatans.store') }}" method="POST" class="modal-form" id="jenisKegiatanForm">
    @csrf
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label for="NA">Status</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y">Non Aktif</option>
            <option value="N" selected>Aktif</option>
        </select>
    </div>
    <div class="modal-footer">
    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Simpan</button>
    </div>
</form>

<script>
document.getElementById('jenisKegiatanForm').addEventListener('submit', function(event) {
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
