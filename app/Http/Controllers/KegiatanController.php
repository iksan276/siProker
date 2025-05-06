<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
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
            'editedBy'
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
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
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
    
    public function exportExcel(Request $request)
    {
        // Base query with program rektor relationship
        $kegiatansQuery = Kegiatan::with([
            'programRektor', 
            'programRektor.programPengembangan', 
            'programRektor.programPengembangan.isuStrategis', 
            'programRektor.programPengembangan.isuStrategis.pilar',
            'programRektor.programPengembangan.isuStrategis.pilar.renstra'
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
             // Apply filter if programRektorID is provided
             if ($request->has('programRektorID') && $request->programRektorID) {
                $kegiatansQuery->where('ProgramRektorID', $request->programRektorID);
            }
            
            // Get the filtered results
            $kegiatans = $kegiatansQuery->orderBy('KegiatanID', 'desc')->get();
            
            return Excel::download(new KegiatansExport($kegiatans), 'kegiatans.xlsx');
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
            ]);
    
            $kegiatan = new Kegiatan();
            $kegiatan->ProgramRektorID = $request->ProgramRektorID;
            $kegiatan->Nama = $request->Nama;
            $kegiatan->TanggalMulai = $request->TanggalMulai;
            $kegiatan->TanggalSelesai = $request->TanggalSelesai;
            $kegiatan->RincianKegiatan = $request->RincianKegiatan;
            $kegiatan->DCreated = now();
            $kegiatan->UCreated = Auth::id();
            $kegiatan->save();
    
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Kegiatan berhasil ditambahkan']);
            }
            return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil ditambahkan');
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
                'editedBy'
            ])->findOrFail($id);
            
            if (request()->ajax()) {
                return view('kegiatans.show', compact('kegiatan'))->render();
            }
            return view('kegiatans.show', compact('kegiatan'));
        }
    
        public function edit($id)
        {
            $kegiatan = Kegiatan::findOrFail($id);
            
            // Get all active renstras, pilars, and isu strategis
            $renstras = Renstra::where('NA', 'N')->get();
            $pilars = Pilar::where('NA', 'N')->get();
            $isuStrategis = IsuStrategis::where('NA', 'N')->get();
            $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
            $programRektors = ProgramRektor::where('NA', 'N')->get();
            $users = User::all();
            
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
            ]);
    
            $kegiatan->ProgramRektorID = $request->ProgramRektorID;
            $kegiatan->Nama = $request->Nama;
            $kegiatan->TanggalMulai = $request->TanggalMulai;
            $kegiatan->TanggalSelesai = $request->TanggalSelesai;
            $kegiatan->RincianKegiatan = $request->RincianKegiatan;
            $kegiatan->DEdited = now();
            $kegiatan->UEdited = Auth::id();
            $kegiatan->save();
    
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Kegiatan berhasil diupdate']);
            }
            return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil diupdate');
        }
    
        public function destroy($id)
        {
            try {
                $kegiatan = Kegiatan::findOrFail($id);
                $kegiatan->delete();
                
                if (request()->ajax()) {
                    return response()->json(['success' => true, 'message' => 'Kegiatan berhasil dihapus']);
                }
                
                return redirect()->route('kegiatans.index')->with('success', 'Kegiatan berhasil dihapus');
            } catch (QueryException $e) {
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
    