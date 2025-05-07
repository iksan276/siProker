<form action="{{ route('kegiatans.update', $kegiatan->KegiatanID) }}" method="POST" class="modal-form" id="kegiatanEditForm">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2" >
        <option value="" disabled selected></option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}" {{ $kegiatan->ProgramRektorID == $programRektor->ProgramRektorID ? 'selected' : '' }}>{{ $programRektor->Nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="Nama">Nama</label>
        <textarea name="Nama" id="Nama" class="form-control" rows="3">{{ $kegiatan->Nama }}</textarea>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="TanggalMulai">Tanggal Mulai</label>
                <input type="date" name="TanggalMulai" id="TanggalMulai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('Y-m-d') }}" onchange="validateDatesEdit()">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="TanggalSelesai">Tanggal Selesai</label>
                <input type="date" name="TanggalSelesai" id="TanggalSelesai" class="form-control" value="{{ \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('Y-m-d') }}" onchange="validateDatesEdit()">
                <small id="dateErrorEdit" class="text-danger" style="display: none;">Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.</small>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="RincianKegiatan">Rincian Kegiatan</label>
        <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4">{{ $kegiatan->RincianKegiatan }}</textarea>
    </div>
    
    <div class="form-group">
        <label>Apakah kegiatan ini memiliki sub kegiatan?</label>
        <div class="custom-control custom-radio">
            <input type="radio" id="has_sub_kegiatan_yes" name="has_sub_kegiatan" value="yes" class="custom-control-input" {{ $kegiatan->subKegiatans->count() > 0 ? 'checked' : '' }}>
            <label class="custom-control-label" for="has_sub_kegiatan_yes">Ya</label>
        </div>
        <div class="custom-control custom-radio">
            <input type="radio" id="has_sub_kegiatan_no" name="has_sub_kegiatan" value="no" class="custom-control-input" {{ $kegiatan->subKegiatans->count() == 0 ? 'checked' : '' }}>
            <label class="custom-control-label" for="has_sub_kegiatan_no">Tidak</label>
        </div>
    </div>
    
    <!-- RAB Section (always visible) -->
    <div id="rab_section">
        <h5 class="mt-4 mb-3">Rencana Anggaran Biaya (RAB)</h5>
        <div id="rab_container">
            @foreach($kegiatan->rabs->whereNull('SubKegiatanID') as $index => $rab)
                <div class="card mb-3 rab-item">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label>Komponen</label>
                                    <input type="text" name="existing_rabs[{{ $rab->RABID }}][Komponen]" class="form-control" value="{{ $rab->Komponen }}" required>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label>Volume</label>
                                    <input type="text" name="existing_rabs[{{ $rab->RABID }}][Volume]" class="form-control volume-input rab-calc" data-type="volume" value="{{ number_format($rab->Volume, 0, ',', '.') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>Satuan</label>
                                    <select name="existing_rabs[{{ $rab->RABID }}][Satuan]" class="form-control satuan-select select2" required>
                                        <option value="">Pilih</option>
                                        @foreach($satuans as $satuan)
                                            <option value="{{ $satuan->SatuanID }}" {{ $rab->Satuan == $satuan->SatuanID ? 'selected' : '' }}>{{ $satuan->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>Harga Satuan</label>
                                    <input type="text" name="existing_rabs[{{ $rab->RABID }}][HargaSatuan]" class="form-control harga-input rab-calc" data-type="harga" value="{{ number_format($rab->HargaSatuan, 0, ',', '.') }}" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mb-0">
                                    <label>Jumlah</label>
                                    <input type="text" class="form-control jumlah-input" value="{{ number_format($rab->Volume * $rab->HargaSatuan, 0, ',', '.') }}" readonly>
                                    <input type="hidden" name="existing_rabs[{{ $rab->RABID }}][Jumlah]" class="jumlah-hidden" value="{{ $rab->Volume * $rab->HargaSatuan }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group mb-0">
                                    <label>Status</label>
                                    <select name="existing_rabs[{{ $rab->RABID }}][Status]" class="form-control form-control-sm">
                                        <option value="N" {{ $rab->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="Y" {{ $rab->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="T" {{ $rab->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
                                        <option value="R" {{ $rab->Status == 'R' ? 'selected' : '' }}>Revisi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger remove-existing-rab" data-id="{{ $rab->RABID }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_rab">
            <i class="fas fa-plus"></i> Tambah RAB
        </button>
    </div>
    
    <!-- Sub Kegiatan Section -->
    <div id="sub_kegiatan_section" style="{{ $kegiatan->subKegiatans->count() > 0 ? '' : 'display: none;' }}">
        <h5 class="mt-4 mb-3">Sub Kegiatan</h5>
        <div id="sub_kegiatan_container">
            @foreach($kegiatan->subKegiatans as $index => $subKegiatan)
                <div class="card mb-3 sub-kegiatan-item">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Sub Kegiatan #{{ $index + 1 }}</h6>
                        <input type="hidden" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][id]" value="{{ $subKegiatan->SubKegiatanID }}">
                        <button type="button" class="btn btn-sm btn-danger remove-sub-kegiatan" data-id="{{ $subKegiatan->SubKegiatanID }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Sub Kegiatan</label>
                            <textarea name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][Nama]" class="form-control" rows="2" required>{{ $subKegiatan->Nama }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jadwal Mulai</label>
                                    <input type="date" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][JadwalMulai]" class="form-control sub-kegiatan-start" value="{{ \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jadwal Selesai</label>
                                    <input type="date" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][JadwalSelesai]" class="form-control sub-kegiatan-end" value="{{ \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][Status]" class="form-control">
                                <option value="N" {{ $subKegiatan->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Y" {{ $subKegiatan->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
                                <option value="T" {{ $subKegiatan->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
                                <option value="R" {{ $subKegiatan->Status == 'R' ? 'selected' : '' }}>Revisi</option>
                            </select>
                        </div>
                        
                        <div class="mt-3">
                            <h6>RAB Sub Kegiatan</h6>
                            <div class="rab-sub-container">
                                @foreach($subKegiatan->rabs as $rabIndex => $rab)
                                    <div class="card mb-2 rab-sub-item">
                                        <div class="card-body py-2">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Komponen</label>
                                                        <input type="text" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][Komponen]" class="form-control form-control-sm" value="{{ $rab->Komponen }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Volume</label>
                                                        <input type="text" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][Volume]" class="form-control form-control-sm volume-input rab-calc" data-type="volume" value="{{ number_format($rab->Volume, 0, ',', '.') }}" required>
                                                    </div>
                                                    </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Satuan</label>
                                                        <select name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][Satuan]" class="form-control form-control-sm satuan-select select2" required>
                                                            <option value="">Pilih</option>
                                                            @foreach($satuans as $satuan)
                                                                <option value="{{ $satuan->SatuanID }}" {{ $rab->Satuan == $satuan->SatuanID ? 'selected' : '' }}>{{ $satuan->Nama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Harga Satuan</label>
                                                        <input type="text" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][HargaSatuan]" class="form-control form-control-sm harga-input rab-calc" data-type="harga" value="{{ number_format($rab->HargaSatuan, 0, ',', '.') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Jumlah</label>
                                                        <input type="text" class="form-control form-control-sm jumlah-input" value="{{ number_format($rab->Volume * $rab->HargaSatuan, 0, ',', '.') }}" readonly>
                                                        <input type="hidden" name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][Jumlah]" class="jumlah-hidden" value="{{ $rab->Volume * $rab->HargaSatuan }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-0">
                                                        <label class="small">Status</label>
                                                        <select name="existing_sub_kegiatans[{{ $subKegiatan->SubKegiatanID }}][existing_rabs][{{ $rab->RABID }}][Status]" class="form-control form-control-sm">
                                                            <option value="N" {{ $rab->Status == 'N' ? 'selected' : '' }}>Menunggu</option>
                                                            <option value="Y" {{ $rab->Status == 'Y' ? 'selected' : '' }}>Disetujui</option>
                                                            <option value="T" {{ $rab->Status == 'T' ? 'selected' : '' }}>Ditolak</option>
                                                            <option value="R" {{ $rab->Status == 'R' ? 'selected' : '' }}>Revisi</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-sm btn-danger remove-existing-rab-sub" data-id="{{ $rab->RABID }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-rab-sub" data-subkegiatan-id="{{ $subKegiatan->SubKegiatanID }}">
                                <i class="fas fa-plus"></i> Tambah RAB
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add_sub_kegiatan">
            <i class="fas fa-plus"></i> Tambah Sub Kegiatan
        </button>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtnEdit">Ubah</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Initialize select2 for all existing select elements
    initSelect2ForRab();
    
    // Toggle sub kegiatan section based on radio button
    $('input[name="has_sub_kegiatan"]').change(function() {
        if ($(this).val() === 'yes') {
            $('#sub_kegiatan_section').show();
            // Add first sub kegiatan if none exists
            if ($('.sub-kegiatan-item').length === 0) {
                addSubKegiatan();
            }
        } else {
            $('#sub_kegiatan_section').hide();
        }
    });
    
    // Add Sub Kegiatan button click
    $('#add_sub_kegiatan').click(function() {
        addSubKegiatan();
    });
    
    // Add RAB button click
    $('#add_rab').click(function() {
        addRAB();
    });
    
    // Function to add a new sub kegiatan
    function addSubKegiatan() {
        const index = new Date().getTime(); // Use timestamp as unique index
        const html = `
            <div class="card mb-3 sub-kegiatan-item">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Sub Kegiatan Baru</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-sub-kegiatan">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Sub Kegiatan</label>
                        <textarea name="new_sub_kegiatans[${index}][Nama]" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jadwal Mulai</label>
                                <input type="date" name="new_sub_kegiatans[${index}][JadwalMulai]" class="form-control sub-kegiatan-start" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jadwal Selesai</label>
                                <input type="date" name="new_sub_kegiatans[${index}][JadwalSelesai]" class="form-control sub-kegiatan-end" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="new_sub_kegiatans[${index}][Status]" class="form-control">
                            <option value="N" selected>Menunggu</option>
                            <option value="Y">Disetujui</option>
                            <option value="T">Ditolak</option>
                            <option value="R">Revisi</option>
                        </select>
                    </div>
                    
                    <div class="mt-3">
                        <h6>RAB Sub Kegiatan</h6>
                        <div class="rab-sub-container">
                            <!-- RAB items for this sub kegiatan will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-new-rab-sub" data-index="${index}">
                            <i class="fas fa-plus"></i> Tambah RAB
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#sub_kegiatan_container').append(html);
        
        // Add first RAB for this sub kegiatan
        addRABForNewSubKegiatan(index);
        
        // Initialize date validation for this sub kegiatan
        initSubKegiatanDateValidation();
    }
    
    // Function to add a RAB for a new sub kegiatan
    function addRABForNewSubKegiatan(subKegiatanIndex) {
        const rabIndex = new Date().getTime(); // Use timestamp as unique index
        const html = `
            <div class="card mb-2 rab-sub-item">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Komponen</label>
                                <input type="text" name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Komponen]" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label class="small">Volume</label>
                                <input type="text" name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Volume]" class="form-control form-control-sm volume-input rab-calc" data-type="volume" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Satuan</label>
                                <select name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Satuan]" class="form-control form-control-sm satuan-select select2" required>
                                    <option value="">Pilih</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Harga Satuan</label>
                                <input type="text" name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][HargaSatuan]" class="form-control form-control-sm harga-input rab-calc" data-type="harga" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Jumlah</label>
                                <input type="text" class="form-control form-control-sm jumlah-input" readonly>
                                <input type="hidden" name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Jumlah]" class="jumlah-hidden">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Status</label>
                                <select name="new_sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Status]" class="form-control form-control-sm">
                                    <option value="N" selected>Menunggu</option>
                                    <option value="Y">Disetujui</option>
                                    <option value="T">Ditolak</option>
                                    <option value="R">Revisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-rab-sub">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $(`.sub-kegiatan-item:last .rab-sub-container`).append(html);
        
        // Initialize select2 for this RAB
        initSelect2ForRab();
        
        // Initialize number formatting for this RAB
        initNumberFormatting();
    }
    
    // Function to add a RAB for an existing sub kegiatan
    function addRABForExistingSubKegiatan(subKegiatanId) {
        const rabIndex = new Date().getTime(); // Use timestamp as unique index
        const html = `
            <div class="card mb-2 rab-sub-item">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Komponen</label>
                                <input type="text" name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][Komponen]" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label class="small">Volume</label>
                                <input type="text" name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][Volume]" class="form-control form-control-sm volume-input rab-calc" data-type="volume" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Satuan</label>
                                <select name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][Satuan]" class="form-control form-control-sm satuan-select select2" required>
                                    <option value="">Pilih</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                                    @endforeach
                                </select>
                                                       </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Harga Satuan</label>
                                <input type="text" name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][HargaSatuan]" class="form-control form-control-sm harga-input rab-calc" data-type="harga" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Jumlah</label>
                                <input type="text" class="form-control form-control-sm jumlah-input" readonly>
                                <input type="hidden" name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][Jumlah]" class="jumlah-hidden">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label class="small">Status</label>
                                <select name="existing_sub_kegiatans[${subKegiatanId}][new_rabs][${rabIndex}][Status]" class="form-control form-control-sm">
                                    <option value="N" selected>Menunggu</option>
                                    <option value="Y">Disetujui</option>
                                    <option value="T">Ditolak</option>
                                    <option value="R">Revisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-rab-sub">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $(`[data-subkegiatan-id="${subKegiatanId}"]`).closest('.card-body').find('.rab-sub-container').append(html);
        
        // Initialize select2 for this RAB
        initSelect2ForRab();
        
        // Initialize number formatting for this RAB
        initNumberFormatting();
    }
    
    // Function to add a new RAB for the main kegiatan
    function addRAB() {
        const index = new Date().getTime(); // Use timestamp as unique index
        const html = `
            <div class="card mb-3 rab-item">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Komponen</label>
                                <input type="text" name="new_rabs[${index}][Komponen]" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label>Volume</label>
                                <input type="text" name="new_rabs[${index}][Volume]" class="form-control volume-input rab-calc" data-type="volume" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Satuan</label>
                                <select name="new_rabs[${index}][Satuan]" class="form-control satuan-select select2" required>
                                    <option value="">Pilih</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Harga Satuan</label>
                                <input type="text" name="new_rabs[${index}][HargaSatuan]" class="form-control harga-input rab-calc" data-type="harga" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Jumlah</label>
                                <input type="text" class="form-control jumlah-input" readonly>
                                <input type="hidden" name="new_rabs[${index}][Jumlah]" class="jumlah-hidden">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <select name="new_rabs[${index}][Status]" class="form-control">
                                    <option value="N" selected>Menunggu</option>
                                    <option value="Y">Disetujui</option>
                                    <option value="T">Ditolak</option>
                                    <option value="R">Revisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger remove-rab mt-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#rab_container').append(html);
        
        // Initialize select2 for this RAB
        initSelect2ForRab();
        
        // Initialize number formatting for this RAB
        initNumberFormatting();
    }
    
    // Remove Sub Kegiatan
    $(document).on('click', '.remove-sub-kegiatan', function() {
        const subKegiatanId = $(this).data('id');
        
        if (subKegiatanId) {
            // This is an existing sub kegiatan, add a hidden field to mark it for deletion
            $('#kegiatanEditForm').append(`<input type="hidden" name="delete_sub_kegiatans[]" value="${subKegiatanId}">`);
        }
        
        $(this).closest('.sub-kegiatan-item').remove();
        
        // If no sub kegiatans left, add a new one
        if ($('.sub-kegiatan-item').length === 0 && $('#has_sub_kegiatan_yes').is(':checked')) {
            addSubKegiatan();
        }
    });
    
    // Remove RAB from Sub Kegiatan
    $(document).on('click', '.remove-rab-sub', function() {
        const rabContainer = $(this).closest('.rab-sub-container');
        if (rabContainer.find('.rab-sub-item').length > 1) {
            $(this).closest('.rab-sub-item').remove();
        } else {
            alert('Minimal harus ada satu RAB untuk sub kegiatan ini');
        }
    });
    
    // Remove existing RAB from Sub Kegiatan
    $(document).on('click', '.remove-existing-rab-sub', function() {
        const rabId = $(this).data('id');
        const rabContainer = $(this).closest('.rab-sub-container');
        
        // Add a hidden field to mark this RAB for deletion
        $('#kegiatanEditForm').append(`<input type="hidden" name="delete_rabs[]" value="${rabId}">`);
        
        if (rabContainer.find('.rab-sub-item').length > 1) {
            $(this).closest('.rab-sub-item').remove();
        } else {
            alert('Minimal harus ada satu RAB untuk sub kegiatan ini');
        }
    });
    
    // Remove RAB from main kegiatan
    $(document).on('click', '.remove-rab', function() {
        if ($('.rab-item').length > 1) {
            $(this).closest('.rab-item').remove();
        } else {
            alert('Minimal harus ada satu RAB');
        }
    });
    
    // Remove existing RAB from main kegiatan
    $(document).on('click', '.remove-existing-rab', function() {
        const rabId = $(this).data('id');
        
        // Add a hidden field to mark this RAB for deletion
        $('#kegiatanEditForm').append(`<input type="hidden" name="delete_rabs[]" value="${rabId}">`);
        
        if ($('.rab-item').length > 1) {
            $(this).closest('.rab-item').remove();
        } else {
            alert('Minimal harus ada satu RAB');
        }
    });
    
    // Add RAB to existing Sub Kegiatan
    $(document).on('click', '.add-rab-sub', function() {
        const subKegiatanId = $(this).data('subkegiatan-id');
        addRABForExistingSubKegiatan(subKegiatanId);
    });
    
    // Add RAB to new Sub Kegiatan
    $(document).on('click', '.add-new-rab-sub', function() {
        const subKegiatanIndex = $(this).data('index');
        addRABForNewSubKegiatan(subKegiatanIndex);
    });
    
    // Initialize select2 for RAB satuan fields
    function initSelect2ForRab() {
        $('.satuan-select').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    dropdownParent: $('#mainModal .modal-body'),
                    width: '100%',
                    placeholder: "Pilih satuan"
                });
            }
        });
    }
    
    // Initialize number formatting for currency inputs
    function initNumberFormatting() {
        $('.volume-input, .harga-input').off('input').on('input', function() {
            // Remove non-numeric characters
            let value = $(this).val().replace(/\D/g, '');
            
            // Format with thousand separators
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            
            $(this).val(value);
            
            // Calculate jumlah
            calculateJumlah($(this).closest('.row'));
        });
    }
    
    // Calculate jumlah (volume * harga)
    function calculateJumlah(container) {
        const volumeInput = container.find('.volume-input');
        const hargaInput = container.find('.harga-input');
        const jumlahInput = container.find('.jumlah-input');
        const jumlahHidden = container.find('.jumlah-hidden');
        
        if (volumeInput.val() && hargaInput.val()) {
            // Parse the formatted numbers
            const volume = parseInt(volumeInput.val().replace(/\./g, ''));
            const harga = parseInt(hargaInput.val().replace(/\./g, ''));
            
            // Calculate jumlah
            const jumlah = volume * harga;
            
            // Format and display jumlah
            jumlahInput.val(jumlah.toLocaleString('id-ID'));
            jumlahHidden.val(jumlah);
        } else {
            jumlahInput.val('');
            jumlahHidden.val('');
        }
    }
    
    // Initialize date validation for sub kegiatans
    function initSubKegiatanDateValidation() {
        $('.sub-kegiatan-start, .sub-kegiatan-end').off('change').on('change', function() {
            const subKegiatan = $(this).closest('.sub-kegiatan-item');
            const startDate = new Date(subKegiatan.find('.sub-kegiatan-start').val());
            const endDate = new Date(subKegiatan.find('.sub-kegiatan-end').val());
            
            if (startDate && endDate && endDate < startDate) {
                alert('Jadwal Selesai harus lebih besar atau sama dengan Jadwal Mulai');
                $(this).val('');
            }
            
            // Also validate against main kegiatan dates
            const mainStartDate = new Date($('#TanggalMulai').val());
            const mainEndDate = new Date($('#TanggalSelesai').val());
            
            if (mainStartDate && startDate && startDate < mainStartDate) {
                alert('Jadwal Mulai sub kegiatan tidak boleh sebelum Tanggal Mulai kegiatan utama');
                subKegiatan.find('.sub-kegiatan-start').val('');
            }
            
            if (mainEndDate && endDate && endDate > mainEndDate) {
                alert('Jadwal Selesai sub kegiatan tidak boleh setelah Tanggal Selesai kegiatan utama');
                subKegiatan.find('.sub-kegiatan-end').val('');
            }
        });
    }
    
    // Recalculate all jumlah values when inputs change
    $(document).on('input', '.rab-calc', function() {
        calculateJumlah($(this).closest('.row'));
    });
    
    // Initialize all number formatting
    initNumberFormatting();
    
    // Initialize all sub kegiatan date validation
    initSubKegiatanDateValidation();
    
    // Calculate all jumlah values on page load
    $('.rab-calc').each(function() {
        calculateJumlah($(this).closest('.row'));
    });
});

function validateDatesEdit() {
    const tanggalMulai = new Date(document.getElementById('TanggalMulai').value);
    const tanggalSelesai = new Date(document.getElementById('TanggalSelesai').value);
    const errorElement = document.getElementById('dateErrorEdit');
    const submitBtn = document.getElementById('submitBtnEdit');
    
    if (document.getElementById('TanggalMulai').value && document.getElementById('TanggalSelesai').value) {
        if (tanggalSelesai < tanggalMulai) {
            errorElement.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            errorElement.style.display = 'none';
            submitBtn.disabled = false;
            
            // Also validate sub kegiatan dates if they exist
            $('.sub-kegiatan-item').each(function() {
                const subStartDate = new Date($(this).find('.sub-kegiatan-start').val());
                const subEndDate = new Date($(this).find('.sub-kegiatan-end').val());
                
                if (subStartDate && subStartDate < tanggalMulai) {
                    $(this).find('.sub-kegiatan-start').val('');
                }
                
                if (subEndDate && subEndDate > tanggalSelesai) {
                    $(this).find('.sub-kegiatan-end').val('');
                }
            });
        }
    }
}

document.getElementById('kegiatanEditForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from traditional submission
    
    // Validate empty fields
    const programRektorID = document.getElementById('ProgramRektorID').value.trim();
    const nama = document.getElementById('Nama').value.trim();
    const tanggalMulai = document.getElementById('TanggalMulai').value.trim();
    const tanggalSelesai = document.getElementById('TanggalSelesai').value.trim();
    const rincianKegiatan = document.getElementById('RincianKegiatan').value.trim();
    const hasSubKegiatan = $('input[name="has_sub_kegiatan"]:checked').val();
    
    // Create an array to store error messages
    let emptyFields = [];
    
    // Check each field and add to error messages if empty
    if (!programRektorID) {
        emptyFields.push('Program Rektor harus dipilih');
    }
    
    if (!nama) {
        emptyFields.push('Nama harus diisi');
    }
    
    if (!tanggalMulai) {
        emptyFields.push('Tanggal Mulai harus diisi');
    }
    
    if (!tanggalSelesai) {
        emptyFields.push('Tanggal Selesai harus diisi');
    }
    
    if (!rincianKegiatan) {
        emptyFields.push('Rincian Kegiatan harus diisi');
    }
    
    if (!hasSubKegiatan) {
        emptyFields.push('Pilihan Sub Kegiatan harus dipilih');
    }
    
    // Validate RABs for main kegiatan
    let rabValid = true;
    $('.rab-item').each(function(index) {
        const komponen = $(this).find('input[name*="[Komponen]"]').val().trim();
        const volume = $(this).find('input[name*="[Volume]"]').val().trim();
        const satuan = $(this).find('select[name*="[Satuan]"]').val();
        const hargaSatuan = $(this).find('input[name*="[HargaSatuan]"]').val().trim();
        
        if (!komponen || !volume || !satuan || !hargaSatuan) {
            rabValid = false;
            emptyFields.push(`RAB #${index + 1} harus diisi lengkap`);
        }
    });
    
    // Validate sub kegiatans if has_sub_kegiatan is yes
    if (hasSubKegiatan === 'yes') {
        let subKegiatanValid = true;
        
        // Validate existing sub kegiatans
        $('.sub-kegiatan-item').each(function(index) {
            const subNama = $(this).find('textarea[name*="[Nama]"]').val().trim();
            const subJadwalMulai = $(this).find('input[name*="[JadwalMulai]"]').val().trim();
            const subJadwalSelesai = $(this).find('input[name*="[JadwalSelesai]"]').val().trim();
            
            if (!subNama || !subJadwalMulai || !subJadwalSelesai) {
                subKegiatanValid = false;
                emptyFields.push(`Sub Kegiatan #${index + 1} harus diisi lengkap`);
            }
            
            // Validate RABs for this sub kegiatan
            $(this).find('.rab-sub-item').each(function(rabIndex) {
                const komponen = $(this).find('input[name*="[Komponen]"]').val().trim();
                const volume = $(this).find('input[name*="[Volume]"]').val().trim();
                const satuan = $(this).find('select[name*="[Satuan]"]').val();
                const hargaSatuan = $(this).find('input[name*="[HargaSatuan]"]').val().trim();
                
                if (!komponen || !volume || !satuan || !hargaSatuan) {
                    subKegiatanValid = false;
                    emptyFields.push(`RAB #${rabIndex + 1} pada Sub Kegiatan #${index + 1} harus diisi lengkap`);
                }
            });
        });
    }
    
    // If there are empty fields, show the error message
    if (emptyFields.length > 0) {
        const errorList = '<ul style="text-align:left;margin-left:40px;margin-right:50px" class="text-danger">' + 
            emptyFields.map(error => `<li>${error}</li>`).join('') + 
            '</ul>';
            
        Swal.fire({
            title: 'Validasi Inputan',
            html: errorList,
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    // Check if tanggal selesai is valid
    if (new Date(tanggalSelesai) < new Date(tanggalMulai)) {
        Swal.fire({
            title: 'Validasi Inputan',
            html: 'Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
});

// Run validation on page load
document.addEventListener('DOMContentLoaded', function() {
    validateDatesEdit();
    
    // Calculate all jumlah values
    $('.rab-calc').each(function() {
        calculateJumlah($(this).closest('.row'));
    });
});

// Helper function to calculate jumlah for any row
function calculateJumlah(container) {
    const volumeInput = container.find('.volume-input');
    const hargaInput = container.find('.harga-input');
    const jumlahInput = container.find('.jumlah-input');
    const jumlahHidden = container.find('.jumlah-hidden');
    
    if (volumeInput.val() && hargaInput.val()) {
        // Parse the formatted numbers
        const volume = parseInt(volumeInput.val().replace(/\./g, ''));
        const harga = parseInt(hargaInput.val().replace(/\./g, ''));
        
        // Calculate jumlah
        const jumlah = volume * harga;
        
        // Format and display jumlah
        jumlahInput.val(jumlah.toLocaleString('id-ID'));
        jumlahHidden.val(jumlah);
    } else {
        jumlahInput.val('');
        jumlahHidden.val('');
    }
}
</script>

