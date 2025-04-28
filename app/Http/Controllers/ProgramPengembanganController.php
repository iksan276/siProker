<?php

namespace App\Http\Controllers;

use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ProgramPengembanganController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Get all active pilars for the filter
        $pilars = Pilar::where('NA', 'N')->get();
        
        // Get all active isu strategis for the filter
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        
        // Base query
        $programPengembangansQuery = ProgramPengembangan::with(['isuStrategis.pilar.renstra', 'createdBy', 'editedBy']);
        
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
                
            $programPengembangansQuery->whereIn('IsuID', $isuIds);
            
            // Update pilars list based on selected renstra
            $pilars = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->get();
                
            // Update isu strategis list based on filtered pilars
            $isuStrategis = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if pilarID is provided
        if ($request->has('pilarID') && $request->pilarID) {
            // Filter isu strategis by pilarID
            $isuIds = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            $programPengembangansQuery->whereIn('IsuID', $isuIds);
            
            // Update isu strategis list based on selected pilar
            $isuStrategis = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if isuID is provided
        if ($request->has('isuID') && $request->isuID) {
            $programPengembangansQuery->where('IsuID', $request->isuID);
        }
        
        // Get the filtered results
        $programPengembangans = $programPengembangansQuery->orderBy('ProgramPengembanganID', 'asc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedRenstra = $request->renstraID;
        $selectedPilar = $request->pilarID;
        $selectedIsu = $request->isuID;
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax() && $request->wantsJson()) {
            $data = [];
            foreach ($programPengembangans as $index => $program) {
                // Format the actions HTML
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('program-pengembangans.show', $program->ProgramPengembanganID).'" data-title="Detail Program Pengembangan (PP)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('program-pengembangans.edit', $program->ProgramPengembanganID).'" data-title="Edit Program Pengembangan (PP)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-program" data-id="'.$program->ProgramPengembanganID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                // Format the NA status
                $naStatus = '';
                if ($program->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($program->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                $data[] = [
                    'no' => $index + 1,
                    'isu_strategis' => nl2br($program->isuStrategis->Nama),
                    'nama' => nl2br($program->Nama),
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $program->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('programPengembangans.index', compact('programPengembangans', 'renstras', 'pilars', 'isuStrategis', 'selectedRenstra', 'selectedPilar', 'selectedIsu'));
    }

    public function create()
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $pilars = Pilar::where('NA', 'N')->get();
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $users = User::all();
        
        // Get the selected filters from the request
        $selectedRenstra = request('renstraID');
        $selectedPilar = request('pilarID');
        $selectedIsu = request('isuID');
        
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
        }
        
        // If pilar is selected, filter isu strategis
        if ($selectedPilar) {
            $isuStrategis = IsuStrategis::where('PilarID', $selectedPilar)
                ->where('NA', 'N')
                ->get();
        }
        
        if (request()->ajax()) {
            return view('programPengembangans.create', compact('isuStrategis', 'users', 'renstras', 'pilars', 'selectedRenstra', 'selectedPilar', 'selectedIsu'))->render();
        }
        return view('programPengembangans.create', compact('isuStrategis', 'users', 'renstras', 'pilars', 'selectedRenstra', 'selectedPilar', 'selectedIsu'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IsuID' => 'required|exists:isu_strategis,IsuID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $programPengembangan = new ProgramPengembangan();
        $programPengembangan->IsuID = $request->IsuID;
        $programPengembangan->Nama = $request->Nama;
        $programPengembangan->NA = $request->NA;
        $programPengembangan->DCreated = now();
        $programPengembangan->UCreated = Auth::id();
        $programPengembangan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Pengembangan berhasil ditambahkan']);
        }
        return redirect()->route('program-pengembangans.index')->with('success', 'Program Pengembangan berhasil ditambahkan');
    }

    public function show(ProgramPengembangan $programPengembangan)
    {
        if (request()->ajax()) {
            return view('programPengembangans.show', compact('programPengembangan'))->render();
        }
        return view('programPengembangans.show', compact('programPengembangan'));
    }

    public function edit(ProgramPengembangan $programPengembangan)
    {
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('programPengembangans.edit', compact('programPengembangan', 'isuStrategis', 'users'))->render();
        }
        return view('programPengembangans.edit', compact('programPengembangan', 'isuStrategis', 'users'));
    }

    public function update(Request $request, ProgramPengembangan $programPengembangan)
    {
        $request->validate([
            'IsuID' => 'required|exists:isu_strategis,IsuID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $programPengembangan->IsuID = $request->IsuID;
        $programPengembangan->Nama = $request->Nama;
        $programPengembangan->NA = $request->NA;
        $programPengembangan->DEdited = now();
        $programPengembangan->UEdited = Auth::id();
        $programPengembangan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Pengembangan berhasil diupdate']);
        }
        return redirect()->route('program-pengembangans.index')->with('success', 'Program Pengembangan berhasil diupdate');
    }

    public function destroy(ProgramPengembangan $programPengembangan)
    {
        try {
            $programPengembangan->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Program Pengembangan berhasil dihapus']);
            }
            
            return redirect()->route('program-pengembangans.index')->with('success', 'Program Pengembangan berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  program pengembangan ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('program-pengembangans.index')
                    ->with('error', 'Tidak dapat menghapus  program pengembangan ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('program-pengembangans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
