<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::with(['createdBy', 'editedBy'])
            ->orderBy('SatuanID', 'desc')
            ->get();
        return view('satuans.index', compact('satuans'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('satuans.create', compact('users'))->render();
        }
        return view('satuans.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $satuan = new Satuan();
        $satuan->Nama = $request->Nama;
        $satuan->NA = $request->NA;
        $satuan->DCreated = now();
        $satuan->UCreated = Auth::id();
        $satuan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Satuan berhasil ditambahkan']);
        }
        return redirect()->route('satuans.index')->with('success', 'Satuan berhasil ditambahkan');
    }

    public function show($id)
    {
        $satuan = Satuan::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('satuans.show', compact('satuan'))->render();
        }
        return view('satuans.show', compact('satuan'));
    }

    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('satuans.edit', compact('satuan', 'users'))->render();
        }
        return view('satuans.edit', compact('satuan', 'users'));
    }

    public function update(Request $request, $id)
    {
        $satuan = Satuan::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $satuan->Nama = $request->Nama;
        $satuan->NA = $request->NA;
        $satuan->DEdited = now();
        $satuan->UEdited = Auth::id();
        $satuan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Satuan berhasil diupdate']);
        }
        return redirect()->route('satuans.index')->with('success', 'Satuan berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $satuan = Satuan::findOrFail($id);
            $satuan->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Satuan berhasil dihapus']);
            }
            
            return redirect()->route('satuans.index')->with('success', 'Satuan berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  satuan ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('satuans.index')
                    ->with('error', 'Tidak dapat menghapus  satuan ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('satuans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
