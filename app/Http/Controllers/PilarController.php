<?php

namespace App\Http\Controllers;

use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class PilarController extends Controller
{
    public function index()
    {
        $pilars = Pilar::with(['renstra', 'createdBy', 'editedBy'])
        ->orderBy('DCreated', 'desc')
        ->get();
        return view('pilars.index', compact('pilars'));
    }

    public function create()
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('pilars.create', compact('renstras', 'users'))->render();
        }
        return view('pilars.create', compact('renstras', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'RenstraID' => 'required|exists:renstras,RenstraID',
            'Nama' => 'required|string|max:255',
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
            'Nama' => 'required|string|max:255',
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
                        'message' => 'Tidak dapat menghapus  pilar ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('pilars.index')
                    ->with('error', 'Tidak dapat menghapus  pilar ini karena dirujuk oleh baris di table lain.');
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
