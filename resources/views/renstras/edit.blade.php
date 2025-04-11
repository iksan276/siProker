<form action="{{ route('renstras.update', $renstra->RenstraID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $renstra->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="PeriodeMulai">Periode Mulai</label>
        <input type="number" name="PeriodeMulai" id="PeriodeMulai" class="form-control" min="2000" max="2100" value="{{ $renstra->PeriodeMulai }}" required>
    </div>
    <div class="form-group">
        <label for="PeriodeSelesai">Periode Selesai</label>
        <input type="number" name="PeriodeSelesai" id="PeriodeSelesai" class="form-control" min="2000" max="2100" value="{{ $renstra->PeriodeSelesai }}" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $renstra->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $renstra->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
