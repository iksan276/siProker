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
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

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

// Replace the existing dashboard route with our new controller
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
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
