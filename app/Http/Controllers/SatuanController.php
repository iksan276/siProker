<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return response()->json(['success' => true]);
        }
        return redirect()->route('satuans.index')->with('success', 'Satuan created successfully');
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('satuans.index')->with('success', 'Satuan updated successfully');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);
        $satuan->delete();
        return redirect()->route('satuans.index')->with('success', 'Satuan deleted successfully');
    }
}
