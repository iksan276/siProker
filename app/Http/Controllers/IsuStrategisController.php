<?php

namespace App\Http\Controllers;

use App\Models\IsuStrategis;
use App\Models\Pilar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsuStrategisController extends Controller
{
    public function index()
    {
        $isuStrategis = IsuStrategis::with(['pilar', 'createdBy', 'editedBy'])
            ->orderBy('IsuID', 'desc')
            ->get();
        return view('isuStrategis.index', compact('isuStrategis'));
    }

    public function create()
    {
        $pilars = Pilar::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('isuStrategis.create', compact('pilars', 'users'))->render();
        }
        return view('isuStrategis.create', compact('pilars', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'PilarID' => 'required|exists:pilars,PilarID',
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $isuStrategis = new IsuStrategis();
        $isuStrategis->PilarID = $request->PilarID;
        $isuStrategis->Nama = $request->Nama;
        $isuStrategis->NA = $request->NA;
        $isuStrategis->DCreated = now();
        $isuStrategis->UCreated = Auth::id();
        $isuStrategis->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis created successfully');
    }

    public function show($id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        
        if (request()->ajax()) {
            return view('isuStrategis.show', compact('isuStrategis'))->render();
        }
        return view('isuStrategis.show', compact('isuStrategis'));
    }

    public function edit($id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        $pilars = Pilar::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('isuStrategis.edit', compact('isuStrategis', 'pilars', 'users'))->render();
        }
        return view('isuStrategis.edit', compact('isuStrategis', 'pilars', 'users'));
    }

    public function update(Request $request, $id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        
        $request->validate([
            'PilarID' => 'required|exists:pilars,PilarID',
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $isuStrategis->PilarID = $request->PilarID;
        $isuStrategis->Nama = $request->Nama;
        $isuStrategis->NA = $request->NA;
        $isuStrategis->DEdited = now();
        $isuStrategis->UEdited = Auth::id();
        $isuStrategis->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis updated successfully');
    }

    public function destroy($id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        $isuStrategis->delete();
        return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis deleted successfully');
    }
}
