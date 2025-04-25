<form method="POST" action="{{ route('users.update', $user->id) }}" class="modal-form" id="userEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="name">Nama</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}">
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password (Biarkan kosong untuk menyimpan kata sandi sebelumnya)">
        <small class="form-text text-muted">Biarkan kosong untuk menyimpan kata sandi sebelumnya. Jika diisi, minimal 8 karakter.</small>
    </div>
    <div class="form-group">
        <label for="level">Level</label>
        <select name="level" id="level" class="form-control">
            <option value="1" {{ $user->level == 1 ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ $user->level == 2 ? 'selected' : '' }}>User</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit">Ubah</button>
    </div>
</form>

<script>
document.getElementById('userEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!name) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!email) {
        emptyFields.push('Email harus diisi');
    }
    
    // Check password length only if password is provided (since it's optional in edit form)
    if (password && password.length < 8) {
        emptyFields.push('Password minimal 8 karakter');
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
