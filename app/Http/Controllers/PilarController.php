<?php

namespace App\Http\Controllers;

use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Kegiatan;
use App\Models\ProgramRektor;

class PilarController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Base query
        $pilarsQuery = Pilar::with(['renstra', 'createdBy', 'editedBy', 'isuStrategis.programPengembangans.programRektors']);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            $pilarsQuery->where('RenstraID', $request->renstraID);
        }
        
        // Get the filtered results
        $pilars = $pilarsQuery->orderBy('PilarID', 'asc')->get();
        
        // Get the selected filter value (for re-populating the select)
        $selectedRenstra = $request->renstraID;
        
        // If user is not admin, prepare tree grid data
        if (!auth()->user()->isAdmin()) {
            $userId = Auth::id();
            
            if ($request->ajax() && $request->wantsJson()) {
                $treeData = $this->buildTreeData($pilars, $userId);
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            return view('pilars.user_index', compact('pilars', 'renstras', 'selectedRenstra'));
        }
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax() && $request->wantsJson()) {
            // If format=tree is requested, return tree data even for admin
            if ($request->has('format') && $request->format === 'tree') {
                $treeData = $this->buildTreeData($pilars, Auth::id());
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            // Otherwise return regular datatable format
            $data = [];
            foreach ($pilars as $index => $pilar) {
                // Format the actions HTML
                $actions = '';
                if (auth()->user()->isAdmin()) {
                    $actions = '
                        <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('pilars.show', $pilar->PilarID).'" data-title="Detail Pilar">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('pilars.edit', $pilar->PilarID).'" data-title="Edit Pilar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-square btn-sm delete-pilar" data-id="'.$pilar->PilarID.'">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }
                
                // Format the NA status
                $naStatus = '';
                if ($pilar->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($pilar->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                $data[] = [
                    'no' => $index + 1,
                    'renstra' => $pilar->renstra->Nama,
                    'nama' => nl2br($pilar->Nama),
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $pilar->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('pilars.index', compact('pilars', 'renstras', 'selectedRenstra'));
    }
    
    private function buildTreeData($pilars, $userId)
    {
        $treeData = [];
        $rowIndex = 1;
        
        foreach ($pilars as $pilar) {
            if ($pilar->NA == 'Y') continue; // Skip non-active pilars
            
            $pilarNode = [
                'id' => 'pilar_' . $pilar->PilarID,
                'no' => $rowIndex++,
                'nama' => $pilar->Nama,
                'type' => 'pilar',
                'parent' => null,
                'level' => 0,
                'has_children' => count($pilar->isuStrategis->where('NA', 'N')) > 0,
                'actions' => '',
                'row_class' => '',
                'tooltip' => 'Lihat isu strategis'
            ];
            
            $treeData[] = $pilarNode;
            
            // Add Isu Strategis
            foreach ($pilar->isuStrategis as $isu) {
                if ($isu->NA == 'Y') continue; // Skip non-active isu
                
                $isuNode = [
                    'id' => 'isu_' . $isu->IsuID,
                    'no' => '',
                    'nama' => $isu->Nama,
                    'type' => 'isu',
                    'parent' => 'pilar_' . $pilar->PilarID,
                    'level' => 1,
                    'has_children' => count($isu->programPengembangans->where('NA', 'N')) > 0,
                    'actions' => '',
                    'row_class' => '',
                    'tooltip' => 'Lihat program pengembangan'
                ];
                
                $treeData[] = $isuNode;
                
                // Add Program Pengembangan
                foreach ($isu->programPengembangans as $program) {
                    if ($program->NA == 'Y') continue; // Skip non-active programs
                    
                    $programNode = [
                        'id' => 'program_' . $program->ProgramPengembanganID,
                        'no' => '',
                        'nama' => $program->Nama,
                        'type' => 'program',
                        'parent' => 'isu_' . $isu->IsuID,
                        'level' => 2,
                        'has_children' => count($program->programRektors->where('NA', 'N')) > 0,
                        'actions' => '',
                        'row_class' => '',
                        'tooltip' => 'Lihat program rektor'
                    ];
                    
                    $treeData[] = $programNode;
                    
                    // Add Program Rektor
                    foreach ($program->programRektors as $rektor) {
                        if ($rektor->NA == 'Y') continue; // Skip non-active rektor programs
                        
                        // Get kegiatan count directly from program rektor's ProgramRektorID
                        $kegiatanCount = Kegiatan::where('ProgramRektorID', $rektor->ProgramRektorID)
                                        ->where('UCreated', $userId)
                                        ->count();
                        
                        $rektorNode = [
                            'id' => 'rektor_' . $rektor->ProgramRektorID,
                            'no' => '',
                            'nama' => $rektor->Nama,
                            'type' => 'rektor',
                            'parent' => 'program_' . $program->ProgramPengembanganID,
                            'level' => 3,
                            'has_children' => $kegiatanCount > 0,
                            'actions' => '
                                <button class="btn btn-info btn-square btn-sm load-modal" 
                                    data-url="' . route('program-rektors.show', $rektor->ProgramRektorID) . '" 
                                    data-title="Detail Program Rektor">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-square btn-sm load-modal" 
                                    data-url="' . route('kegiatans.create') . '?program_rektor=' . $rektor->ProgramRektorID . '" 
                                    data-title="Tambah Kegiatan">
                                    <i class="fas fa-plus"></i>
                                </button>',
                            'row_class' => '',
                            'tooltip' => 'Lihat kegiatan'
                        ];
                        
                        $treeData[] = $rektorNode;
                        
                        // Add Kegiatan directly under Program Rektor
                        $kegiatans = Kegiatan::where('ProgramRektorID', $rektor->ProgramRektorID)
                            ->where('UCreated', $userId)
                            ->get();
                            
                        foreach ($kegiatans as $kegiatan) {
                            $kegiatanNode = [
                                'id' => 'kegiatan_' . $kegiatan->KegiatanID,
                                'no' => '',
                                'nama' => $kegiatan->Nama,
                                'type' => 'kegiatan',
                                'parent' => 'rektor_' . $rektor->ProgramRektorID,
                                'level' => 4,
                                'has_children' => false,
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
                                'tooltip' => ''
                            ];
                            
                            $treeData[] = $kegiatanNode;
                        }
                    }
                }
            }
        }
        
        return $treeData;
    }
    

    public function create()
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $users = User::all();
        
        // Get the selected Renstra from the request
        $selectedRenstra = request('renstraID');
        
        if (request()->ajax()) {
            return view('pilars.create', compact('renstras', 'users', 'selectedRenstra'))->render();
        }
        return view('pilars.create', compact('renstras', 'users', 'selectedRenstra'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'RenstraID' => 'required|exists:renstras,RenstraID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $pilar = new Pilar();
        $pilar->RenstraID = $request->RenstraID;
        $pilar->Nama = $request->Nama;
        $pilar->NA = $request->NA;
        $pilar->DCreated = now();
        $pilar->UCreated = Auth::id();
        $pilar->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pilar berhasil ditambahkan']);
        }
        return redirect()->route('pilars.index')->with('success', 'Pilar berhasil ditambahkan');
    }

    public function show(Pilar $pilar)
    {
        if (request()->ajax()) {
            return view('pilars.show', compact('pilar'))->render();
        }
        return view('pilars.show', compact('pilar'));
    }

    public function edit(Pilar $pilar)
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('pilars.edit', compact('pilar', 'renstras', 'users'))->render();
        }
        return view('pilars.edit', compact('pilar', 'renstras', 'users'));
    }

    public function update(Request $request, Pilar $pilar)
    {
        $request->validate([
            'RenstraID' => 'required|exists:renstras,RenstraID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $pilar->RenstraID = $request->RenstraID;
        $pilar->Nama = $request->Nama;
        $pilar->NA = $request->NA;
        $pilar->DEdited = now();
        $pilar->UEdited = Auth::id();
        $pilar->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pilar berhasil diupdate']);
        }
        return redirect()->route('pilars.index')->with('success', 'Pilar berhasil diupdate');
    }

    public function destroy(Pilar $pilar)
    {
        try {
            $pilar->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pilar berhasil dihapus']);
            }
            
            return redirect()->route('pilars.index')->with('success', 'Pilar berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus pilar ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('pilars.index')
                    ->with('error', 'Tidak dapat menghapus pilar ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('pilars.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
