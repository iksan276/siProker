<form action="{{ route('indikator-kinerjas.store') }}" method="POST" class="modal-form">
    @csrf
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2" required>
            <option value="">-- Pilih Program Rektor --</option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}">{{ $programRektor->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="SatuanID">Satuan</label>
        <select name="SatuanID" id="SatuanID" class="form-control select2" required>
            <option value="">-- Pilih Satuan --</option>
            @foreach($satuans as $satuan)
                <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Bobot">Bobot</label>
        <input type="number" name="Bobot" id="Bobot" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="HargaSatuan">Harga Satuan</label>
        <input type="number" name="HargaSatuan" id="HargaSatuan" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="Jumlah">Jumlah</label>
        <input type="number" name="Jumlah" id="Jumlah" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="MetaAnggaranID">Meta Anggaran</label>
        <select name="MetaAnggaranID[]" id="MetaAnggaranID" class="form-control select2" multiple required>
            @foreach($metaAnggarans as $metaAnggaran)
                <option value="{{ $metaAnggaran->MetaAnggaranID }}">{{ $metaAnggaran->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="UnitTerkaitID">Unit Terkait</label>
        <select name="UnitTerkaitID" id="UnitTerkaitID" class="form-control select2" required>
            <option value="">-- Pilih Unit --</option>
            @foreach($units as $unit)
                <option value="{{ $unit->UnitID }}">{{ $unit->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="NA">Status</label>
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
