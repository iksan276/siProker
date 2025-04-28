<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class RenstraController extends Controller
{
    public function index()
    {
        $renstras = Renstra::with(['createdBy', 'editedBy'])
        ->orderBy('RenstraID', 'asc')
        ->get();
        return view('renstras.index', compact('renstras'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('renstras.create', compact('users'))->render();
        }
        return view('renstras.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'PeriodeMulai' => 'required',
            'PeriodeSelesai' => 'required',
            'NA' => 'required|in:Y,N',
        ]);

        $renstra = new Renstra();
        $renstra->Nama = $request->Nama;
        $renstra->PeriodeMulai = $request->PeriodeMulai;
        $renstra->PeriodeSelesai = $request->PeriodeSelesai;
        $renstra->NA = $request->NA;
        $renstra->DCreated = now();
        $renstra->UCreated = Auth::id();
        $renstra->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Renstra berhasil ditambahkan']);
        }
        return redirect()->route('renstras.index')->with('success', 'Renstra berhasil ditambahkan');
    }

    public function show(Renstra $renstra)
    {
        if (request()->ajax()) {
            return view('renstras.show', compact('renstra'))->render();
        }
        return view('renstras.show', compact('renstra'));
    }

    public function edit(Renstra $renstra)
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('renstras.edit', compact('renstra', 'users'))->render();
        }
        return view('renstras.edit', compact('renstra', 'users'));
    }

    public function update(Request $request, Renstra $renstra)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'PeriodeMulai' => 'required',
            'PeriodeSelesai' => 'required',
            'NA' => 'required|in:Y,N',
        ]);

        $renstra->Nama = $request->Nama;
        $renstra->PeriodeMulai = $request->PeriodeMulai;
        $renstra->PeriodeSelesai = $request->PeriodeSelesai;
        $renstra->NA = $request->NA;
        $renstra->DEdited = now();
        $renstra->UEdited = Auth::id();
        $renstra->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Renstra berhasil diupdate']);
        }
        return redirect()->route('renstras.index')->with('success', 'Renstra berhasil diupdate');
    }

    public function destroy(Renstra $renstra)
    {
        try {
            $renstra->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Renstra berhasil dihapus']);
            }
            
            return redirect()->route('renstras.index')->with('success', 'Renstra berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  renstra ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('renstras.index')
                    ->with('error', 'Tidak dapat menghapus  renstra ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('renstras.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
