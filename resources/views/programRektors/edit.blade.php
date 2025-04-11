<form action="{{ route('program-rektors.update', $programRektor->ProgramRektorID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="ProgramPengembanganID">Program Pengembangan</label>
        <select name="ProgramPengembanganID" id="ProgramPengembanganID" class="form-control select2" required>
            @foreach($programPengembangans as $program)
                <option value="{{ $program->ProgramPengembanganID }}" {{ $programRektor->ProgramPengembanganID == $program->ProgramPengembanganID ? 'selected' : '' }}>{{ $program->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $programRektor->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="Tahun">Tahun</label>
        <input type="number" name="Tahun" id="Tahun" class="form-control" min="2000" max="2100" value="{{ $programRektor->Tahun }}" required>
    </div>
    <div class="form-group">
        <label for="NA">NA</label>
        <select name="NA" id="NA" class="form-control" required>
            <option value="Y" {{ $programRektor->NA == 'Y' ? 'selected' : '' }}>Non Aktif</option>
            <option value="N" {{ $programRektor->NA == 'N' ? 'selected' : '' }}>Aktif</option>
        </select>
    </div>
 
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
