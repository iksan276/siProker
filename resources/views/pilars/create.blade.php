<form action="{{ route('pilars.store') }}" method="POST" class="modal-form">
    @csrf
    <div class="form-group">
        <label for="RenstraID">Renstra</label>
        <select name="RenstraID" id="RenstraID" class="form-control select2" required>
        <option value="" disabled selected></option>
            @foreach($renstras as $renstra)
                <option value="{{ $renstra->RenstraID }}">{{ $renstra->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
        <option value="Y">Non Aktif</option>
        <option value="N" selected>Aktif</option>
        </select>
    </div>
  
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
    </div>
</form>
