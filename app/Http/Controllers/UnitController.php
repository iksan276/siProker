<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return response()->json(['success' => true]);
        }
        return redirect()->route('units.index')->with('success', 'Unit created successfully');
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('units.index')->with('success', 'Unit updated successfully');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Unit deleted successfully');
    }
}
