<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::with(['createdBy', 'editedBy'])
            ->orderBy('UnitID', 'desc')
            ->get();
        return view('units.index', compact('units'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('units.create', compact('users'))->render();
        }
        return view('units.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $unit = new Unit();
        $unit->Nama = $request->Nama;
        $unit->NA = $request->NA;
        $unit->DCreated = now();
        $unit->UCreated = Auth::id();
        $unit->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit berhasil ditambahkan']);
        }
        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan');
    }

    public function show($id)
    {
        $unit = Unit::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('units.show', compact('unit'))->render();
        }
        return view('units.show', compact('unit'));
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('units.edit', compact('unit', 'users'))->render();
        }
        return view('units.edit', compact('unit', 'users'));
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $unit->Nama = $request->Nama;
        $unit->NA = $request->NA;
        $unit->DEdited = now();
        $unit->UEdited = Auth::id();
        $unit->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit berhasil diupdate']);
        }
        return redirect()->route('units.index')->with('success', 'Unit berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Unit berhasil dihapus']);
            }
            
            return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  unit ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('units.index')
                    ->with('error', 'Tidak dapat menghapus  unit ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('units.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
