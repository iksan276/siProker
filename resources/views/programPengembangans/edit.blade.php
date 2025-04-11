<form action="{{ route('program-pengembangans.update', $programPengembangan->ProgramPengembanganID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="IsuID">Isu Strategis</label>
        <select name="IsuID" id="IsuID" class="form-control select2" required>
            @foreach($isuStrategis as $isu)
                <option value="{{ $isu->IsuID }}" {{ $programPengembangan->IsuID == $isu->IsuID ? 'selected' : '' }}>{{ $isu->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $programPengembangan->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $programPengembangan->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $programPengembangan->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
 
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
