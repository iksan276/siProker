<?php

namespace App\Http\Controllers;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramRektorsExport;
use Illuminate\Database\QueryException;

class ProgramRektorController extends Controller
{
    public function index(Request $request)
    {
        // Get all program pengembangans for the filter
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        
        // Base query
        $programRektorsQuery = ProgramRektor::with(['programPengembangan', 'createdBy', 'editedBy']);
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            $programRektorsQuery->where('ProgramPengembanganID', $request->programPengembanganID);
        }
        
        // Get the filtered results
        $programRektors = $programRektorsQuery->orderBy('DCreated', 'desc')->get();
        
        // Get the selected filter value (for re-populating the select)
        $selectedProgramPengembangan = $request->programPengembanganID;
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($programRektors as $index => $program) {
                // Format the actions HTML
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('program-rektors.show', $program->ProgramRektorID).'" data-title="Detail Program Rektor">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('program-rektors.edit', $program->ProgramRektorID).'" data-title="Edit Program Rektor">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-program-rektor" data-id="'.$program->ProgramRektorID.'">
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
                    'program_pengembangan' => nl2br($program->programPengembangan->Nama),
                    'nama' => nl2br($program->Nama),
                    'tahun' => $program->Tahun,
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $program->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('programRektors.index', compact('programRektors', 'programPengembangans', 'selectedProgramPengembangan'));
    }

    public function create()
    {
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('programRektors.create', compact('programPengembangans', 'users'))->render();
        }
        return view('programRektors.create', compact('programPengembangans', 'users'));
    }

    public function exportExcel(Request $request)
    {
        // Base query with all necessary relationships
        $programRektorsQuery = ProgramRektor::with([
            'programPengembangan.isuStrategis.pilar.renstra',
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            $programRektorsQuery->where('ProgramPengembanganID', $request->programPengembanganID);
        }
        
        // Get the filtered results
        $programRektors = $programRektorsQuery->orderBy('DCreated', 'desc')->get();
        
        return Excel::download(new ProgramRektorsExport($programRektors), 'program_rektors.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'Nama' => 'required|string',
            'Tahun' => 'required',
            'NA' => 'required|in:Y,N',
        ]);

        $programRektor = new ProgramRektor();
        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->Nama = $request->Nama;
        $programRektor->Tahun = $request->Tahun;
        $programRektor->NA = $request->NA;
        $programRektor->DCreated = now();
        $programRektor->UCreated = Auth::id();
        $programRektor->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Rektor berhasil ditambahkan']);
        }
        return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil ditambahkan');
    }

    public function show(ProgramRektor $programRektor)
    {
        if (request()->ajax()) {
            return view('programRektors.show', compact('programRektor'))->render();
        }
        return view('programRektors.show', compact('programRektor'));
    }

    public function edit(ProgramRektor $programRektor)
    {
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('programRektors.edit', compact('programRektor', 'programPengembangans', 'users'))->render();
        }
        return view('programRektors.edit', compact('programRektor', 'programPengembangans', 'users'));
    }

    public function update(Request $request, ProgramRektor $programRektor)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'Nama' => 'required|string',
            'Tahun' => 'required',
            'NA' => 'required|in:Y,N',
        ]);

        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->Nama = $request->Nama;
        $programRektor->Tahun = $request->Tahun;
        $programRektor->NA = $request->NA;
        $programRektor->DEdited = now();
        $programRektor->UEdited = Auth::id();
        $programRektor->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Rektor berhasil diupdate']);
        }
        return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil diupdate');
    }

    public function destroy(ProgramRektor $programRektor)
    {
        try {
            $programRektor->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Program Rektor berhasil dihapus']);
            }
            
            return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  program rektor ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('program-rektors.index')
                    ->with('error', 'Tidak dapat menghapus  program rektor ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('program-rektors.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
