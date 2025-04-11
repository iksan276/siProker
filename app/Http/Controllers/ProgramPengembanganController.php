<?php

namespace App\Http\Controllers;

use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramPengembanganController extends Controller
{
    public function index()
    {
        $programPengembangans = ProgramPengembangan::with(['isuStrategis', 'createdBy', 'editedBy'])
        ->orderBy('DCreated', 'desc')
        ->get();
        return view('programPengembangans.index', compact('programPengembangans'));
    }

    public function create()
    {
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('programPengembangans.create', compact('isuStrategis', 'users'))->render();
        }
        return view('programPengembangans.create', compact('isuStrategis', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IsuID' => 'required|exists:isu_strategis,IsuID',
            'Nama' => 'required|string|max:255',
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('programPengembangans.index')->with('success', 'Program Pengembangan created successfully');
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
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $programPengembangan->IsuID = $request->IsuID;
        $programPengembangan->Nama = $request->Nama;
        $programPengembangan->NA = $request->NA;
        $programPengembangan->DEdited = now();
        $programPengembangan->UEdited = Auth::id();
        $programPengembangan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('programPengembangans.index')->with('success', 'Program Pengembangan updated successfully');
    }

    public function destroy(ProgramPengembangan $programPengembangan)
    {
        $programPengembangan->delete();
        return redirect()->route('programPengembangans.index')->with('success', 'Program Pengembangan deleted successfully');
    }
}
