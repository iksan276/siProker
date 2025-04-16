<?php

namespace App\Http\Controllers;

use App\Models\MetaAnggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class MetaAnggaranController extends Controller
{
    public function index()
    {
        $metaAnggarans = MetaAnggaran::with(['createdBy', 'editedBy'])
            ->orderBy('MetaAnggaranID', 'desc')
            ->get();
        return view('metaAnggarans.index', compact('metaAnggarans'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('metaAnggarans.create', compact('users'))->render();
        }
        return view('metaAnggarans.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $metaAnggaran = new MetaAnggaran();
        $metaAnggaran->Nama = $request->Nama;
        $metaAnggaran->NA = $request->NA;
        $metaAnggaran->DCreated = now();
        $metaAnggaran->UCreated = Auth::id();
        $metaAnggaran->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Meta Anggaran berhasil ditambahkan']);
        }
        return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran berhasil ditambahkan');
    }

    public function show($id)
    {
        $metaAnggaran = MetaAnggaran::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('metaAnggarans.show', compact('metaAnggaran'))->render();
        }
        return view('metaAnggarans.show', compact('metaAnggaran'));
    }

    public function edit($id)
    {
        $metaAnggaran = MetaAnggaran::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('metaAnggarans.edit', compact('metaAnggaran', 'users'))->render();
        }
        return view('metaAnggarans.edit', compact('metaAnggaran', 'users'));
    }

    public function update(Request $request, $id)
    {
        $metaAnggaran = MetaAnggaran::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $metaAnggaran->Nama = $request->Nama;
        $metaAnggaran->NA = $request->NA;
        $metaAnggaran->DEdited = now();
        $metaAnggaran->UEdited = Auth::id();
        $metaAnggaran->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Meta Anggaran berhasil diupdate']);
        }
        return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $metaAnggaran = MetaAnggaran::findOrFail($id);
            $metaAnggaran->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Meta Anggaran berhasil dihapus']);
            }
            
            return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  meta anggaran ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('metaAnggarans.index')
                    ->with('error', 'Tidak dapat menghapus  meta anggaran ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('metaAnggarans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
