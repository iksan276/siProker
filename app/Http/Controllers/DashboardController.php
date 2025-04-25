<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndikatorKinerja;
use App\Models\IsuStrategis;
use App\Models\JenisKegiatan;
use App\Models\Kegiatan;
use App\Models\MataAnggaran;
use App\Models\Pilar;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;
use App\Models\Renstra;
use App\Models\Satuan;
use App\Models\Unit;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Count data from each model
        $data = [
            'indikatorKinerjaCount' => IndikatorKinerja::count(),
            'isuStrategisCount' => IsuStrategis::count(),
            'jenisKegiatanCount' => JenisKegiatan::count(),
            'kegiatanCount' => Kegiatan::count(),
            'mataAnggaranCount' => MataAnggaran::count(),
            'pilarCount' => Pilar::count(),
            'programPengembanganCount' => ProgramPengembangan::count(),
            'programRektorCount' => ProgramRektor::count(),
            'renstraCount' => Renstra::count(),
            'satuanCount' => Satuan::count(),
            'unitCount' => Unit::count(),
            'userCount' => User::count(),
        ];

        // Get active Renstra
        $activeRenstra = Renstra::active()->first();
        
        // Get Pilar data for pie chart
        $pilars = Pilar::withCount('isuStrategis')->get();
        $pilarLabels = $pilars->pluck('Nama')->toJson();
        $pilarData = $pilars->pluck('isu_strategis_count')->toJson();
        
        // Get monthly Kegiatan data for area chart
        $monthlyKegiatan = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyKegiatan[] = Kegiatan::whereMonth('TanggalMulai', $i)
                ->whereYear('TanggalMulai', date('Y'))
                ->count();
        }
        $monthlyKegiatanData = json_encode($monthlyKegiatan);
        
        // Get Program Rektor data by Jenis Kegiatan for bar chart
        $jenisKegiatans = JenisKegiatan::withCount('programRektors')->get();
        $jenisKegiatanLabels = $jenisKegiatans->pluck('Nama')->toJson();
        $jenisKegiatanData = $jenisKegiatans->pluck('program_rektors_count')->toJson();
        
        return view('dashboard.index', compact(
            'data', 
            'activeRenstra', 
            'pilars',  // Pass the pilars collection to the view
            'pilarLabels', 
            'pilarData', 
            'monthlyKegiatanData',
            'jenisKegiatanLabels',
            'jenisKegiatanData'
        ));
    }
}
