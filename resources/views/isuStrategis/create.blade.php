<form action="{{ route('isu-strategis.store') }}" method="POST" class="modal-form" id="isuStrategisForm">
    @csrf
    <div class="form-group">
        <label for="PilarID">Pilar</label>
        <select name="PilarID" id="PilarID" class="form-control select2">
            <option value="" disabled selected></option>
            @foreach($pilars as $pilar)
                <option value="{{ $pilar->PilarID }}" {{ isset($selectedPilar) && $selectedPilar == $pilar->PilarID ? 'selected' : '' }}>{{ $pilar->Nama }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3"></textarea>
    </div>
  
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control">
            <option value="Y">Non Aktif</option>
            <option value="N" selected>Aktif</option>
        </select>
    </div>
  
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
</form>

<script>
    $(document).ready(function() {
    
    // Get the selected value from cookie if not already set
    if (!$('#PilarID').val()) {
        var cookieValue = getCookie('selected_pilar');
        if (cookieValue) {
            $('#PilarID').val(cookieValue).trigger('change');
        }
    }
    
    // Cookie function
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
   
});
document.getElementById('isuStrategisForm').addEventListener('submit', function(event) {
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
    
    // If validation passes, let the form submission continue via AJAX
    // (This is handled by the event handler in index.blade.php)
});
</script>
