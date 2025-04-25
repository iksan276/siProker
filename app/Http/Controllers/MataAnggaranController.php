<?php

namespace App\Http\Controllers;

use App\Models\MataAnggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class MataAnggaranController extends Controller
{
    public function index()
    {
        $mataAnggarans = MataAnggaran::with(['createdBy', 'editedBy'])
            ->orderBy('MataAnggaranID', 'desc')
            ->get();
        return view('mataAnggarans.index', compact('mataAnggarans'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('mataAnggarans.create', compact('users'))->render();
        }
        return view('mataAnggarans.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $mataAnggaran = new MataAnggaran();
        $mataAnggaran->Nama = $request->Nama;
        $mataAnggaran->NA = $request->NA;
        $mataAnggaran->DCreated = now();
        $mataAnggaran->UCreated = Auth::id();
        $mataAnggaran->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata Anggaran berhasil ditambahkan']);
        }
        return redirect()->route('mataAnggarans.index')->with('success', 'Mata Anggaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $mataAnggaran = MataAnggaran::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('mataAnggarans.show', compact('mataAnggaran'))->render();
        }
        return view('mataAnggarans.show', compact('mataAnggaran'));
    }

    public function edit($id)
    {
        $mataAnggaran = MataAnggaran::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('mataAnggarans.edit', compact('mataAnggaran', 'users'))->render();
        }
        return view('mataAnggarans.edit', compact('mataAnggaran', 'users'));
    }

    public function update(Request $request, $id)
    {
        $mataAnggaran = MataAnggaran::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $mataAnggaran->Nama = $request->Nama;
        $mataAnggaran->NA = $request->NA;
        $mataAnggaran->DEdited = now();
        $mataAnggaran->UEdited = Auth::id();
        $mataAnggaran->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mata Anggaran berhasil diupdate']);
        }
        return redirect()->route('mataAnggarans.index')->with('success', 'Mata Anggaran berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $mataAnggaran = MataAnggaran::findOrFail($id);
            $mataAnggaran->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Mata Anggaran berhasil dihapus']);
            }
            
            return redirect()->route('mataAnggarans.index')->with('success', 'Mata Anggaran berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  mata anggaran ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('mataAnggarans.index')
                    ->with('error', 'Tidak dapat menghapus  mata anggaran ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('mataAnggarans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
