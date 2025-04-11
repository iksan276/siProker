<?php

namespace App\Http\Controllers;

use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RenstraController extends Controller
{
    public function index()
    {
        $renstras = Renstra::with(['createdBy', 'editedBy'])
        ->orderBy('DCreated', 'desc')
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('renstras.index')->with('success', 'Renstra created successfully');
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
            return response()->json(['success' => true]);
        }
        return redirect()->route('renstras.index')->with('success', 'Renstra updated successfully');
    }

    public function destroy(Renstra $renstra)
    {
        $renstra->delete();
        return redirect()->route('renstras.index')->with('success', 'Renstra deleted successfully');
    }
}
