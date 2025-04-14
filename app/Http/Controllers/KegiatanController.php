<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\IndikatorKinerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::with(['indikatorKinerja', 'createdBy', 'editedBy'])
            ->orderBy('KegiatanID', 'desc')
            ->get();
        return view('kegiatans.index', compact('kegiatans'));
    }

    public function create()
    {
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('kegiatans.create', compact('indikatorKinerjas', 'users'))->render();
        }
        return view('kegiatans.create', compact('indikatorKinerjas', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID',
            'Nama' => 'required|string|max:255',
            'TanggalMulai' => 'required|date',
            'TanggalSelesai' => 'required|date|after_or_equal:TanggalMulai',
            'RincianKegiatan' => 'required|string',
        ]);

        $kegiatan = new Kegiatan();
        $kegiatan->IndikatorKinerjaID = $request->IndikatorKinerjaID;
        $kegiatan->Nama = $request->Nama;
        $kegiatan->TanggalMulai = $request->TanggalMulai;
        $kegiatan->TanggalSelesai = $request->TanggalSelesai;
        $kegiatan->RincianKegiatan = $request->RincianKegiatan;
        $kegiatan->DCreated = now();
        $kegiatan->UCreated = Auth::id();
        $kegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('kegiatans.index')->with('success', 'Kegiatan created successfully');
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::with(['indikatorKinerja', 'createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('kegiatans.show', compact('kegiatan'))->render();
        }
        return view('kegiatans.show', compact('kegiatan'));
    }

    public function edit($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $indikatorKinerjas = IndikatorKinerja::all();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('kegiatans.edit', compact('kegiatan', 'indikatorKinerjas', 'users'))->render();
        }
        return view('kegiatans.edit', compact('kegiatan', 'indikatorKinerjas', 'users'));
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        $request->validate([
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID',
            'Nama' => 'required|string|max:255',
            'TanggalMulai' => 'required|date',
            'TanggalSelesai' => 'required|date|after_or_equal:TanggalMulai',
            'RincianKegiatan' => 'required|string',
        ]);

        $kegiatan->IndikatorKinerjaID = $request->IndikatorKinerjaID;
        $kegiatan->Nama = $request->Nama;
        $kegiatan->TanggalMulai = $request->TanggalMulai;
        $kegiatan->TanggalSelesai = $request->TanggalSelesai;
        $kegiatan->RincianKegiatan = $request->RincianKegiatan;
        $kegiatan->DEdited = now();
        $kegiatan->UEdited = Auth::id();
        $kegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('kegiatans.index')->with('success', 'Kegiatan updated successfully');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();
        return redirect()->route('kegiatans.index')->with('success', 'Kegiatan deleted successfully');
    }
}
