<?php

namespace App\Http\Controllers;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramRektorController extends Controller
{
    public function index()
    {
        $programRektors = ProgramRektor::with(['programPengembangan', 'createdBy', 'editedBy'])
        ->orderBy('DCreated', 'desc')
        ->get();
        return view('programRektors.index', compact('programRektors'));
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
