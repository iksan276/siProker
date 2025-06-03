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
use App\Imports\MultiSheetImport;
use App\Exports\MultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $year = $request->input('year', date('Y'));
        $renstraId = $request->input('renstra_id');
        $pilarId = $request->input('pilar_id');
        $isuId = $request->input('isu_id');
        $programPengembanganId = $request->input('program_pengembangan_id');
        $jenisKegiatanId = $request->input('jenis_kegiatan_id');
        
        // Get all available years for dropdown
        $years = [];
        $currentYear = (int)date('Y');
        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
            $years[] = $i;
        }
        
        // Get all models for filter dropdowns
        $renstras = Renstra::active()->get();
        $pilars = Pilar::when($renstraId, function($query) use ($renstraId) {
            return $query->byRenstra($renstraId);
        })->get();
        
        $isuStrategis = IsuStrategis::when($renstraId, function($query) use ($renstraId) {
            return $query->byRenstra($renstraId);
        })->when($pilarId, function($query) use ($pilarId) {
            return $query->byPilar($pilarId);
        })->get();
        
        $programPengembangans = ProgramPengembangan::when($renstraId, function($query) use ($renstraId) {
            return $query->byRenstra($renstraId);
        })->when($pilarId, function($query) use ($pilarId) {
            return $query->byPilar($pilarId);
        })->when($isuId, function($query) use ($isuId) {
            return $query->byIsuStrategis($isuId);
        })->get();
        
        $jenisKegiatans = JenisKegiatan::active()->get();
        
        // Apply filters to query builders
        $indikatorKinerjaQuery = IndikatorKinerja::active()
            ->when($programPengembanganId, function($query) use ($programPengembanganId) {
                return $query->byProgramPengembangan($programPengembanganId);
            });
            
        $kegiatanQuery = Kegiatan::forYear($year)
            ->when($renstraId, function($query) use ($renstraId) {
                return $query->byRenstra($renstraId);
            })
            ->when($pilarId, function($query) use ($pilarId) {
                return $query->byPilar($pilarId);
            })
            ->when($isuId, function($query) use ($isuId) {
                return $query->byIsuStrategis($isuId);
            })
            ->when($programPengembanganId, function($query) use ($programPengembanganId) {
                return $query->byProgramPengembangan($programPengembanganId);
            })
            ->when($jenisKegiatanId, function($query) use ($jenisKegiatanId) {
                return $query->byJenisKegiatan($jenisKegiatanId);
            });
            
        $programRektorQuery = ProgramRektor::active()
            ->when($renstraId, function($query) use ($renstraId) {
                return $query->byRenstra($renstraId);
            })
            ->when($pilarId, function($query) use ($pilarId) {
                return $query->byPilar($pilarId);
            })
            ->when($isuId, function($query) use ($isuId) {
                return $query->byIsuStrategis($isuId);
            })
            ->when($programPengembanganId, function($query) use ($programPengembanganId) {
                return $query->byProgramPengembangan($programPengembanganId);
            })
            ->when($jenisKegiatanId, function($query) use ($jenisKegiatanId) {
                return $query->byJenisKegiatan($jenisKegiatanId);
            });
            
        $isuStrategisQuery = IsuStrategis::active()
            ->when($renstraId, function($query) use ($renstraId) {
                return $query->byRenstra($renstraId);
            })
            ->when($pilarId, function($query) use ($pilarId) {
                return $query->byPilar($pilarId);
            });
            
        $programPengembanganQuery = ProgramPengembangan::active()
            ->when($renstraId, function($query) use ($renstraId) {
                return $query->byRenstra($renstraId);
            })
            ->when($pilarId, function($query) use ($pilarId) {
                return $query->byPilar($pilarId);
            })
            ->when($isuId, function($query) use ($isuId) {
                return $query->byIsuStrategis($isuId);
            });
            
        $pilarQuery = Pilar::active()
            ->when($renstraId, function($query) use ($renstraId) {
                return $query->byRenstra($renstraId);
            });
        
        // Count data from each model with filters applied
        $data = [
            'indikatorKinerjaCount' => $indikatorKinerjaQuery->count(),
            'isuStrategisCount' => $isuStrategisQuery->count(),
            'jenisKegiatanCount' => JenisKegiatan::active()->count(),
            'kegiatanCount' => $kegiatanQuery->count(),
            'mataAnggaranCount' => MataAnggaran::where('NA', 'N')->count(),
            'pilarCount' => $pilarQuery->count(),
            'programPengembanganCount' => $programPengembanganQuery->count(),
            'programRektorCount' => $programRektorQuery->count(),
            'renstraCount' => Renstra::active()->count(),
            'satuanCount' => Satuan::where('NA', 'N')->count(),
            'unitCount' => Unit::where('NA', 'N')->count(),
            'userCount' => User::count(),
        ];

        // Get active Renstra
        $activeRenstra = Renstra::active()->first();
        
        // Get Pilar data for pie chart with filters applied
        $pilarsForChart = $pilarQuery->withCount('isuStrategis')->get();
        $pilarLabels = $pilarsForChart->pluck('Nama')->toJson();
        $pilarData = $pilarsForChart->pluck('isu_strategis_count')->toJson();
        
        // Get monthly Kegiatan data for area chart with filters applied
        $monthlyKegiatan = [];
        for ($i = 1; $i <= 12; $i++) {
            $count = clone $kegiatanQuery;
            $monthlyKegiatan[] = $count->whereMonth('TanggalMulai', $i)
                ->whereYear('TanggalMulai', $year)
                ->count();
        }
        $monthlyKegiatanData = json_encode($monthlyKegiatan);
        
        // Get Program Rektor data by Jenis Kegiatan for bar chart with filters applied
        $jenisKegiatansForChart = JenisKegiatan::active()->get();
        $jenisKegiatanLabels = $jenisKegiatansForChart->pluck('Nama')->toJson();
        
        $jenisKegiatanData = [];
        foreach ($jenisKegiatansForChart as $jenisKegiatan) {
            $count = clone $programRektorQuery;
            $jenisKegiatanData[] = $count->where('JenisKegiatanID', $jenisKegiatan->JenisKegiatanID)->count();
        }
        $jenisKegiatanData = json_encode($jenisKegiatanData);
        
        return view('dashboard.index', compact(
            'data', 
            'activeRenstra', 
            'pilars',
            'pilarLabels', 
            'pilarData', 
            'monthlyKegiatanData',
            'jenisKegiatanLabels',
            'jenisKegiatanData',
            'year',
            'years',
            'renstraId',
            'pilarId',
            'isuId',
            'programPengembanganId',
            'jenisKegiatanId',
            'renstras',
            'isuStrategis',
            'programPengembangans',
            'jenisKegiatans'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new MultiSheetImport, $request->file('file'));
            
            return redirect()->route('dashboard')->with('success', 'Data berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            return Excel::download(new MultiSheetExport, 'dashboard_data_' . date('Y-m-d_H-i-s') . '.xlsx');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
{
    try {
        $fileName = 'template_dashboard_data.xlsx';
        $filePath = public_path('templates/' . $fileName);
        
        // Check if file exists
        if (!file_exists($filePath)) {
            return redirect()->route('dashboard')->with('error', 'Template file not found.');
        }
        
        // Return file download response
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
        
    } catch (\Exception $e) {
        return redirect()->route('dashboard')->with('error', 'Error downloading template: ' . $e->getMessage());
    }
}

}
