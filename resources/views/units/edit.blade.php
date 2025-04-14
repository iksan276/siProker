<form action="{{ route('units.update', $unit->UnitID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $unit->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="NA">Status</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $unit->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $unit->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
