<form action="{{ route('meta-anggarans.update', $metaAnggaran->MetaAnggaranID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $metaAnggaran->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="NA">Status</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $metaAnggaran->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $metaAnggaran->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>

