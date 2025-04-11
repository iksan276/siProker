<form action="{{ route('renstras.store') }}" method="POST" class="modal-form">
    @csrf
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="PeriodeMulai">Periode Mulai</label>
        <input type="number" name="PeriodeMulai" id="PeriodeMulai" class="form-control" min="2000" max="2100" required>
    </div>
    <div class="form-group">
        <label for="PeriodeSelesai">Periode Selesai</label>
        <input type="number" name="PeriodeSelesai" id="PeriodeSelesai" class="form-control" min="2000" max="2100" required>
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
