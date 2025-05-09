<?php

namespace App\Http\Controllers;

use App\Models\RAB;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class RABController extends Controller
{
    public function index(Request $request)
    {
        $rabs = RAB::with(['kegiatan', 'subKegiatan', 'satuanRelation', 'createdBy', 'editedBy'])
            ->orderBy('RABID', 'asc')
            ->get();
            
        if ($request->ajax() && $request->wantsJson()) {
            $data = [];
            foreach ($rabs as $index => $rab) {
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('rabs.show', $rab->RABID).'" data-title="Detail RAB">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('rabs.edit', $rab->RABID).'" data-title="Edit RAB">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-rab" data-id="'.$rab->RABID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $data[] = [
                    'no' => $index + 1,
                    'kegiatan' => $rab->kegiatan ? $rab->kegiatan->Nama : 'N/A',
                    'sub_kegiatan' => $rab->subKegiatan ? $rab->subKegiatan->Nama : 'N/A',
                    'komponen' => nl2br($rab->Komponen),
                    'volume' => number_format($rab->Volume, 0, ',', '.'),
                    'satuan' => $rab->satuanRelation ? $rab->satuanRelation->Nama : 'N/A',
                    'harga_satuan' => 'Rp ' . number_format($rab->HargaSatuan, 0, ',', '.'),
                    'jumlah' => 'Rp ' . number_format($rab->Jumlah, 0, ',', '.'),
                    'feedback' => nl2br($rab->Feedback),
                    'status' => $rab->status_label,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('rabs.index', compact('rabs'));
    }

    public function create(Request $request)
    {
        $kegiatans = Kegiatan::all();
        $subKegiatans = SubKegiatan::all();
        $satuans = Satuan::where('NA', 'N')->get();
        $users = User::all();
        
        // Get the selected Kegiatan and SubKegiatan from the request
        $selectedKegiatan = request('kegiatanID');
        $selectedSubKegiatan = request('subKegiatanID');
        
        // If kegiatan is selected, filter subKegiatans
        if ($selectedKegiatan) {
            $subKegiatans = SubKegiatan::where('KegiatanID', $selectedKegiatan)->get();
        }
        
        if (request()->ajax()) {
            return view('rabs.create', compact('kegiatans', 'subKegiatans', 'satuans', 'users', 'selectedKegiatan', 'selectedSubKegiatan'))->render();
        }
        return view('rabs.create', compact('kegiatans', 'subKegiatans', 'satuans', 'users', 'selectedKegiatan', 'selectedSubKegiatan'));
    }

    public function store(Request $request)
    {
        // Clean numeric inputs from formatting before validation
        if ($request->has('Volume')) {
            $request->merge(['Volume' => (int) str_replace('.', '', $request->Volume)]);
        }
        
        if ($request->has('HargaSatuan')) {
            $request->merge(['HargaSatuan' => (int) str_replace('.', '', $request->HargaSatuan)]);
        }
    
        $request->validate([
            'KegiatanID' => 'nullable|exists:kegiatans,KegiatanID',
            'SubKegiatanID' => 'nullable|exists:sub_kegiatans,SubKegiatanID',
            'Komponen' => 'required|string',
            'Volume' => 'required|integer|min:1',
            'Satuan' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer|min:0',
        ]);
    
        // Ensure at least one of KegiatanID or SubKegiatanID is provided
        if (empty($request->KegiatanID) && empty($request->SubKegiatanID)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Harus memilih Kegiatan atau Sub Kegiatan'
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Harus memilih Kegiatan atau Sub Kegiatan'])->withInput();
        }
    
        // Calculate Jumlah
        $jumlah = $request->Volume * $request->HargaSatuan;
    
        $rab = new RAB();
        $rab->KegiatanID = $request->KegiatanID;
        $rab->SubKegiatanID = $request->SubKegiatanID;
        $rab->Komponen = $request->Komponen;
        $rab->Volume = $request->Volume;
        $rab->Satuan = $request->Satuan;
        $rab->HargaSatuan = $request->HargaSatuan;
        $rab->Jumlah = $jumlah;
        $rab->Feedback = $request->Feedback;
        $rab->Status = isset($request->Status) ? $request->Status : 'N';
        $rab->DCreated = now();
        $rab->UCreated = Auth::id();
        $rab->save();
    
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'RAB berhasil ditambahkan', 'rab' => $rab]);
        }
        return redirect()->route('rabs.index')->with('success', 'RAB berhasil ditambahkan');
    }
    

    public function show(RAB $rab)
    {
        $rab->load(['kegiatan', 'subKegiatan', 'satuanRelation', 'createdBy', 'editedBy']);
        
        if (request()->ajax()) {
            return view('rabs.show', compact('rab'))->render();
        }
        return view('rabs.show', compact('rab'));
    }

    public function edit(RAB $rab)
    {
        $kegiatans = Kegiatan::all();
        $subKegiatans = SubKegiatan::all();
        $satuans = Satuan::where('NA', 'N')->get();
        $users = User::all();
        
        // If RAB has a kegiatan, filter subKegiatans
        if ($rab->KegiatanID) {
            $subKegiatans = SubKegiatan::where('KegiatanID', $rab->KegiatanID)->get();
        }
        
        if (request()->ajax()) {
            return view('rabs.edit', compact('rab', 'kegiatans', 'subKegiatans', 'satuans', 'users'))->render();
        }
        return view('rabs.edit', compact('rab', 'kegiatans', 'subKegiatans', 'satuans', 'users'));
    }

    public function update(Request $request, RAB $rab)
    {
        // Clean numeric inputs from formatting before validation
        if ($request->has('Volume')) {
            $request->merge(['Volume' => (int) str_replace('.', '', $request->Volume)]);
        }
        
        if ($request->has('HargaSatuan')) {
            $request->merge(['HargaSatuan' => (int) str_replace('.', '', $request->HargaSatuan)]);
        }
    
        $request->validate([
            'KegiatanID' => 'nullable|exists:kegiatans,KegiatanID',
            'SubKegiatanID' => 'nullable|exists:sub_kegiatans,SubKegiatanID',
            'Komponen' => 'required|string',
            'Volume' => 'required|integer|min:1',
            'Satuan' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer|min:0',
        ]);
    
        // Ensure at least one of KegiatanID or SubKegiatanID is provided
        if (empty($request->KegiatanID) && empty($request->SubKegiatanID)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Harus memilih Kegiatan atau Sub Kegiatan'
                ], 422);
            }
            return redirect()->back()->withErrors(['error' => 'Harus memilih Kegiatan atau Sub Kegiatan'])->withInput();
        }
    
        // Calculate Jumlah
        $jumlah = $request->Volume * $request->HargaSatuan;
    
        $rab->KegiatanID = $request->KegiatanID;
        $rab->SubKegiatanID = $request->SubKegiatanID;
        $rab->Komponen = $request->Komponen;
        $rab->Volume = $request->Volume;
        $rab->Satuan = $request->Satuan;
        $rab->HargaSatuan = $request->HargaSatuan;
        $rab->Jumlah = $jumlah;
        $rab->Feedback = $request->Feedback;
        $rab->Status = isset($request->Status) ? $request->Status : 'N';
        $rab->DEdited = now();
        $rab->UEdited = Auth::id();
        $rab->save();
    
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'RAB berhasil diupdate', 'rab' => $rab]);
        }
        return redirect()->route('rabs.index')->with('success', 'RAB berhasil diupdate');
    }
    

    public function destroy(RAB $rab)
    {
        try {
            $rab->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'RAB berhasil dihapus']);
            }
            
            return redirect()->route('rabs.index')->with('success', 'RAB berhasil dihapus');
        } catch (QueryException $e) {
            // For database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('rabs.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}

