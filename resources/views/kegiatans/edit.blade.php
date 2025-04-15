<form action="{{ route('kegiatans.update', $kegiatan->KegiatanID) }}" method="POST" class="modal-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="IndikatorKinerjaID">Indikator Kinerja</label>
        <select name="IndikatorKinerjaID" id="IndikatorKinerjaID" class="form-control select2" required>
        <option value="" disabled selected></option>
            @foreach($indikatorKinerjas as $indikatorKinerja)
                <option value="{{ $indikatorKinerja->IndikatorKinerjaID }}" {{ $kegiatan->IndikatorKinerjaID == $indikatorKinerja->IndikatorKinerjaID ? 'selected' : '' }}>{{ $indikatorKinerja->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <input type="text" name="Nama" id="Nama" class="form-control" value="{{ $kegiatan->Nama }}" required>
    </div>
    <div class="form-group">
        <label for="TanggalMulai">Tanggal Mulai</label>
        <input type="datetime-local" name="TanggalMulai" id="TanggalMulai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('Y-m-d\TH:i') }}" required>
    </div>
    <div class="form-group">
        <label for="TanggalSelesai">Tanggal Selesai</label>
        <input type="datetime-local" name="TanggalSelesai" id="TanggalSelesai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('Y-m-d\TH:i') }}" required>
    </div>
    <div class="form-group">
        <label for="RincianKegiatan">Rincian Kegiatan</label>
        <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4" required>{{ $kegiatan->RincianKegiatan }}</textarea>
    </div>
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
