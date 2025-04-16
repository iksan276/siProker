<?php

namespace App\Http\Controllers;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramRektorsExport;

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
                    <form action="'.route('program-rektors.destroy', $program->ProgramRektorID).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="button" class="btn btn-danger btn-square btn-sm delete-confirm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
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
                    'nama' => $program->Nama,
                    'program_pengembangan' => $program->programPengembangan->Nama,
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
            'Nama' => 'required|string|max:255',
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('programRektors.index')->with('success', 'Program Rektor created successfully');
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
            'Nama' => 'required|string|max:255',
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('programRektors.index')->with('success', 'Program Rektor updated successfully');
    }

    public function destroy(ProgramRektor $programRektor)
    {
        $programRektor->delete();
        return redirect()->route('programRektors.index')->with('success', 'Program Rektor deleted successfully');
    }
}
