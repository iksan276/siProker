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
