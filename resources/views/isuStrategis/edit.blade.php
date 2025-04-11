<form action="{{ route('isu-strategis.update', $isuStrategis->IsuID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="PilarID">Pilar</label>
        <select name="PilarID" id="PilarID" class="form-control select2" required>
            @foreach($pilars as $pilar)
                <option value="{{ $pilar->PilarID }}" {{ $isuStrategis->PilarID == $pilar->PilarID ? 'selected' : '' }}>{{ $pilar->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $isuStrategis->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $isuStrategis->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $isuStrategis->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
