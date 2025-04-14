<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\ProgramRektor;
use App\Models\Satuan;
use App\Models\MetaAnggaran;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndikatorKinerjaController extends Controller
{
    public function index()
    {
        $indikatorKinerjas = IndikatorKinerja::with(['programRektor', 'satuan', 'unitTerkait', 'createdBy', 'editedBy'])
            ->orderBy('IndikatorKinerjaID', 'desc')
            ->get();
        return view('indikatorKinerjas.index', compact('indikatorKinerjas'));
    }

    public function create()
    {
        $programRektors = ProgramRektor::where('NA', 'N')->get();
        $satuans = Satuan::where('NA', 'N')->get();
        $metaAnggarans = MetaAnggaran::where('NA', 'N')->get();
        $units = Unit::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.create', compact('programRektors', 'satuans', 'metaAnggarans', 'units', 'users'))->render();
        }
        return view('indikatorKinerjas.create', compact('programRektors', 'satuans', 'metaAnggarans', 'units', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProgramRektorID' => 'required|exists:program_rektors,ProgramRektorID',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'Nama' => 'required|string|max:255',
            'Bobot' => 'required|integer',
            'HargaSatuan' => 'required|integer',
            'Jumlah' => 'required|integer',
            'MetaAnggaranID' => 'required|array',
            'UnitTerkaitID' => 'required|exists:units,UnitID',
            'NA' => 'required|in:Y,N',
        ]);

        $indikatorKinerja = new IndikatorKinerja();
        $indikatorKinerja->ProgramRektorID = $request->ProgramRektorID;
        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Bobot = $request->Bobot;
        $indikatorKinerja->HargaSatuan = $request->HargaSatuan;
        $indikatorKinerja->Jumlah = $request->Jumlah;
        $indikatorKinerja->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $indikatorKinerja->UnitTerkaitID = $request->UnitTerkaitID;
        $indikatorKinerja->NA = $request->NA;
        $indikatorKinerja->DCreated = now();
        $indikatorKinerja->UCreated = Auth::id();
        $indikatorKinerja->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('indikatorKinerjas.index')->with('success', 'Indikator Kinerja created successfully');
    }

    public function show($id)
    {
        $indikatorKinerja = IndikatorKinerja::with(['programRektor', 'satuan', 'unitTerkait', 'createdBy', 'editedBy'])->findOrFail($id);
        $metaAnggarans = MetaAnggaran::whereIn('MetaAnggaranID', explode(',', $indikatorKinerja->MetaAnggaranID))->get();
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.show', compact('indikatorKinerja', 'metaAnggarans'))->render();
        }
        return view('indikatorKinerjas.show', compact('indikatorKinerja', 'metaAnggarans'));
    }

    public function edit($id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        $programRektors = ProgramRektor::all();
        $satuans = Satuan::all();
        $metaAnggarans = MetaAnggaran::all();
        $units = Unit::all();
        $users = User::all();
        $selectedMetaAnggarans = explode(',', $indikatorKinerja->MetaAnggaranID);
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'programRektors', 'satuans', 'metaAnggarans', 'units', 'users', 'selectedMetaAnggarans'))->render();
        }
        return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'programRektors', 'satuans', 'metaAnggarans', 'units', 'users', 'selectedMetaAnggarans'));
    }

    public function update(Request $request, $id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        
        $request->validate([
            'ProgramRektorID' => 'required|exists:program_rektors,ProgramRektorID',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'Nama' => 'required|string|max:255',
            'Bobot' => 'required|integer',
            'HargaSatuan' => 'required|integer',
            'Jumlah' => 'required|integer',
            'MetaAnggaranID' => 'required|array',
            'UnitTerkaitID' => 'required|exists:units,UnitID',
            'NA' => 'required|in:Y,N',
        ]);

        $indikatorKinerja->ProgramRektorID = $request->ProgramRektorID;
        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Bobot = $request->Bobot;
        $indikatorKinerja->HargaSatuan = $request->HargaSatuan;
        $indikatorKinerja->Jumlah = $request->Jumlah;
        $indikatorKinerja->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $indikatorKinerja->UnitTerkaitID = $request->UnitTerkaitID;
        $indikatorKinerja->NA = $request->NA;
        $indikatorKinerja->DEdited = now();
        $indikatorKinerja->UEdited = Auth::id();
        $indikatorKinerja->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('indikatorKinerjas.index')->with('success', 'Indikator Kinerja updated successfully');
    }

    public function destroy($id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        $indikatorKinerja->delete();
        return redirect()->route('indikatorKinerjas.index')->with('success', 'Indikator Kinerja deleted successfully');
    }
}
