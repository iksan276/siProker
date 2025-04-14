<?php

namespace App\Http\Controllers;

use App\Models\MetaAnggaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return response()->json(['success' => true]);
        }
        return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran created successfully');
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran updated successfully');
    }

    public function destroy($id)
    {
        $metaAnggaran = MetaAnggaran::findOrFail($id);
        $metaAnggaran->delete();
        return redirect()->route('metaAnggarans.index')->with('success', 'Meta Anggaran deleted successfully');
    }
}
