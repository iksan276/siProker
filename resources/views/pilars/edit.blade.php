<form action="{{ route('pilars.update', $pilar->PilarID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="RenstraID">Renstra</label>
        <select name="RenstraID" id="RenstraID" class="form-control select2" required>
            @foreach($renstras as $renstra)
                <option value="{{ $renstra->RenstraID }}" {{ $pilar->RenstraID == $renstra->RenstraID ? 'selected' : '' }}>{{ $renstra->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $pilar->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $pilar->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $pilar->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
  
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
