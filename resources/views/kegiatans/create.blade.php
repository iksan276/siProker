<form action="{{ route('kegiatans.store') }}" method="POST" class="modal-form" id="kegiatanForm">
    @csrf
        
    <div class="form-group">
        <label for="ProgramRektorID">Program Rektor</label>
        <select name="ProgramRektorID" id="ProgramRektorID" class="form-control select2">
            <option value="" disabled {{ !isset($selectedProgramRektorObj) ? 'selected' : '' }}></option>
            @foreach($programRektors as $programRektor)
                <option value="{{ $programRektor->ProgramRektorID }}" 
                    {{ (isset($selectedProgramRektor) && $selectedProgramRektor == $programRektor->ProgramRektorID) || 
                       (isset($selectedProgramRektorObj) && $selectedProgramRektorObj->ProgramRektorID == $programRektor->ProgramRektorID) ? 'selected' : '' }}>
                    {{ $programRektor->Nama }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Add this alert div below the Program Rektor select -->
    <!-- Program Rektor Info in a more compact single-row layout -->
    <div id="programRektorInfo" class="alert alert-info mt-2 py-2" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <span class="badge badge-primary">Info Program Rektor</span>
            </div>
            <div class="d-flex flex-wrap">
                <div class="mr-3"><small><strong>Jumlah:</strong> <span id="infoJumlahKegiatan">-</span></small></div>
                <div class="mr-3"><small><strong>Satuan:</strong> <span id="infoSatuan">-</span></small></div>
                <div class="mr-3"><small><strong>Harga:</strong> <span id="infoHargaSatuan">-</span></small></div>
                <div class="mr-3"><small><strong>Total:</strong> <span id="infoTotal">-</span></small></div>
                <div><small><strong>Penanggung Jawab:</strong> <span id="infoPenanggungJawab">-</span></small></div>
            </div>
        </div>
    </div>

    <!-- Budget warning alert -->
    <div id="budgetWarning" class="alert alert-danger mt-2 py-2" style="display: none;">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <strong>Peringatan Anggaran!</strong> Total anggaran melebihi batas Program Rektor.
                <div>Total RAB: <span id="currentTotal">Rp 0</span> | Batas: <span id="budgetLimit">Rp 0</span></div>
            </div>
        </div>
    </div>

    <div id="loadingIndicator" style="display: none;" class="text-center my-4">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2">Memuat data Program Rektor...</p>
    </div>
    <!-- Form content that will be shown only after Program Rektor is selected -->
    <div id="formContent" style="display: none;">
        <div class="form-group">
            <label for="Nama">Nama</label>
            <textarea name="Nama" id="Nama" class="form-control" rows="3"></textarea>
        </div>
        
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="TanggalMulai">Tanggal Mulai</label>
                    <input type="date" name="TanggalMulai" id="TanggalMulai" class="form-control" onchange="validateDates()">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="TanggalSelesai">Tanggal Selesai</label>
                    <input type="date" name="TanggalSelesai" id="TanggalSelesai" class="form-control" onchange="validateDates()">
                    <small id="dateError" class="text-danger" style="display: none;">Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai.</small>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="RincianKegiatan">Rincian Kegiatan</label>
            <textarea name="RincianKegiatan" id="RincianKegiatan" class="form-control" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <label>Apakah kegiatan ini memiliki sub kegiatan?</label>
            <div class="custom-control custom-radio">
                <input type="radio" id="has_sub_kegiatan_yes" name="has_sub_kegiatan" value="yes" class="custom-control-input">
                <label class="custom-control-label" for="has_sub_kegiatan_yes">Ya</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" id="has_sub_kegiatan_no" name="has_sub_kegiatan" value="no" class="custom-control-input" checked>
                <label class="custom-control-label" for="has_sub_kegiatan_no">Tidak</label>
            </div>
        </div>
        
        <!-- Sub Kegiatan Section (initially hidden) -->
        <div id="sub_kegiatan_section" style="display: none;">
            <label class="mb-3">Sub Kegiatan</label>
            <div id="sub_kegiatan_container">
                <!-- Sub kegiatan items will be added here -->
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add_sub_kegiatan">
                <i class="fas fa-plus"></i> Tambah Sub Kegiatan
            </button>
        </div>
        
        <!-- RAB Section (always visible) -->
        <div id="rab_section">
            <label class="mb-3">Rencana Anggaran Biaya (RAB)</label>
            <div id="rab_container">
                <!-- RAB items will be added here -->
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add_rab">
                <i class="fas fa-plus"></i> Tambah RAB
            </button>
        </div>
    </div>
    
    <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary" type="submit" id="submitBtn">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Global variable to store Program Rektor total budget
    let programRektorTotal = 0;
    let formContentVisible = false;
    
    // Initially hide the form content
    $('#formContent').hide();
    
    // Add this to the existing $(document).ready function
    $('#ProgramRektorID').on('change', function() {
    const programRektorId = $(this).val();
    
    // Hide form content when changing Program Rektor
    $('#formContent').hide();
    $('#budgetWarning').hide();
    formContentVisible = false;
    
    if (programRektorId) {
        // Show loading indicator
        $('#loadingIndicator').show();
        $('#programRektorInfo').show();
        $('#infoJumlahKegiatan, #infoSatuan, #infoHargaSatuan, #infoTotal, #infoPenanggungJawab').text('Loading...');
        
        // Fetch program rektor details
        $.ajax({
            url: `/api/program-rektor-details/${programRektorId}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Hide loading indicator
                $('#loadingIndicator').hide();
                
                // Format currency values
                const hargaSatuan = parseInt(data.hargaSatuan).toLocaleString('id-ID');
                const total = parseInt(data.total).toLocaleString('id-ID');
                
                // Rest of the success handler remains the same...
                programRektorTotal = parseInt(data.total);
                
                $('#infoJumlahKegiatan').text(data.jumlahKegiatan || '-');
                $('#infoSatuan').text(data.satuan || '-');
                $('#infoHargaSatuan').text('Rp ' + hargaSatuan);
                $('#infoTotal').text('Rp ' + total);
                $('#infoPenanggungJawab').text(data.penanggungJawab || '-');
                $('#budgetLimit').text('Rp ' + total);
                
                $('#programRektorInfo').show();
                $('#formContent').show();
                formContentVisible = true;
                
                validateTotalBudget();
            },
            error: function() {
                // Hide loading indicator on error
                $('#loadingIndicator').hide();
                $('#programRektorInfo').hide();
                $('#formContent').hide();
                $('#budgetWarning').hide();
                formContentVisible = false;
                
                Swal.fire({
                    title: 'Error',
                    text: 'Gagal memuat informasi Program Rektor',
                    icon: 'error'
                });
            }
        });
    } else {
        // Hide loading indicator and other elements if no program rektor is selected
        $('#loadingIndicator').hide();
        $('#programRektorInfo').hide();
        $('#formContent').hide();
        $('#budgetWarning').hide();
        formContentVisible = false;
    }
});


    // Trigger the change event if a program rektor is already selected
    if ($('#ProgramRektorID').val()) {
        $('#ProgramRektorID').trigger('change');
    }

    // Get the selected values from cookies if not already set
    if (!$('#ProgramRektorID').val()) {
        var programRektorCookie = getCookie('selected_program_rektor');
        if (programRektorCookie) {
            $('#ProgramRektorID').val(programRektorCookie).trigger('change');
        }
    }
    
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    // Toggle sub kegiatan section based on radio button
    $('input[name="has_sub_kegiatan"]').change(function() {
        if ($(this).val() === 'yes') {
            $('#sub_kegiatan_section').show();
            // Add first sub kegiatan if none exists
            if ($('.sub-kegiatan-item').length === 0) {
                addSubKegiatan();
            }
            validateTotalBudget(); // Validate after toggling
        } else {
            $('#sub_kegiatan_section').hide();
            validateTotalBudget(); // Validate after toggling
        }
    });
    
    // Add Sub Kegiatan button click
    $('#add_sub_kegiatan').click(function() {
        addSubKegiatan();
        validateTotalBudget(); // Validate after adding
    });
    
    // Add RAB button click
    $('#add_rab').click(function() {
        addRAB();
        validateTotalBudget(); // Validate after adding
    });
    
    // Function to add a new sub kegiatan
    function addSubKegiatan() {
        const index = $('.sub-kegiatan-item').length;
        const html = `
            <div class="card mb-3 sub-kegiatan-item">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Sub Kegiatan #${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-sub-kegiatan">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Sub Kegiatan</label>
                        <textarea name="sub_kegiatans[${index}][Nama]" class="form-control" rows="2" ></textarea>
                    </div>
                    <div class="form-group">
                        <label>Jadwal</label>
                        <input type="text" class="form-control date-range-picker" id="date_range_${index}" >
                        <input type="hidden" name="sub_kegiatans[${index}][JadwalMulai]" class="date-start-hidden">
                        <input type="hidden" name="sub_kegiatans[${index}][JadwalSelesai]" class="date-end-hidden">
                    </div>
                     <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="sub_kegiatans[${index}][Catatan]" class="form-control" rows="2" ></textarea>
                    </div>
                    
                    <div class="mt-3">
                        <h6>RAB Sub Kegiatan</h6>
                        <div class="rab-sub-container">
                            <!-- RAB items for this sub kegiatan will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-rab-sub" data-index="${index}">
                            <i class="fas fa-plus"></i> Tambah RAB
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        $('#sub_kegiatan_container').append(html);
        
        // Add first RAB for this sub kegiatan
        addRABForSubKegiatan(index, 0);
        
        // Initialize date range picker for this sub kegiatan
        initDateRangePicker(index);
    }

    // Add this function to initialize the date range picker
    function initDateRangePicker(index) {
        $(`#date_range_${index}`).attr('placeholder', 'DD/MM/YYYY - DD/MM/YYYY');
            $(`#date_range_${index}`).val('');
            
        $(`#date_range_${index}`).daterangepicker({
            opens: 'left',
            autoApply: true,
            autoUpdateInput: false,
            locale: {
                format: 'DD/MM/YYYY',
                separator: ' - ',
                applyLabel: 'Pilih',
                cancelLabel: 'Batal',
                fromLabel: 'Dari',
                toLabel: 'Sampai',
                customRangeLabel: 'Custom',
                weekLabel: 'W',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1
            }
        });
         // Handle the apply event
         $(`#date_range_${index}`).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            const subKegiatan = $(this).closest('.sub-kegiatan-item');
            subKegiatan.find('.date-start-hidden').val(picker.startDate.format('YYYY-MM-DD'));
            subKegiatan.find('.date-end-hidden').val(picker.endDate.format('YYYY-MM-DD'));
            
            // Validate against main kegiatan dates
            validateSubKegiatanDateRange(subKegiatan, picker.startDate, picker.endDate);
        });
        
        // Handle the cancel event
        $(`#date_range_${index}`).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            const subKegiatan = $(this).closest('.sub-kegiatan-item');
            subKegiatan.find('.date-start-hidden').val('');
            subKegiatan.find('.date-end-hidden').val('');
        });
    }

    // Add this function to validate the date range against main kegiatan dates
    function validateSubKegiatanDateRange(subKegiatan, start, end) {
        // Get main dates and strip time components by using YYYY-MM-DD format
        const mainStartStr = $('#TanggalMulai').val();
        const mainEndStr = $('#TanggalSelesai').val();
        
        // Create date objects using only the date part
        const mainStartDate = mainStartStr ? new Date(mainStartStr) : null;
        const mainEndDate = mainEndStr ? new Date(mainEndStr) : null;
        
        // Convert moment objects to date objects and set time to 00:00:00
        const startDate = start ? new Date(start.format('YYYY-MM-DD')) : null;
        const endDate = end ? new Date(end.format('YYYY-MM-DD')) : null;
        
        if (mainStartDate && startDate && startDate < mainStartDate) {
            alert('Jadwal Mulai sub kegiatan tidak boleh sebelum Tanggal Mulai kegiatan utama');
            // Reset the date range picker
            const dateRangePicker = subKegiatan.find('.date-range-picker').data('daterangepicker');
            dateRangePicker.setStartDate(moment(mainStartStr));
            subKegiatan.find('.date-start-hidden').val(mainStartStr);
        }
        
        if (mainEndDate && endDate && endDate > mainEndDate) {
            alert('Jadwal Selesai sub kegiatan tidak boleh setelah Tanggal Selesai kegiatan utama');
            // Reset the date range picker
            const dateRangePicker = subKegiatan.find('.date-range-picker').data('daterangepicker');
            dateRangePicker.setEndDate(moment(mainEndStr));
            subKegiatan.find('.date-end-hidden').val(mainEndStr);
        }
    }
    
    // Function to add a RAB for a sub kegiatan
    function addRABForSubKegiatan(subKegiatanIndex, rabIndex) {
        const html = `
            <div class="card mb-2 rab-sub-item">
                <div class="card-body py-2">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-11">
                            <div class="form-group mb-0">
                                <label class="small">Komponen</label>
                                <textarea name="sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Komponen]" class="form-control form-control-sm" rows="2" ></textarea>
                            </div>
                        </div>
                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-sm btn-danger remove-rab-sub mt-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">Volume</label>
                                <input type="text" name="sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Volume]" class="form-control form-control-sm volume-input rab-calc" data-type="volume" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small d-block">Satuan</label>
                                <select name="sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Satuan]" class="form-control form-control-sm select2" >
                                    <option value="">Pilih</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">Harga Satuan</label>
                                <input type="text" name="sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][HargaSatuan]" class="form-control form-control-sm harga-input rab-calc" data-type="harga" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="small">Jumlah</label>
                                <input type="text" class="form-control form-control-sm jumlah-input" >
                                <input type="hidden" name="sub_kegiatans[${subKegiatanIndex}][rabs][${rabIndex}][Jumlah]" class="jumlah-hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $(`.sub-kegiatan-item:eq(${subKegiatanIndex}) .rab-sub-container`).append(html);
        $(`.sub-kegiatan-item:eq(${subKegiatanIndex}) .rab-sub-container .rab-sub-item:last-child select.select2`).each(function() {
            $(this).select2({
                dropdownParent: $('#mainModal .modal-body'),
                width: '100%'
            });
            
            // Apply custom styling to this specific element
            var $container = $(this).next('.select2-container');
            
            // Style the selection container
            $container.find('.select2-selection--single').css({
                'height': '33px',
                'padding': '0.15rem 0.75rem',
                'border': '1px solid #d1d3e2',
                'border-radius': '0.35rem'
            });
            
            // Style the rendered text and center the placeholder
            $container.find('.select2-selection__rendered').css({
                'line-height': '1.5',
                'padding-left': '0',
                'padding-top': '0.15rem',
                'padding-bottom': '0.15rem',
                'color': '#6e707e',
            });
            
            // Style the dropdown arrow
            $container.find('.select2-selection__arrow').css({
                'height': '33px'
            });
            
            // Namespace the event handler to avoid affecting modals
            var selectId = $(this).attr('id') || 'select-' + Math.random().toString(36).substring(2, 15);
            $(this).attr('id', selectId);
            
            // Remove any previous event handlers
            $(document).off('select2:open.' + selectId);
            
            // Add namespaced event handler
            $(document).on('select2:open.' + selectId, function() {
                // Only target dropdowns that are not in modals
                $('.select2-dropdown').each(function() {
                    if ($(this).closest('.modal').length === 0) {
                        $(this).css({
                            'font-size': '0.875rem'
                        });
                        
                        $(this).find('.select2-search__field').css({
                            'height': '28px',
                            'padding': '2px 6px'
                        });
                        
                        $(this).find('.select2-results__option').css({
                            'padding': '4px 8px',
                            'min-height': '28px'
                        });
                    }
                });
            });
        });

        // Initialize number formatting for this RAB
        initNumberFormatting();
    }
    
    // Function to add a new RAB for the main kegiatan
    function addRAB() {
        const index = $('.rab-item').length;
        const html = `
            <div class="card mb-3 rab-item">
                <div class="card-body py-3">
                    <div class="row align-items-center mb-2">
                        <div class="col-md-11">
                            <div class="form-group mb-0">
                                <label>Komponen</label>
                                <textarea name="rabs[${index}][Komponen]" class="form-control" rows="2" ></textarea>
                            </div>
                        </div>
                     
                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-sm btn-danger remove-rab mt-4">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row align-items-center">
                       <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Volume</label>
                                <input type="text" name="rabs[${index}][Volume]" class="form-control volume-input rab-calc" data-type="volume" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="d-block">Satuan</label>
                                <select name="rabs[${index}][Satuan]" class="form-control select2" >
                                    <option value="">Pilih</option>
                                    @foreach($satuans as $satuan)
                                        <option value="{{ $satuan->SatuanID }}">{{ $satuan->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Harga Satuan</label>
                                <input type="text" name="rabs[${index}][HargaSatuan]" class="form-control harga-input rab-calc" data-type="harga" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Jumlah</label>
                                <input type="text" class="form-control jumlah-input" >
                                <input type="hidden" name="rabs[${index}][Jumlah]" class="jumlah-hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#rab_container').append(html);
        
        $('#rab_container .rab-item:last-child select.select2').select2({
            dropdownParent: $('#mainModal .modal-body'),
            width: '100%'
        });

        // Initialize number formatting for this RAB
        initNumberFormatting();
    }

    // Remove Sub Kegiatan
    $(document).on('click', '.remove-sub-kegiatan', function() {
        if ($('.sub-kegiatan-item').length > 0) {
            $(this).closest('.sub-kegiatan-item').remove();
            // Renumber the remaining sub kegiatans
            $('.sub-kegiatan-item').each(function(index) {
                $(this).find('h6').text(`Sub Kegiatan #${index + 1}`);
            });
            validateTotalBudget(); // Validate after removing
        } 
    });
    
    // Remove RAB from Sub Kegiatan
    $(document).on('click', '.remove-rab-sub', function() {
        const rabContainer = $(this).closest('.rab-sub-container');
        if (rabContainer.find('.rab-sub-item').length > 0) {
            $(this).closest('.rab-sub-item').remove();
            validateTotalBudget(); // Validate after removing
        }
    });
    
    // Remove RAB from main kegiatan
    $(document).on('click', '.remove-rab', function() {
        if ($('.rab-item').length > 0) {
            $(this).closest('.rab-item').remove();
            validateTotalBudget(); // Validate after removing
        } 
    });
    
    // Add RAB to Sub Kegiatan
    $(document).ready(function() {
        // Unbind and rebind the add-rab-sub click event to prevent double execution
        $(document).off('click', '.add-rab-sub').on('click', '.add-rab-sub', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            const subKegiatanIndex = $(this).data('index');
            const rabCount = $(this).closest('.card-body').find('.rab-sub-item').length;
            
            // Call the existing function but ensure it only runs once
            if (typeof addRABForSubKegiatan === 'function') {
                addRABForSubKegiatan(subKegiatanIndex, rabCount);
                validateTotalBudget(); // Validate after adding
            }
        });
    });
    
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
            calculateJumlah($(this).closest('.card-body'));
            
            // Validate total budget after any change
            validateTotalBudget();
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
    
    // Function to validate total budget against Program Rektor limit
    function validateTotalBudget() {
        if (!formContentVisible || programRektorTotal < 0) {
            return; // Skip validation if form is not visible or program rektor total is not set
        }
        
        let totalBudget = 0;
        
        // Calculate total from main RABs
        $('.rab-item').each(function() {
            const jumlahHidden = $(this).find('.jumlah-hidden').val();
            if (jumlahHidden) {
                totalBudget += parseInt(jumlahHidden);
            }
        });
        
        // Calculate total from sub kegiatan RABs if sub kegiatan is enabled
        if ($('#has_sub_kegiatan_yes').is(':checked')) {
            $('.sub-kegiatan-item').each(function() {
                $(this).find('.rab-sub-item').each(function() {
                    const jumlahHidden = $(this).find('.jumlah-hidden').val();
                    if (jumlahHidden) {
                        totalBudget += parseInt(jumlahHidden);
                    }
                });
            });
        }
        
        // Update current total display
        $('#currentTotal').text('Rp ' + totalBudget.toLocaleString('id-ID'));
        
        // Check if total budget exceeds program rektor total
        if (totalBudget > programRektorTotal) {
            $('#budgetWarning').show();
            $('#submitBtn').prop('disabled', true);
            
            // Highlight the budget warning
            $('#budgetWarning').removeClass('alert-danger').addClass('alert-danger');
            setTimeout(function() {
                $('#budgetWarning').removeClass('alert-danger').addClass('alert-danger');
            }, 200);
            if ($('#budgetWarning').is(':visible')) {
            Swal.fire({
            title: 'Peringatan Anggaran!',
            html: `Total anggaran melebihi batas Program Rektor.<br><br>
                  <div class="text-left">
                    <strong>Total RAB:</strong> Rp ${totalBudget.toLocaleString('id-ID')}<br>
                    <strong>Batas:</strong> Rp ${programRektorTotal.toLocaleString('id-ID')}<br>
                    <strong>Selisih:</strong> Rp ${(totalBudget - programRektorTotal).toLocaleString('id-ID')}
                  </div>`,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Mengerti'
        });
    }
        } else {
            $('#budgetWarning').hide();
            $('#submitBtn').prop('disabled', false);
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
        calculateJumlah($(this).closest('.card-body'));
        validateTotalBudget();
    });
    
    // Show the appropriate section based on initial selection
    if ($('#has_sub_kegiatan_yes').is(':checked')) {
        $('#sub_kegiatan_section').show();
        addSubKegiatan();
    } else {
        $('#sub_kegiatan_section').hide();
    }
    
    // Add first RAB item for main kegiatan
    addRAB();
});

// Update the validateDates function
function validateDates() {
    // Get date strings
    const tanggalMulaiStr = document.getElementById('TanggalMulai').value;
    const tanggalSelesaiStr = document.getElementById('TanggalSelesai').value;
    
    // Create date objects using only the date part
    const tanggalMulai = tanggalMulaiStr ? new Date(tanggalMulaiStr) : null;
    const tanggalSelesai = tanggalSelesaiStr ? new Date(tanggalSelesaiStr) : null;
    
    const errorElement = document.getElementById('dateError');
    const submitBtn = document.getElementById('submitBtn');
    
    if (tanggalMulaiStr && tanggalSelesaiStr) {
        if (tanggalSelesai < tanggalMulai) {
            errorElement.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            errorElement.style.display = 'none';
            submitBtn.disabled = false;
            
            // Update sub kegiatan date ranges if they exist
            $('.sub-kegiatan-item').each(function() {
                const dateRangePicker = $(this).find('.date-range-picker').data('daterangepicker');
                if (dateRangePicker) {
                    // Convert moment objects to date objects using only the date part
                    const startDate = new Date(dateRangePicker.startDate.format('YYYY-MM-DD'));
                    const endDate = new Date(dateRangePicker.endDate.format('YYYY-MM-DD'));
                    
                    if (startDate && startDate < tanggalMulai) {
                        dateRangePicker.setStartDate(moment(tanggalMulaiStr));
                        $(this).find('.date-start-hidden').val(tanggalMulaiStr);
                    }
                    
                    if (endDate && endDate > tanggalSelesai) {
                        dateRangePicker.setEndDate(moment(tanggalSelesaiStr));
                        $(this).find('.date-end-hidden').val(tanggalSelesaiStr);
                    }
                }
            });
        }
    }
}

document.getElementById('kegiatanForm').addEventListener('submit', function(event) {
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
        const komponen = $(this).find('textarea[name$="[Komponen]"]').val().trim();
        const volume = $(this).find('input[name$="[Volume]"]').val().trim();
        const satuan = $(this).find('select[name$="[Satuan]"]').val();
        const hargaSatuan = $(this).find('input[name$="[HargaSatuan]"]').val().trim();
        
        if (!komponen || !volume || !satuan || !hargaSatuan) {
            rabValid = false;
            emptyFields.push(`RAB #${index + 1} harus diisi lengkap`);
        }
    });

    // Validate sub kegiatans if has_sub_kegiatan is yes
    if (hasSubKegiatan === 'yes') {
        let subKegiatanValid = true;
        $('.sub-kegiatan-item').each(function(index) {
            const subNama = $(this).find('textarea[name^="sub_kegiatans"]').val().trim();
            const subJadwalMulai = $(this).find('input[name$="[JadwalMulai]"]').val().trim();
            const subJadwalSelesai = $(this).find('input[name$="[JadwalSelesai]"]').val().trim();
            
            if (!subNama || !subJadwalMulai || !subJadwalSelesai) {
                subKegiatanValid = false;
                emptyFields.push(`Sub Kegiatan #${index + 1} harus diisi lengkap`);
            }
            
            // Validate RABs for this sub kegiatan
            $(this).find('.rab-sub-item').each(function(rabIndex) {
                const komponen = $(this).find('textarea[name$="[Komponen]"]').val().trim();
                const volume = $(this).find('input[name$="[Volume]"]').val().trim();
                const satuan = $(this).find('select[name$="[Satuan]"]').val();
                const hargaSatuan = $(this).find('input[name$="[HargaSatuan]"]').val().trim();
                
                if (!komponen || !volume || !satuan || !hargaSatuan) {
                    subKegiatanValid = false;
                    emptyFields.push(`RAB #${rabIndex + 1} pada Sub Kegiatan #${index + 1} harus diisi lengkap`);
                }
            });
        });
    }
    
    // Validate budget
    let totalBudget = 0;
    let programRektorTotal = parseInt($('#infoTotal').text().replace(/\D/g, ''));
    
    // Calculate total from main RABs
    $('.rab-item').each(function() {
        const jumlahHidden = $(this).find('.jumlah-hidden').val();
        if (jumlahHidden) {
            totalBudget += parseInt(jumlahHidden);
        }
    });
    
    // Calculate total from sub kegiatan RABs if sub kegiatan is enabled
    if ($('#has_sub_kegiatan_yes').is(':checked')) {
        $('.sub-kegiatan-item').each(function() {
            $(this).find('.rab-sub-item').each(function() {
                const jumlahHidden = $(this).find('.jumlah-hidden').val();
                if (jumlahHidden) {
                    totalBudget += parseInt(jumlahHidden);
                }
            });
        });
    }
    
    // Check if total budget exceeds program rektor total
    if (totalBudget > programRektorTotal) {
        emptyFields.push(`Total anggaran (Rp ${totalBudget.toLocaleString('id-ID')}) melebihi batas Program Rektor (Rp ${programRektorTotal.toLocaleString('id-ID')})`);
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
    validateDates();
});
</script>
<script>
     setTimeout(function() {
            $('.alert').alert('close');
        }, 10000000);
</script>