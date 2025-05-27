<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RenstraController;
use App\Http\Controllers\PilarController;
use App\Http\Controllers\IsuStrategisController;
use App\Http\Controllers\ProgramPengembanganController;
use App\Http\Controllers\ProgramRektorController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\IndikatorKinerjaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MataAnggaranController;
use App\Http\Controllers\UnitAnggaranController;
use App\Http\Controllers\JenisKegiatanController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\RABController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthGoogleController;
use App\Http\Controllers\IKUPTController;
use App\Http\Controllers\KriteriaAkreditasiController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route with filtering
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Dashboard Import/Export routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/dashboard/import', [DashboardController::class, 'import'])->name('dashboard.import');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::get('/dashboard/template', [DashboardController::class, 'downloadTemplate'])->name('dashboard.template');
});

// API routes for dashboard filters
Route::middleware('auth')->prefix('api')->group(function() {
    Route::get('/pilars-by-renstra', 'App\Http\Controllers\ApiController@getPilarsByRenstra')->name('api.pilars-by-renstra');
    Route::get('/isus-by-pilar', 'App\Http\Controllers\ApiController@getIsusByPilar')->name('api.isus-by-pilar');
    Route::get('/programs-by-isu', 'App\Http\Controllers\ApiController@getProgramsByIsu')->name('api.programs-by-isu');
    Route::get('/programs-by-rektor', 'App\Http\Controllers\ApiController@getProgramRektor')->name('api.programs-by-rektor');
    Route::get('/sub-kegiatans-by-kegiatan', 'App\Http\Controllers\ApiController@getSubKegiatansByKegiatan')->name('api.sub-kegiatans-by-kegiatan');
    Route::get('/program-rektor-details/{id}', 'App\Http\Controllers\ApiController@getProgramRektorDetails')->name('api.program-rektor-details');
    Route::get('/kegiatan-details/{id}', 'App\Http\Controllers\ApiController@getKegiatanDetails')->name('api.kegiatan-details');
    // API routes for status updates
    Route::post('/kegiatan/{id}/update-status', 'App\Http\Controllers\ApiController@updateKegiatanStatus')->name('api.kegiatan.update-status');
    Route::post('/subkegiatan/{id}/update-status', 'App\Http\Controllers\ApiController@updateSubKegiatanStatus')->name('api.subkegiatan.update-status');
    Route::post('/rab/{id}/update-status', 'App\Http\Controllers\ApiController@updateRabStatus')->name('api.rab.update-status');

    Route::get('/sub-kegiatan-details/{id}', 'App\Http\Controllers\ApiController@getSubKegiatanDetails')->name('api.sub-kegiatan-details');
});

Route::get('/auth/oauth_google', [OAuthGoogleController::class, 'authenticate']);

Route::middleware('auth')->group(function () {

      Route::get('/kegiatans/summary', [KegiatanController::class, 'getSummary'])->name('kegiatans.summary');
    
    // Add this to the middleware('auth') group
    Route::resource('requests', RequestController::class);

    Route::resource('sub-kegiatans', SubKegiatanController::class);
    
    // RAB routes
    Route::resource('rabs', RABController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Pilar routes - accessible by all users
    Route::resource('pilars', PilarController::class);
    
    Route::resource('kegiatans', KegiatanController::class);
    Route::resource('program-rektors', ProgramRektorController::class);
    
    // Routes accessible only by admin (level 1)
    Route::middleware(['admin'])->group(function () {
        // User routes
        Route::resource('users', UserController::class);
        Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
        Route::get('/users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');

        // Renstra routes
        Route::resource('renstras', RenstraController::class);
        
        // Isu Strategis routes
        Route::resource('isu-strategis', IsuStrategisController::class);
        
        // Program Pengembangan routes
        Route::resource('program-pengembangans', ProgramPengembanganController::class);
        
        // Program Rektor routes
        Route::get('/program-rektors/export/excel', [ProgramRektorController::class, 'exportExcel'])->name('program-rektors.export.excel');

        // Satuan routes
        Route::resource('satuans', SatuanController::class);

        // Unit routes
        Route::resource('units', UnitController::class);
        
        // Mata Anggaran routes
        Route::resource('meta-anggarans', MataAnggaranController::class);
        
        // IKUPT routes
        Route::resource('ikupts', IKUPTController::class);

        // Kriteria Akreditasi routes
        Route::resource('kriteria-akreditasis', KriteriaAkreditasiController::class);

        // Indikator Kinerja routes
        Route::resource('indikator-kinerjas', IndikatorKinerjaController::class);
        Route::get('/indikator-kinerjas/export/excel', [IndikatorKinerjaController::class, 'exportExcel'])->name('indikator-kinerjas.export.excel');
        Route::get('indikator-kinerjas/renstra-years/{id}', [IndikatorKinerjaController::class,'getRenstraYears'])->name('indikator-kinerjas.renstra-years');

        // Kegiatan routes
        Route::get('/kegiatans/export/excel', [KegiatanController::class, 'exportExcel'])->name('kegiatans.export.excel');

        // Jenis Kegiatan Routes
        Route::resource('jenis-kegiatans', JenisKegiatanController::class);
    });
});

require __DIR__.'/auth.php';
