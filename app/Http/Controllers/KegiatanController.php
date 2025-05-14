<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use App\Models\SubKegiatan;
use App\Models\RAB;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KegiatansExport;
use Illuminate\Database\QueryException;

class KegiatanController extends Controller
{

    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Get all active pilars for the filter
        $pilars = Pilar::where('NA', 'N')->get();
        
        // Get all active isu strategis for the filter
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        
        // Get all program pengembangans for the filter
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        
        // Get all active program rektors for the filter
        $programRektors = ProgramRektor::where('NA', 'N')->get();
        
        // Base query
        $kegiatansQuery = Kegiatan::with([
            'programRektor', 
            'programRektor.programPengembangan.isuStrategis.pilar.renstra', 
            'createdBy', 
            'editedBy',
            'subKegiatans',
            'rabs'
        ]);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            // Filter pilars by renstraID
            $pilarIds = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->pluck('PilarID');
                
            // Filter isu strategis by pilar IDs
            $isuIds = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            // Filter program rektors by program pengembangan IDs
            $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->pluck('ProgramRektorID');
                
            $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
            
            // Update pilars list based on selected renstra
            $pilars = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->get();
                
            // Update isu strategis list based on filtered pilars
            $isuStrategis = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->get();
                
            // Update program pengembangans list based on filtered isus
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
                
            // Update program rektors list based on filtered program pengembangans
            $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if pilarID is provided
        if ($request->has('pilarID') && $request->pilarID) {
            // Filter isu strategis by pilarID
            $isuIds = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            // Filter program rektors by program pengembangan IDs
            $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->pluck('ProgramRektorID');
                
            $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
            
            // Update isu strategis list based on selected pilar
            $isuStrategis = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->get();
                
            // Update program pengembangans list based on filtered isus
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
                
            // Update program rektors list based on filtered program pengembangans
            $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if isuID is provided
        if ($request->has('isuID') && $request->isuID) {
            // Filter program pengembangans by isuID
            $programIds = ProgramPengembangan::where('IsuID', $request->isuID)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            // Filter program rektors by program pengembangan IDs
            $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->pluck('ProgramRektorID');
                
            $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
            
            // Update program pengembangans list based on selected isu
            $programPengembangans = ProgramPengembangan::where('IsuID', $request->isuID)
                ->where('NA', 'N')
                ->get();
                
            // Update program rektors list based on filtered program pengembangans
            $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            // Filter program rektors by program pengembangan ID
            $programRektorIds = ProgramRektor::where('ProgramPengembanganID', $request->programPengembanganID)
                ->where('NA', 'N')
                ->pluck('ProgramRektorID');
                
            $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
            
            // Update program rektors list based on selected program pengembangan
            $programRektors = ProgramRektor::where('ProgramPengembanganID', $request->programPengembanganID)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if programRektorID is provided
        if ($request->has('programRektorID') && $request->programRektorID) {
            $kegiatansQuery->where('ProgramRektorID', $request->programRektorID);
        }
        
        // Get the filtered results
        $kegiatans = $kegiatansQuery->orderBy('KegiatanID', 'asc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedRenstra = $request->renstraID;
        $selectedPilar = $request->pilarID;
        $selectedIsu = $request->isuID;
        $selectedProgramPengembangan = $request->programPengembanganID;
        $selectedProgramRektor = $request->programRektorID;
        
        // If user is not admin, prepare tree grid data
        if (!auth()->user()->isAdmin()) {
            $userId = Auth::id();
            
            if ($request->ajax() && $request->wantsJson()) {
                $treeData = $this->buildTreeData($kegiatans);
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            return view('kegiatans.user_index', compact(
                'kegiatans', 
                'renstras', 
                'pilars', 
                'isuStrategis', 
                'programPengembangans', 
                'programRektors', 
                'selectedRenstra', 
                'selectedPilar', 
                'selectedIsu', 
                'selectedProgramPengembangan', 
                'selectedProgramRektor'
            ));
        }
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            // If format=tree is requested, return tree data
            if ($request->has('format') && $request->format === 'tree') {
                $treeData = $this->buildTreeData($kegiatans);
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            $data = [];
            foreach ($kegiatans as $index => $kegiatan) {
                // Match the exact styling from the Mata Anggaran page, but without td tags
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('kegiatans.show', $kegiatan->KegiatanID).'" data-title="Detail Kegiatan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('kegiatans.edit', $kegiatan->KegiatanID).'" data-title="Edit Kegiatan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-kegiatan" data-id="'.$kegiatan->KegiatanID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $data[] = [
                    'no' => $index + 1,
                    'program_rektor' => nl2br($kegiatan->programRektor->Nama),
                    'nama' => nl2br($kegiatan->Nama),
                    'tanggal_mulai' => \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
                    'tanggal_selesai' => \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
                    'rincian_kegiatan' => nl2br($kegiatan->RincianKegiatan),
                    'feedback' => nl2br($kegiatan->Feedback),
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('kegiatans.index', compact(
            'kegiatans', 
            'renstras', 
            'pilars', 
            'isuStrategis', 
            'programPengembangans', 
            'programRektors', 
            'selectedRenstra', 
            'selectedPilar', 
            'selectedIsu', 
            'selectedProgramPengembangan', 
            'selectedProgramRektor'
        ));
    }
    
   
    private function buildTreeData($kegiatans)
    {
        $treeData = [];
        $rowIndex = 1;
        
        foreach ($kegiatans as $kegiatan) {
            // Create kegiatan node
            $kegiatanNode = [
                'id' => 'kegiatan_' . $kegiatan->KegiatanID,
                'no' => $rowIndex++,
                'nama' => '<span data-toggle="tooltip" title="Ini adalah Kegiatan">' . $kegiatan->Nama . '</span>',
                'program_rektor_id' => $kegiatan->ProgramRektorID,
                'type' => 'kegiatan',
                'parent' => null,
                'level' => 0,
                'has_children' => $kegiatan->subKegiatans->count() > 0 || $kegiatan->rabs->whereNull('SubKegiatanID')->count() > 0,
                'actions' => '
                    <button class="btn btn-info btn-square btn-sm load-modal" 
                        data-url="' . route('kegiatans.show', $kegiatan->KegiatanID) . '" 
                        data-title="Detail Kegiatan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" 
                        data-url="' . route('kegiatans.edit', $kegiatan->KegiatanID) . '" 
                        data-title="Edit Kegiatan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-kegiatan" 
                        data-id="' . $kegiatan->KegiatanID . '">
                        <i class="fas fa-trash"></i>
                    </button>',
                'row_class' => '',
                'tooltip' => 'Tanggal: ' . \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y') . ' s/d ' . 
                            \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y')
            ];
            
            $treeData[] = $kegiatanNode;
            
            // Add Sub Kegiatans
            if ($kegiatan->subKegiatans->count() > 0) {
                foreach ($kegiatan->subKegiatans as $subIndex => $subKegiatan) {
                    $statusBadge = '';
                    if ($subKegiatan->Status == 'N') {
                        $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                    } elseif ($subKegiatan->Status == 'Y') {
                        $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                    } elseif ($subKegiatan->Status == 'T') {
                        $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                    } elseif ($subKegiatan->Status == 'R') {
                        $statusBadge = '<span class="badge badge-info">Revisi</span>';
                    }
                    
                    $subKegiatanNode = [
                        'id' => 'subkegiatan_' . $subKegiatan->SubKegiatanID,
                        'no' => '',
                        'nama' =>'<span data-toggle="tooltip" title="Ini adalah Sub Kegiatan">' . $subKegiatan->Nama . '</span> <span data-toggle="tooltip" title='. $subKegiatan->Feedback .'>' . $statusBadge . '</span>',
                        'type' => 'subkegiatan',
                        'parent' => 'kegiatan_' . $kegiatan->KegiatanID,
                        'level' => 1,
                        'has_children' => $subKegiatan->rabs->count() > 0,
                        'actions' => '
                            <button class="btn btn-info btn-square btn-sm load-modal" 
                                data-url="' . route('sub-kegiatans.show', $subKegiatan->SubKegiatanID) . '" 
                                data-title="Detail Sub Kegiatan">
                                <i class="fas fa-eye"></i>
                            </button>',
                        'row_class' => '',
                        'tooltip' => 'Jadwal: ' . \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y') . ' s/d ' . 
                                    \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y')
                    ];
                    
                    $treeData[] = $subKegiatanNode;
                    
                    // Add RABs for this Sub Kegiatan
                    if ($subKegiatan->rabs->count() > 0) {
                        foreach ($subKegiatan->rabs as $rabIndex => $rab) {
                            $statusBadge = '';
                            if ($rab->Status == 'N') {
                                $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                            } elseif ($rab->Status == 'Y') {
                                $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                            } elseif ($rab->Status == 'T') {
                                $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                            } elseif ($rab->Status == 'R') {
                                $statusBadge = '<span class="badge badge-info">Revisi</span>';
                            }
                            
                            $total = $rab->Volume * $rab->HargaSatuan;
                            
                            $rabNode = [
                                'id' => 'rab_sub_' . $rab->RABID,
                                'no' => '',
                                'nama' =>'<span data-toggle="tooltip" title="Ini adalah RAB dari Sub Kegiatan">' . $rab->Komponen . '</span> <span data-toggle="tooltip" title='. $rab->Feedback .'>' . $statusBadge . '</span>',
                                'type' => 'rab',
                                'parent' => 'subkegiatan_' . $subKegiatan->SubKegiatanID,
                                'level' => 2,
                                'has_children' => false,
                                'actions' => '
                                    <button class="btn btn-info btn-square btn-sm load-modal" 
                                        data-url="' . route('rabs.show', $rab->RABID) . '" 
                                        data-title="Detail RAB">
                                        <i class="fas fa-eye"></i>
                                    </button>',
                                'row_class' => '',
                                'tooltip' => 'Volume: ' . number_format($rab->Volume, 0, ',', '.') . ' ' . 
                                            ($rab->satuan ? $rab->satuan->Nama : '-') . ' x Rp ' . 
                                            number_format($rab->HargaSatuan, 0, ',', '.') . ' = Rp ' . 
                                            number_format($total, 0, ',', '.')
                            ];
                            
                            $treeData[] = $rabNode;
                        }
                    }
                }
            }
            
            // Add direct RABs for this Kegiatan (if no sub kegiatans)
            $directRabs = $kegiatan->rabs()->whereNull('SubKegiatanID')->get();
            
            if ($directRabs->count() > 0) {
                foreach ($directRabs as $rabIndex => $rab) {
                    $statusBadge = '';
                    if ($rab->Status == 'N') {
                        $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                    } elseif ($rab->Status == 'Y') {
                        $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                    } elseif ($rab->Status == 'T') {
                        $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                    } elseif ($rab->Status == 'R') {
                        $statusBadge = '<span class="badge badge-info">Revisi</span>';
                    }
                    
                    $total = $rab->Volume * $rab->HargaSatuan;
                    
                    $rabNode = [
                        'id' => 'rab_' . $rab->RABID,
                        'no' => '',
                       'nama' =>'<span data-toggle="tooltip" title="Ini adalah RAB dari Kegiatan">' . $rab->Komponen . '</span> <span data-toggle="tooltip" title='. $rab->Feedback .'>' . $statusBadge . '</span>',
                        'type' => 'rab',
                        'parent' => 'kegiatan_' . $kegiatan->KegiatanID,
                        'level' => 1,
                        'has_children' => false,
                        'actions' => '
                            <button class="btn btn-info btn-square btn-sm load-modal" 
                                data-url="' . route('rabs.show', $rab->RABID) . '" 
                                data-title="Detail RAB">
                                <i class="fas fa-eye"></i>
                            </button>',
                        'row_class' => '',
                        'tooltip' => 'Volume: ' . number_format($rab->Volume, 0, ',', '.') . ' ' . 
                                    ($rab->satuan ? $rab->satuan->Nama : '-') . ' x Rp ' . 
                                    number_format($rab->HargaSatuan, 0, ',', '.') . ' = Rp ' . 
                                    number_format($total, 0, ',', '.')
                    ];
                    
                    $treeData[] = $rabNode;
                }
            }
        }
        
        return $treeData;
    }
    


    
    public function exportExcel(Request $request)
{
    // Base query with program rektor relationship
    $kegiatansQuery = Kegiatan::with([
        'programRektor', 
        'programRektor.programPengembangan', 
        'programRektor.programPengembangan.isuStrategis', 
        'programRektor.programPengembangan.isuStrategis.pilar',
        'programRektor.programPengembangan.isuStrategis.pilar.renstra',
        'subKegiatans.rabs.satuanRelation',
        'rabs.satuanRelation'
    ]);
    
    // Apply filter if renstraID is provided
    if ($request->has('renstraID') && $request->renstraID) {
        // Filter pilars by renstraID
        $pilarIds = Pilar::where('RenstraID', $request->renstraID)
            ->where('NA', 'N')
            ->pluck('PilarID');
            
        // Filter isu strategis by pilar IDs
        $isuIds = IsuStrategis::whereIn('PilarID', $pilarIds)
            ->where('NA', 'N')
            ->pluck('IsuID');
            
        // Filter program pengembangans by isu IDs
        $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
            ->where('NA', 'N')
            ->pluck('ProgramPengembanganID');
            
        // Filter program rektors by program pengembangan IDs
        $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
            ->where('NA', 'N')
            ->pluck('ProgramRektorID');
            
        $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
    }
    
    // Apply filter if pilarID is provided
    if ($request->has('pilarID') && $request->pilarID) {
        // Filter isu strategis by pilarID
        $isuIds = IsuStrategis::where('PilarID', $request->pilarID)
            ->where('NA', 'N')
            ->pluck('IsuID');
            
        // Filter program pengembangans by isu IDs
        $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
            ->where('NA', 'N')
            ->pluck('ProgramPengembanganID');
            
        // Filter program rektors by program pengembangan IDs
        $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
            ->where('NA', 'N')
            ->pluck('ProgramRektorID');
            
        $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
    }
    
    // Apply filter if isuID is provided
    if ($request->has('isuID') && $request->isuID) {
        // Filter program pengembangans by isuID
        $programIds = ProgramPengembangan::where('IsuID', $request->isuID)
            ->where('NA', 'N')
            ->pluck('ProgramPengembanganID');
            
        // Filter program rektors by program pengembangan IDs
        $programRektorIds = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
            ->where('NA', 'N')
            ->pluck('ProgramRektorID');
            
        $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
    }
    
    // Apply filter if programPengembanganID is provided
    if ($request->has('programPengembanganID') && $request->programPengembanganID) {
        // Filter program rektors by program pengembangan ID
        $programRektorIds = ProgramRektor::where('ProgramPengembanganID', $request->programPengembanganID)
            ->where('NA', 'N')
            ->pluck('ProgramRektorID');
            
        $kegiatansQuery->whereIn('ProgramRektorID', $programRektorIds);
    }
    
    // Apply filter if programRektorID is provided
    if ($request->has('programRektorID') && $request->programRektorID) {
        $kegiatansQuery->where('ProgramRektorID', $request->programRektorID);
    }
    
    // Get the filtered results
    $kegiatans = $kegiatansQuery->orderBy('KegiatanID', 'desc')->get();
    
    // Generate a more descriptive filename based on filters
    $filename = 'kegiatans';
  
    $filename .= '_' . date('Y-m-d') . '.xlsx';
    
    return Excel::download(new KegiatansExport($kegiatans), $filename);
}


    public function create(Request $request)
    {
        // Get all active renstras, pilars, and isu strategis
        $renstras = Renstra::where('NA', 'N')->get();
        $pilars = Pilar::where('NA', 'N')->get();
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $programRektors = ProgramRektor::where('NA', 'N')->get();
        $users = User::all();
        $satuans = Satuan::where('NA', 'N')->get();
        
        // Get the
                // Get the selected filters from the request
                $selectedRenstra = request('renstraID');
                $selectedPilar = request('pilarID');
                $selectedIsu = request('isuID');
                $selectedProgramPengembangan = request('programPengembanganID');
                $selectedProgramRektor = request('programRektorID');
                
                // If renstra is selected, filter pilars
                if ($selectedRenstra) {
                    $pilars = Pilar::where('RenstraID', $selectedRenstra)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter isu strategis by pilars from selected renstra
                    $pilarIds = $pilars->pluck('PilarID')->toArray();
                    $isuStrategis = IsuStrategis::whereIn('PilarID', $pilarIds)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter program pengembangans by isu strategis from selected pilars
                    $isuIds = $isuStrategis->pluck('IsuID')->toArray();
                    $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter program rektors by program pengembangans from selected isus
                    $programIds = $programPengembangans->pluck('ProgramPengembanganID')->toArray();
                    $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // If pilar is selected, filter isu strategis
                if ($selectedPilar) {
                    $isuStrategis = IsuStrategis::where('PilarID', $selectedPilar)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter program pengembangans by isu strategis from selected pilar
                    $isuIds = $isuStrategis->pluck('IsuID')->toArray();
                    $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter program rektors by program pengembangans from selected isus
                    $programIds = $programPengembangans->pluck('ProgramPengembanganID')->toArray();
                    $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // If isu strategis is selected, filter program pengembangans
                if ($selectedIsu) {
                    $programPengembangans = ProgramPengembangan::where('IsuID', $selectedIsu)
                        ->where('NA', 'N')
                        ->get();
                        
                    // Filter program rektors by program pengembangans from selected isu
                    $programIds = $programPengembangans->pluck('ProgramPengembanganID')->toArray();
                    $programRektors = ProgramRektor::whereIn('ProgramPengembanganID', $programIds)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // If program pengembangan is selected, filter program rektors
                if ($selectedProgramPengembangan) {
                    $programRektors = ProgramRektor::where('ProgramPengembanganID', $selectedProgramPengembangan)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // Get the pre-selected program rektor if provided
                $selectedProgramRektorObj = null;
                if ($selectedProgramRektor) {
                    $selectedProgramRektorObj = ProgramRektor::find($selectedProgramRektor);
                }
                
                if (request()->ajax()) {
                    return view('kegiatans.create', compact(
                        'renstras',
                        'pilars',
                        'isuStrategis',
                        'programPengembangans',
                        'programRektors',
                        'users',
                        'satuans',
                        'selectedRenstra',
                        'selectedPilar',
                        'selectedIsu',
                        'selectedProgramPengembangan',
                        'selectedProgramRektor',
                        'selectedProgramRektorObj'
                    ))->render();
                }
                
                return view('kegiatans.create', compact(
                    'renstras',
                    'pilars',
                    'isuStrategis',
                    'programPengembangans',
                    'programRektors',
                    'users',
                    'satuans',
                    'selectedRenstra',
                    'selectedPilar',
                    'selectedIsu',
                    'selectedProgramPengembangan',
                    'selectedProgramRektor',
                    'selectedProgramRektorObj'
                ));
            }
        
            public function store(Request $request)
            {
                $request->validate([
                    'ProgramRektorID' => 'required|exists:program_rektors,ProgramRektorID',
                    'Nama' => 'required|string',
                    'TanggalMulai' => 'required|date',
                    'TanggalSelesai' => 'required|date|after_or_equal:TanggalMulai',
                    'RincianKegiatan' => 'required|string',
                    'has_sub_kegiatan' => 'required|in:yes,no',
                ]);
        
                // Begin transaction
                \DB::beginTransaction();
                
                try {
                    // Create Kegiatan
                    $kegiatan = new Kegiatan();
                    $kegiatan->ProgramRektorID = $request->ProgramRektorID;
                    $kegiatan->Nama = $request->Nama;
                    $kegiatan->TanggalMulai = $request->TanggalMulai;
                    $kegiatan->TanggalSelesai = $request->TanggalSelesai;
                    $kegiatan->RincianKegiatan = $request->RincianKegiatan;
                    $kegiatan->Feedback = $request->Feedback  ?? null;
                    $kegiatan->DCreated = now();
                    $kegiatan->UCreated = Auth::id();
                    $kegiatan->save();
                    
                    // Process Sub Kegiatans if has_sub_kegiatan is yes
                    if ($request->has_sub_kegiatan === 'yes' && $request->has('sub_kegiatans')) {
                        foreach ($request->sub_kegiatans as $subKegiatanData) {
                            if (empty($subKegiatanData['Nama'])) continue;
                            
                            $subKegiatan = new SubKegiatan();
                            $subKegiatan->KegiatanID = $kegiatan->KegiatanID;
                            $subKegiatan->Nama = $subKegiatanData['Nama'];
                            $subKegiatan->JadwalMulai = $subKegiatanData['JadwalMulai'];
                            $subKegiatan->JadwalSelesai = $subKegiatanData['JadwalSelesai'];
                            $subKegiatan->Catatan = $subKegiatanData['Catatan'];
                            $subKegiatan->Feedback = $subKegiatanData['Feedback']  ?? null;
                            $subKegiatan->Status = 'N'; // Default status is Menunggu
                            $subKegiatan->DCreated = now();
                            $subKegiatan->UCreated = Auth::id();
                            $subKegiatan->save();
                            
                            // Process RABs for this SubKegiatan if any
                            if (isset($subKegiatanData['rabs']) && is_array($subKegiatanData['rabs'])) {
                                foreach ($subKegiatanData['rabs'] as $rabData) {
                                    if (empty($rabData['Komponen'])) continue;
                                    
                                    $rab = new RAB();
                                    $rab->SubKegiatanID = $subKegiatan->SubKegiatanID;
                                    $rab->KegiatanID = $kegiatan->KegiatanID;
                                    $rab->Komponen = $rabData['Komponen'];
                                    $rab->Volume = str_replace('.', '', $rabData['Volume']);
                                    $rab->Satuan = $rabData['Satuan'];
                                    $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                                    $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                                    $rab->Feedback = $rabData['Feedback']  ?? null;
                                    $rab->Status = 'N'; // Default status is Menunggu
                                    $rab->DCreated = now();
                                    $rab->UCreated = Auth::id();
                                    $rab->save();
                                }
                            }
                        }
                    }
                    
                    // Process RABs for Kegiatan if any
                    if ($request->has('rabs') && is_array($request->rabs)) {
                        foreach ($request->rabs as $rabData) {
                            if (empty($rabData['Komponen'])) continue;
                            
                            $rab = new RAB();
                            $rab->KegiatanID = $kegiatan->KegiatanID;
                            $rab->SubKegiatanID = null; // This RAB belongs directly to Kegiatan
                            $rab->Komponen = $rabData['Komponen'];
                            $rab->Volume = str_replace('.', '', $rabData['Volume']);
                            $rab->Satuan = $rabData['Satuan'];
                            $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                            $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                            $rab->Feedback = $rabData['Feedback']  ?? null;
                            $rab->Status = 'N'; // Default status is Menunggu
                            $rab->DCreated = now();
                            $rab->UCreated = Auth::id();
                            $rab->save();
                        }
                    }
                    
                    // Commit transaction
                    \DB::commit();
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => true, 'message' => 'Kegiatan berhasil ditambahkan']);
                    }
                    return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil ditambahkan');
                } catch (\Exception $e) {
                    // Rollback transaction on error
                    \DB::rollback();
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
                    }
                    return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
                }
            }
        
            public function show($id)
            {
                $kegiatan = Kegiatan::with([
                    'programRektor', 
                    'programRektor.programPengembangan', 
                    'programRektor.programPengembangan.isuStrategis', 
                    'programRektor.programPengembangan.isuStrategis.pilar',
                    'programRektor.programPengembangan.isuStrategis.pilar.renstra',
                    'createdBy', 
                    'editedBy',
                    'subKegiatans.rabs.satuanRelation',
                    'rabs.satuanRelation'
                ])->findOrFail($id);
                
                if (request()->ajax()) {
                    return view('kegiatans.show', compact('kegiatan'))->render();
                }
                return view('kegiatans.show', compact('kegiatan'));
            }
        
            public function edit($id)
            {
                $kegiatan = Kegiatan::with([
                    'subKegiatans.rabs.satuanRelation',
                    'rabs.satuanRelation'
                ])->findOrFail($id);
                
                // Get all active renstras, pilars, and isu strategis
                $renstras = Renstra::where('NA', 'N')->get();
                $pilars = Pilar::where('NA', 'N')->get();
                $isuStrategis = IsuStrategis::where('NA', 'N')->get();
                $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
                $programRektors = ProgramRektor::where('NA', 'N')->get();
                $users = User::all();
                $satuans = Satuan::where('NA', 'N')->get();
                
                // Load the kegiatan's relationships to get the hierarchy
                $kegiatan->load('programRektor.programPengembangan.isuStrategis.pilar.renstra');
                
                // Get the selected values from the loaded relationships
                $selectedRenstra = $kegiatan->programRektor->programPengembangan->isuStrategis->pilar->renstra->RenstraID;
                $selectedPilar = $kegiatan->programRektor->programPengembangan->isuStrategis->pilar->PilarID;
                $selectedIsu = $kegiatan->programRektor->programPengembangan->isuStrategis->IsuID;
                $selectedProgramPengembangan = $kegiatan->programRektor->ProgramPengembanganID;
                $selectedProgramRektor = $kegiatan->ProgramRektorID;
                
                // Filter pilars by selected renstra
                if ($selectedRenstra) {
                    $pilars = Pilar::where('RenstraID', $selectedRenstra)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // Filter isu strategis by selected pilar
                if ($selectedPilar) {
                    $isuStrategis = IsuStrategis::where('PilarID', $selectedPilar)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // Filter program pengembangans by selected isu
                if ($selectedIsu) {
                    $programPengembangans = ProgramPengembangan::where('IsuID', $selectedIsu)
                        ->where('NA', 'N')
                        ->get();
                }
                
                // Filter program rektors by selected program pengembangan
                if ($selectedProgramPengembangan) {
                    $programRektors = ProgramRektor::where('ProgramPengembanganID', $selectedProgramPengembangan)
                        ->where('NA', 'N')
                        ->get();
                }
                
                if (request()->ajax()) {
                    return view('kegiatans.edit', compact(
                        'kegiatan',
                        'renstras',
                        'pilars',
                        'isuStrategis',
                        'programPengembangans',
                        'programRektors',
                        'users',
                        'satuans',
                        'selectedRenstra',
                        'selectedPilar',
                        'selectedIsu',
                        'selectedProgramPengembangan',
                        'selectedProgramRektor'
                    ))->render();
                }
                
                return view('kegiatans.edit', compact(
                    'kegiatan',
                    'renstras',
                    'pilars',
                    'isuStrategis',
                    'programPengembangans',
                    'programRektors',
                    'users',
                    'satuans',
                    'selectedRenstra',
                    'selectedPilar',
                    'selectedIsu',
                    'selectedProgramPengembangan',
                    'selectedProgramRektor'
                ));
            }
        
            public function update(Request $request, $id)
            {
                $kegiatan = Kegiatan::findOrFail($id);
                
                $request->validate([
                    'ProgramRektorID' => 'required|exists:program_rektors,ProgramRektorID',
                    'Nama' => 'required|string',
                    'TanggalMulai' => 'required|date',
                    'TanggalSelesai' => 'required|date|after_or_equal:TanggalMulai',
                    'RincianKegiatan' => 'required|string',
                    'has_sub_kegiatan' => 'required|in:yes,no',
                ]);
        
                // Begin transaction
                \DB::beginTransaction();
                
                try {
                    // Update Kegiatan
                    $kegiatan->ProgramRektorID = $request->ProgramRektorID;
                    $kegiatan->Nama = $request->Nama;
                    $kegiatan->TanggalMulai = $request->TanggalMulai;
                    $kegiatan->TanggalSelesai = $request->TanggalSelesai;
                    $kegiatan->RincianKegiatan = $request->RincianKegiatan;
                    $kegiatan->Feedback = $request->Feedback;
                    $kegiatan->DEdited = now();
                    $kegiatan->UEdited = Auth::id();
                    $kegiatan->save();
                    
                    // Process Sub Kegiatans
                    if ($request->has_sub_kegiatan === 'yes') {
                        // Handle existing sub kegiatans
                        if ($request->has('existing_sub_kegiatans') && is_array($request->existing_sub_kegiatans)) {
                            foreach ($request->existing_sub_kegiatans as $subKegiatanId => $subKegiatanData) {
                                $subKegiatan = SubKegiatan::find($subKegiatanId);
                                
                                if ($subKegiatan) {
                                    // Update sub kegiatan
                                    $subKegiatan->Nama = $subKegiatanData['Nama'];
                                    $subKegiatan->JadwalMulai = $subKegiatanData['JadwalMulai'];
                                    $subKegiatan->JadwalSelesai = $subKegiatanData['JadwalSelesai'];
                                    $subKegiatan->Catatan = $subKegiatanData['Catatan'];
                                    $subKegiatan->Feedback = $subKegiatanData['Feedback'];
                                    $subKegiatan->Status = $subKegiatanData['Status'] ?? 'N';
                                    $subKegiatan->DEdited = now();
                                    $subKegiatan->UEdited = Auth::id();
                                    $subKegiatan->save();
                                    
                                    // Handle existing RABs for this sub kegiatan
                                    if (isset($subKegiatanData['existing_rabs']) && is_array($subKegiatanData['existing_rabs'])) {
                                        foreach ($subKegiatanData['existing_rabs'] as $rabId => $rabData) {
                                            $rab = RAB::find($rabId);
                                            
                                            if ($rab) {
                                                // Update RAB
                                                $rab->Komponen = $rabData['Komponen'];
                                                $rab->Volume = str_replace('.', '', $rabData['Volume']);
                                                $rab->Satuan = $rabData['Satuan'];
                                                $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                                                $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                                                $rab->Feedback = $rabData['Feedback'];
                                                $rab->Status = $rabData['Status'] ?? 'N';
                                                $rab->DEdited = now();
                                                $rab->UEdited = Auth::id();
                                                $rab->save();
                                            }
                                        }
                                    }
                                    
                                    // Add new RABs for this sub kegiatan
                                    if (isset($subKegiatanData['new_rabs']) && is_array($subKegiatanData['new_rabs'])) {
                                        foreach ($subKegiatanData['new_rabs'] as $rabData) {
                                            if (empty($rabData['Komponen'])) continue;
                                            
                                            $rab = new RAB();
                                            $rab->SubKegiatanID = $subKegiatan->SubKegiatanID;
                                            $rab->KegiatanID = $kegiatan->KegiatanID;
                                            $rab->Komponen = $rabData['Komponen'];
                                            $rab->Volume = str_replace('.', '', $rabData['Volume']);
                                            $rab->Satuan = $rabData['Satuan'];
                                            $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                                            $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                                            $rab->Feedback = $rabData['Feedback'];
                                            $rab->Status = $rabData['Status'] ?? 'N';// Default status is Menunggu
                                            $rab->DCreated = now();
                                            $rab->UCreated = Auth::id();
                                            $rab->save();
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Add new sub kegiatans
                        if ($request->has('new_sub_kegiatans') && is_array($request->new_sub_kegiatans)) {
                            foreach ($request->new_sub_kegiatans as $subKegiatanData) {
                                if (empty($subKegiatanData['Nama'])) continue;
                                
                                $subKegiatan = new SubKegiatan();
                                $subKegiatan->KegiatanID = $kegiatan->KegiatanID;
                                $subKegiatan->Nama = $subKegiatanData['Nama'];
                                $subKegiatan->JadwalMulai = $subKegiatanData['JadwalMulai'];
                                $subKegiatan->JadwalSelesai = $subKegiatanData['JadwalSelesai'];
                                $subKegiatan->Catatan = $subKegiatanData['Catatan'];
                                $subKegiatan->Status = $subKegiatanData['Status'] ?? 'N';
                                $subKegiatan->Feedback = $subKegiatanData['Feedback'];
                                $subKegiatan->DCreated = now();
                                $subKegiatan->UCreated = Auth::id();
                                $subKegiatan->save();
                                
                                // Process RABs for this new SubKegiatan if any
                                if (isset($subKegiatanData['rabs']) && is_array($subKegiatanData['rabs'])) {
                                    foreach ($subKegiatanData['rabs'] as $rabData) {
                                        if (empty($rabData['Komponen'])) continue;
                                        
                                        $rab = new RAB();
                                        $rab->SubKegiatanID = $subKegiatan->SubKegiatanID;
                                        $rab->KegiatanID = $kegiatan->KegiatanID;
                                        $rab->Komponen = $rabData['Komponen'];
                                        $rab->Volume = str_replace('.', '', $rabData['Volume']);
                                        $rab->Satuan = $rabData['Satuan'];
                                        $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                                        $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                                        $rab->Feedback = $rabData['Feedback'];
                                       $rab->Status = $rabData['Status'] ?? 'N';// Default status is Menunggu
                                        $rab->DCreated = now();
                                        $rab->UCreated = Auth::id();
                                        $rab->save();
                                    }
                                }
                            }
                        }
                    }
                    
                    // Handle existing RABs for Kegiatan
                    if ($request->has('existing_rabs') && is_array($request->existing_rabs)) {
                        foreach ($request->existing_rabs as $rabId => $rabData) {
                            $rab = RAB::find($rabId);
                            
                            if ($rab) {
                                // Update RAB
                                $rab->Komponen = $rabData['Komponen'];
                                $rab->Volume = str_replace('.', '', $rabData['Volume']);
                                $rab->Satuan = $rabData['Satuan'];
                                $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                                $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                                $rab->Feedback = $rabData['Feedback'];
                                $rab->Status = $rabData['Status'] ?? 'N';
                                $rab->DEdited = now();
                                $rab->UEdited = Auth::id();
                                $rab->save();
                            }
                        }
                    }
                    
                    // Add new RABs for Kegiatan
                    if ($request->has('new_rabs') && is_array($request->new_rabs)) {
                        foreach ($request->new_rabs as $rabData) {
                            if (empty($rabData['Komponen'])) continue;
                            
                            $rab = new RAB();
                            $rab->KegiatanID = $kegiatan->KegiatanID;
                            $rab->SubKegiatanID = null; // This RAB belongs directly to Kegiatan
                            $rab->Komponen = $rabData['Komponen'];
                            $rab->Volume = str_replace('.', '', $rabData['Volume']);
                            $rab->Satuan = $rabData['Satuan'];
                            $rab->HargaSatuan = str_replace('.', '', $rabData['HargaSatuan']);
                            $rab->Jumlah = str_replace('.', '', $rabData['Volume']) * str_replace('.', '', $rabData['HargaSatuan']);
                            $rab->Feedback = $rabData['Feedback'];
                            $rab->Status = $rabData['Status'] ?? 'N';
                            $rab->DCreated = now();
                            $rab->UCreated = Auth::id();
                            $rab->save();
                        }
                    }
                    
                    // Handle deleted items
                   // Handle deleted items
                        if ($request->has('delete_sub_kegiatans') && is_array($request->delete_sub_kegiatans)) {
                            SubKegiatan::whereIn('SubKegiatanID', $request->delete_sub_kegiatans)->delete();
                        }

                        if ($request->has('delete_rabs') && is_array($request->delete_rabs)) {
                            RAB::whereIn('RABID', $request->delete_rabs)->delete();
                        }

                    
                    // Commit transaction
                    \DB::commit();
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => true, 'message' => 'Kegiatan berhasil diupdate']);
                    }
                    return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil diupdate');
                } catch (\Exception $e) {
                    // Rollback transaction on error
                    \DB::rollback();
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
                    }
                    return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
                }
            }
        
            public function destroy($id)
            {
                try {
                    // Begin transaction
                    \DB::beginTransaction();
                    
                    $kegiatan = Kegiatan::findOrFail($id);
                    
                    // Delete all related RABs
                    RAB::where('KegiatanID', $id)->delete();
                    
                    // Delete all related SubKegiatans
                    SubKegiatan::where('KegiatanID', $id)->delete();
                    
                    // Delete the Kegiatan
                    $kegiatan->delete();
                    
                    // Commit transaction
                    \DB::commit();
                    
                    if (request()->ajax()) {
                        return response()->json(['success' => true, 'message' => 'Kegiatan berhasil dihapus']);
                    }
                    
                    return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil dihapus');
                } catch (QueryException $e) {
                    // Rollback transaction on error
                    \DB::rollback();
                    
                    // Check if it's a foreign key constraint error
                    if ($e->getCode() == 23000) { // Integrity constraint violation
                        if (request()->ajax()) {
                            return response()->json([
                                'success' => false, 
                                'message' => 'Tidak dapat menghapus kegiatan ini karena dirujuk oleh baris di table lain.'
                            ], 422);
                        }
                        
                        return redirect()->route('kegiatans.index')
                            ->with('error', 'Tidak dapat menghapus kegiatan ini karena dirujuk oleh baris di table lain.');
                    }
                    
                    // For other database errors
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false, 
                            'message' => 'Database error occurred: ' . $e->getMessage()
                        ], 500);
                    }
                    
                    return redirect()->route('kegiatans.index')
                        ->with('error', 'Database error occurred: ' . $e->getMessage());
                }
            }
        }
        
        