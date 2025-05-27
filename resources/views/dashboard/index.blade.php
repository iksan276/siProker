@extends('layouts.main')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-3 text-gray-800">Dashboard</h1>
    <div class="d-flex">
        <button class="btn btn-sm btn-primary shadow-sm mb-3 mr-2" data-toggle="modal" data-target="#filterModal">
            <i class="fas fa-filter fa-sm text-white-50"></i> Filter Data
        </button>
        @if(auth()->user()->level == 1)
        <div class="dropdown">
            <button class="btn btn-sm btn-success shadow-sm mb-3 dropdown-toggle" type="button" id="importExportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Import/Export
            </button>
            <div class="dropdown-menu" aria-labelledby="importExportDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#importModal">
                    <i class="fas fa-upload fa-sm text-gray-400"></i> Import Data
                </a>
                <!-- <a class="dropdown-item" href="{{ route('dashboard.export') }}">
                    <i class="fas fa-download fa-sm text-gray-400"></i> Export Data
                </a> -->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('dashboard.template') }}">
                    <i class="fas fa-file-download fa-sm text-gray-400"></i> Download Template
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Filter Summary -->
@if($renstraId || $pilarId || $isuId || $programPengembanganId || $jenisKegiatanId || $year != date('Y'))
<div class="card mb-4 py-3 border-left-info">
    <div class="card-body">
        <h5 class="font-weight-bold text-info mb-2">Active Filters:</h5>
        <div class="d-flex flex-wrap">
            @if($year != date('Y'))
                <span class="badge badge-info mr-2 mb-2 p-2">Year: {{ $year }}</span>
            @endif
            
            @if($renstraId)
                <span class="badge badge-info mr-2 mb-2 p-2">Renstra: {{ $renstras->where('RenstraID', $renstraId)->first()->Nama }}</span>
            @endif
            
            @if($pilarId)
                <span class="badge badge-info mr-2 mb-2 p-2">Pilar: {{ $pilars->where('PilarID', $pilarId)->first()->Nama }}</span>
            @endif
            
            @if($isuId)
                <span class="badge badge-info mr-2 mb-2 p-2">Isu Strategis: {{ $isuStrategis->where('IsuID', $isuId)->first()->Nama }}</span>
            @endif
            
            @if($programPengembanganId)
                <span class="badge badge-info mr-2 mb-2 p-2">Program Pengembangan: {{ $programPengembangans->where('ProgramPengembanganID', $programPengembanganId)->first()->Nama }}</span>
            @endif
            
            @if($jenisKegiatanId)
                <span class="badge badge-info mr-2 mb-2 p-2">Jenis Kegiatan: {{ $jenisKegiatans->where('JenisKegiatanID', $jenisKegiatanId)->first()->Nama }}</span>
            @endif
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2">
            <i class="fas fa-times"></i> Clear Filters
        </a>
    </div>
</div>
@endif

<!-- Content Row -->
<div class="row">
    <!-- Indikator Kinerja Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Indikator Kinerja</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['indikatorKinerjaCount'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kegiatan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Kegiatan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['kegiatanCount'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Program Rektor Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Program Rektor
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $data['programRektorCount'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data['userCount'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Kegiatan Bulanan ({{ $year }})</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Year:</div>
                        @foreach($years as $y)
                            <a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->except('year'), ['year' => $y])) }}">{{ $y }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="kegiatanAreaChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Program Rektor per Jenis Kegiatan</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Program Pengembangan:</div>
                        @foreach($programPengembangans->take(10) as $pp)
                            <a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->except('program_pengembangan_id'), ['program_pengembangan_id' => $pp->ProgramPengembanganID])) }}">{{ $pp->Nama }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Card Body -->
                     <!-- Card Body -->
            <div class="card-body">
                <div class="chart-bar">
                    <canvas id="jenisKegiatanBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Isu Strategis per Pilar</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Renstra:</div>
                        @foreach($renstras as $r)
                            <a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->except('renstra_id'), ['renstra_id' => $r->RenstraID])) }}">{{ $r->Nama }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="pilarPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @foreach($pilars->take(10) as $index => $pilar)
                    <span class="mr-2">
                        <i class="fas fa-circle" style="color: {{ 'hsl(' . (($index * 30) % 360) . ', 70%, 50%)' }}"></i> {{ $pilar->Nama }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Summary</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">Isu Strategis <span
                        class="float-right">{{ $data['isuStrategisCount'] }}</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ min(100, $data['isuStrategisCount'] * 5) }}%"
                        aria-valuenow="{{ $data['isuStrategisCount'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Program Pengembangan <span
                        class="float-right">{{ $data['programPengembanganCount'] }}</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $data['programPengembanganCount'] * 2) }}%"
                        aria-valuenow="{{ $data['programPengembanganCount'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Pilar <span
                        class="float-right">{{ $data['pilarCount'] }}</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar" role="progressbar" style="width: {{ min(100, $data['pilarCount'] * 10) }}%"
                        aria-valuenow="{{ $data['pilarCount'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Satuan <span
                        class="float-right">{{ $data['satuanCount'] }}</span></h4>
                <div class="progress mb-4">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $data['satuanCount'] * 5) }}%"
                        aria-valuenow="{{ $data['satuanCount'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <h4 class="small font-weight-bold">Unit <span
                        class="float-right">{{ $data['unitCount'] }}</span></h4>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $data['unitCount'] * 5) }}%"
                        aria-valuenow="{{ $data['unitCount'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
@if(auth()->user()->level == 1)
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file">Select Excel File</label>
                        <input type="file" class="form-control-file" id="file" name="file" accept=".xlsx,.xls" required>
                        <small class="form-text text-muted">
                            File harus berformat Excel (.xlsx atau .xls) dengan multiple sheets sesuai urutan:
                            <br>1. Renstra
                            <br>2. Pilar  
                            <br>3. Isu Strategis
                            <br>4. Program Pengembangan
                            <br>5. Indikator Kinerja
                            <br>6. Program Rektor
                            <br>7. Kegiatan
                        </small>
                    </div>
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong>
                        <ul class="mb-0">
                            <li>Pastikan data diimport sesuai urutan sheet</li>
                            <li>Data akan diimport secara berurutan untuk menjaga referensi foreign key</li>
                            <li>Download template terlebih dahulu untuk melihat format yang benar</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Dashboard Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select class="form-control" id="year" name="year">
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="renstra_id">Renstra</label>
                                <select class="form-control" id="renstra_id" name="renstra_id">
                                    <option value="">All Renstras</option>
                                    @foreach($renstras as $renstra)
                                        <option value="{{ $renstra->RenstraID }}" {{ $renstraId == $renstra->RenstraID ? 'selected' : '' }}>
                                            {{ $renstra->Nama }} ({{ $renstra->PeriodeMulai }} - {{ $renstra->PeriodeSelesai }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="pilar_id">Pilar</label>
                                <select class="form-control" id="pilar_id" name="pilar_id">
                                    <option value="">All Pilars</option>
                                    @foreach($pilars as $pilar)
                                        <option value="{{ $pilar->PilarID }}" {{ $pilarId == $pilar->PilarID ? 'selected' : '' }}>
                                            {{ $pilar->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="isu_id">Isu Strategis</label>
                                <select class="form-control" id="isu_id" name="isu_id">
                                    <option value="">All Isu Strategis</option>
                                    @foreach($isuStrategis as $isu)
                                        <option value="{{ $isu->IsuID }}" {{ $isuId == $isu->IsuID ? 'selected' : '' }}>
                                            {{ $isu->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="program_pengembangan_id">Program Pengembangan</label>
                                <select class="form-control" id="program_pengembangan_id" name="program_pengembangan_id">
                                    <option value="">All Program Pengembangan</option>
                                    @foreach($programPengembangans as $program)
                                        <option value="{{ $program->ProgramPengembanganID }}" {{ $programPengembanganId == $program->ProgramPengembanganID ? 'selected' : '' }}>
                                            {{ $program->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="jenis_kegiatan_id">Jenis Kegiatan</label>
                                <select class="form-control" id="jenis_kegiatan_id" name="jenis_kegiatan_id">
                                    <option value="">All Jenis Kegiatan</option>
                                    @foreach($jenisKegiatans as $jenisKegiatan)
                                        <option value="{{ $jenisKegiatan->JenisKegiatanID }}" {{ $jenisKegiatanId == $jenisKegiatan->JenisKegiatanID ? 'selected' : '' }}>
                                            {{ $jenisKegiatan->Nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // Area Chart - Kegiatan Bulanan
    var ctx = document.getElementById("kegiatanAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Kegiatan",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! $monthlyKegiatanData !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                              yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value;
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel;
                    }
                }
            }
        }
    });

    // Pie Chart - Isu Strategis per Pilar
    var ctx2 = document.getElementById("pilarPieChart");
    var myPieChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: {!! $pilarLabels !!},
            datasets: [{
                data: {!! $pilarData !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
                    '#2e59d9', '#17a673', '#2c9faf', '#f6c23e', '#e74a3b', '#5a5c69'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#f6c23e', '#e74a3b', '#5a5c69',
                    '#2e59d9', '#17a673', '#2c9faf', '#f6c23e', '#e74a3b', '#5a5c69'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });

    // Bar Chart - Program Rektor per Jenis Kegiatan
    var ctx3 = document.getElementById("jenisKegiatanBarChart");
    var myBarChart = new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: {!! $jenisKegiatanLabels !!},
            datasets: [{
                label: "Program Rektor",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: {!! $jenisKegiatanData !!},
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6
                    },
                    maxBarThickness: 25,
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value;
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel;
                    }
                }
            },
        }
    });

    // Cascade dropdown filters
    $(document).ready(function() {
        // When Renstra changes, update Pilar options
        $('#renstra_id').change(function() {
            var renstraId = $(this).val();
            if (renstraId) {
                $.ajax({
                    url: "{{ route('api.pilars-by-renstra') }}",
                    type: "GET",
                    data: { renstra_id: renstraId },
                    success: function(data) {
                        $('#pilar_id').empty();
                        $('#pilar_id').append('<option value="">All Pilars</option>');
                        $.each(data, function(key, value) {
                            $('#pilar_id').append('<option value="' + value.PilarID + '">' + value.Nama + '</option>');
                        });
                        // Reset dependent dropdowns
                        $('#isu_id').empty().append('<option value="">All Isu Strategis</option>');
                        $('#program_pengembangan_id').empty().append('<option value="">All Program Pengembangan</option>');
                    }
                });
            } else {
                $('#pilar_id').empty();
                $('#pilar_id').append('<option value="">All Pilars</option>');
                $('#isu_id').empty().append('<option value="">All Isu Strategis</option>');
                $('#program_pengembangan_id').empty().append('<option value="">All Program Pengembangan</option>');
            }
        });

        // When Pilar changes, update Isu Strategis options
        $('#pilar_id').change(function() {
            var pilarId = $(this).val();
            if (pilarId) {
                $.ajax({
                    url: "{{ route('api.isus-by-pilar') }}",
                    type: "GET",
                    data: { pilar_id: pilarId },
                    success: function(data) {
                        $('#isu_id').empty();
                        $('#isu_id').append('<option value="">All Isu Strategis</option>');
                        $.each(data, function(key, value) {
                            $('#isu_id').append('<option value="' + value.IsuID + '">' + value.Nama + '</option>');
                        });
                        // Reset dependent dropdown
                        $('#program_pengembangan_id').empty().append('<option value="">All Program Pengembangan</option>');
                    }
                });
            } else {
                $('#isu_id').empty();
                $('#isu_id').append('<option value="">All Isu Strategis</option>');
                $('#program_pengembangan_id').empty().append('<option value="">All Program Pengembangan</option>');
            }
        });

        // When Isu Strategis changes, update Program Pengembangan options
        $('#isu_id').change(function() {
            var isuId = $(this).val();
            if (isuId) {
                $.ajax({
                    url: "{{ route('api.programs-by-isu') }}",
                    type: "GET",
                    data: { isu_id: isuId },
                    success: function(data) {
                        $('#program_pengembangan_id').empty();
                        $('#program_pengembangan_id').append('<option value="">All Program Pengembangan</option>');
                        $.each(data, function(key, value) {
                            $('#program_pengembangan_id').append('<option value="' + value.ProgramPengembanganID + '">' + value.Nama + '</option>');
                        });
                    }
                });
            } else {
                $('#program_pengembangan_id').empty();
                $('#program_pengembangan_id').append('<option value="">All Program Pengembangan</option>');
            }
        });

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 10000000);
    });
</script>
@endpush
