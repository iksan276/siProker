<form action="{{ route('kriteria-akreditasis.update', $kriteria->KriteriaAkreditasiID) }}" method="POST" class="modal-form" id="kriteriaEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Key">Key</label>
        <input type="text" name="Key" id="Key" class="form-control" maxlength="255" value="{{ $kriteria->Key }}">
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" maxlength="255" value="{{ $kriteria->Nama }}">
    </div>
    <div class="form-group">
        <label for="NA">Status</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y" {{ $kriteria->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $kriteria->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Ubah</button>
    </div>
</form>

<script>
document.getElementById('kriteriaEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
      const key = document.getElementById('Key').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
      if (!key) {
        emptyFields.push('Key harus diisi');
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
