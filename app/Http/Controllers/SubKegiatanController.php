<?php

namespace App\Http\Controllers;

use App\Models\SubKegiatan;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class SubKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $subKegiatans = SubKegiatan::with(['kegiatan', 'createdBy', 'editedBy', 'rabs'])
            ->orderBy('SubKegiatanID', 'asc')
            ->get();
            
        if ($request->ajax() && $request->wantsJson()) {
            $data = [];
            foreach ($subKegiatans as $index => $subKegiatan) {
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('subKegiatans.show', $subKegiatan->SubKegiatanID).'" data-title="Detail Sub Kegiatan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('subKegiatans.edit', $subKegiatan->SubKegiatanID).'" data-title="Edit Sub Kegiatan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-sub-kegiatan" data-id="'.$subKegiatan->SubKegiatanID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $data[] = [
                    'no' => $index + 1,
                    'kegiatan' => $subKegiatan->kegiatan ? $subKegiatan->kegiatan->Nama : 'N/A',
                    'nama' => nl2br($subKegiatan->Nama),
                    'jadwal' => \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y'),
                    'catatan' => nl2br($subKegiatan->Catatan),
                    'status' => $subKegiatan->status_label,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('subKegiatans.index', compact('subKegiatans'));
    }

    public function create(Request $request)
    {
        $kegiatans = Kegiatan::all();
        $users = User::all();
        
        // Get the selected Kegiatan from the request
        $selectedKegiatan = request('kegiatanID');
        
        if (request()->ajax()) {
            return view('subKegiatans.create', compact('kegiatans', 'users', 'selectedKegiatan'))->render();
        }
        return view('subKegiatans.create', compact('kegiatans', 'users', 'selectedKegiatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'KegiatanID' => 'required|exists:kegiatans,KegiatanID',
            'Nama' => 'required|string',
            'JadwalMulai' => 'required|date',
            'JadwalSelesai' => 'required|date|after_or_equal:JadwalMulai',
        ]);

        $subKegiatan = new SubKegiatan();
        $subKegiatan->KegiatanID = $request->KegiatanID;
        $subKegiatan->Nama = $request->Nama;
        $subKegiatan->JadwalMulai = $request->JadwalMulai;
        $subKegiatan->JadwalSelesai = $request->JadwalSelesai;
        $subKegiatan->Catatan = $request->Catatan;
        $subKegiatan->Status = isset($request->Status) ? $request->Status : 'N';
        $subKegiatan->DCreated = now();
        $subKegiatan->UCreated = Auth::id();
        $subKegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sub Kegiatan berhasil ditambahkan', 'subKegiatan' => $subKegiatan]);
        }
        return redirect()->route('subKegiatans.index')->with('success', 'Sub Kegiatan berhasil ditambahkan');
    }

    public function show(SubKegiatan $subKegiatan)
    {
        $subKegiatan->load(['kegiatan', 'createdBy', 'editedBy', 'rabs.satuanRelation']);
        
        if (request()->ajax()) {
            return view('subKegiatans.show', compact('subKegiatan'))->render();
        }
        return view('subKegiatans.show', compact('subKegiatan'));
    }

    public function edit(SubKegiatan $subKegiatan)
    {
        $kegiatans = Kegiatan::all();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('subKegiatans.edit', compact('subKegiatan', 'kegiatans', 'users'))->render();
        }
        return view('subKegiatans.edit', compact('subKegiatan', 'kegiatans', 'users'));
    }

    public function update(Request $request, SubKegiatan $subKegiatan)
    {
        $request->validate([
            'KegiatanID' => 'required|exists:kegiatans,KegiatanID',
            'Nama' => 'required|string',
            'JadwalMulai' => 'required|date',
            'JadwalSelesai' => 'required|date|after_or_equal:JadwalMulai',
        ]);

        $subKegiatan->KegiatanID = $request->KegiatanID;
        $subKegiatan->Nama = $request->Nama;
        $subKegiatan->JadwalMulai = $request->JadwalMulai;
        $subKegiatan->JadwalSelesai = $request->JadwalSelesai;
        $subKegiatan->Catatan = $request->Catatan;
        $subKegiatan->Status = isset($request->Status) ? $request->Status : 'N';
        $subKegiatan->DEdited = now();
        $subKegiatan->UEdited = Auth::id();
        $subKegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sub Kegiatan berhasil diupdate', 'subKegiatan' => $subKegiatan]);
        }
        return redirect()->route('subKegiatans.index')->with('success', 'Sub Kegiatan berhasil diupdate');
    }

    public function destroy(SubKegiatan $subKegiatan)
    {
        try {
            $subKegiatan->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Sub Kegiatan berhasil dihapus']);
            }
            
            return redirect()->route('subKegiatans.index')->with('success', 'Sub Kegiatan berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus sub kegiatan ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('subKegiatans.index')
                    ->with('error', 'Tidak dapat menghapus sub kegiatan ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('subKegiatans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
