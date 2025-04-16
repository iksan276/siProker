<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\IndikatorKinerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KegiatansExport;

class KegiatanController extends Controller
{

    public function index(Request $request)
    {
        // Get all active indikator kinerjas for the filter
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        
        // Base query
        $kegiatansQuery = Kegiatan::with(['indikatorKinerja', 'createdBy', 'editedBy']);
        
        // Apply filter if indikatorKinerjaID is provided
        if ($request->has('indikatorKinerjaID') && $request->indikatorKinerjaID) {
            $kegiatansQuery->where('IndikatorKinerjaID', $request->indikatorKinerjaID);
        }
        
        // Get the filtered results
        $kegiatans = $kegiatansQuery->orderBy('KegiatanID', 'desc')->get();
        
        // Get the selected filter value (for re-populating the select)
        $selectedIndikatorKinerja = $request->indikatorKinerjaID;
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($kegiatans as $index => $kegiatan) {
                // Match the exact styling from the Meta Anggaran page, but without td tags
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('kegiatans.show', $kegiatan->KegiatanID).'" data-title="Detail Kegiatan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('kegiatans.edit', $kegiatan->KegiatanID).'" data-title="Edit Kegiatan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="'.route('kegiatans.destroy', $kegiatan->KegiatanID).'" method="POST" class="d-inline">
                        '.csrf_field().'
                        '.method_field('DELETE').'
                        <button type="button" class="btn btn-danger btn-square btn-sm delete-confirm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                ';
                
                $data[] = [
                    'no' => $index + 1,
                    'indikator_kinerja' => $kegiatan->indikatorKinerja->Nama,
                    'nama' => $kegiatan->Nama,
                    'tanggal_mulai' => \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y'),
                    'tanggal_selesai' => \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y'),
                    'rincian_kegiatan' => nl2br(\Illuminate\Support\Str::limit($kegiatan->RincianKegiatan, 50)),
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('kegiatans.index', compact('kegiatans', 'indikatorKinerjas', 'selectedIndikatorKinerja'));
    }
    
    

    public function exportExcel(Request $request)
    {
        // Base query with all necessary relationships
        $kegiatansQuery = Kegiatan::with([
            'indikatorKinerja.programRektor.programPengembangan.isuStrategis.pilar.renstra',
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if indikatorKinerjaID is provided
        if ($request->has('indikatorKinerjaID') && $request->indikatorKinerjaID) {
            $kegiatansQuery->where('IndikatorKinerjaID', $request->indikatorKinerjaID);
        }
        
        // Get the filtered results
        $kegiatans = $kegiatansQuery->orderBy('KegiatanID', 'desc')->get();
        
        return Excel::download(new KegiatansExport($kegiatans), 'kegiatans.xlsx');
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
