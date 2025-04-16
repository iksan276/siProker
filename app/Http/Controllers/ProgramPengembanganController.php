<?php

namespace App\Http\Controllers;

use App\Models\ProgramPengembangan;
use App\Models\IsuStrategis;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

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
