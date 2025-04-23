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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndikatorKinerjasExport;
use Illuminate\Database\QueryException;

class IndikatorKinerjaController extends Controller
{
    public function index(Request $request)
    {
        // Get all program rektors and units for filters
        $programRektors = ProgramRektor::where('NA', 'N')->get();
        $units = Unit::where('NA', 'N')->get();
        
        // Base query
        $indikatorKinerjasQuery = IndikatorKinerja::with(['programRektor', 'satuan', 'createdBy', 'editedBy']);
        
        // Apply filter if programRektorID is provided
        if ($request->has('programRektorID') && $request->programRektorID) {
            $indikatorKinerjasQuery->where('ProgramRektorID', $request->programRektorID);
        }
        
        // Apply filter if unitIDs are provided (multiple units)
        if ($request->has('unitIDs') && !empty($request->unitIDs)) {
            $unitIDs = $request->unitIDs;
            $indikatorKinerjasQuery->where(function($query) use ($unitIDs) {
                foreach ($unitIDs as $unitID) {
                    $query->orWhere('UnitTerkaitID', $unitID)
                          ->orWhere('UnitTerkaitID', 'LIKE', $unitID.',%')
                          ->orWhere('UnitTerkaitID', 'LIKE', '%,'.$unitID)
                          ->orWhere('UnitTerkaitID', 'LIKE', '%,'.$unitID.',%');
                }
            });
        }
        
        // Get the filtered results
        $indikatorKinerjas = $indikatorKinerjasQuery->orderBy('IndikatorKinerjaID', 'desc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedProgramRektor = $request->programRektorID;
        $selectedUnitIDs = $request->unitIDs ?? [];
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($indikatorKinerjas as $index => $indikatorKinerja) {
                // Get meta anggarans
                $metaAnggarans = MetaAnggaran::whereIn('MetaAnggaranID', explode(',', $indikatorKinerja->MetaAnggaranID))->pluck('Nama')->toArray();
                $metaAnggaranHtml = '<ul class="mb-0">';
                foreach ($metaAnggarans as $metaAnggaran) {
                    $metaAnggaranHtml .= '<li>' . $metaAnggaran . '</li>';
                }
                $metaAnggaranHtml .= '</ul>';
                
                // Get unit terkaits
                $unitTerkaits = Unit::whereIn('UnitID', explode(',', $indikatorKinerja->UnitTerkaitID))->pluck('Nama')->toArray();
                $unitTerkaitHtml = '<ul class="mb-0">';
                foreach ($unitTerkaits as $unitTerkait) {
                    $unitTerkaitHtml .= '<li>' . $unitTerkait . '</li>';
                }
                $unitTerkaitHtml .= '</ul>';
                
                // NA badge
                $naBadge = '';
                if ($indikatorKinerja->NA == 'Y') {
                    $naBadge = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($indikatorKinerja->NA == 'N') {
                    $naBadge = '<span class="badge badge-success">Aktif</span>';
                }
                
                // Actions buttons
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('indikator-kinerjas.show', $indikatorKinerja->IndikatorKinerjaID).'" data-title="Detail Indikator Kinerja">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('indikator-kinerjas.edit', $indikatorKinerja->IndikatorKinerjaID).'" data-title="Edit Indikator Kinerja">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-indikator" data-id="'.$indikatorKinerja->IndikatorKinerjaID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $rowClass = $indikatorKinerja->NA == 'Y' ? 'bg-light text-muted' : '';
                
                $data[] = [
                    'DT_RowClass' => $rowClass,
                    'no' => $index + 1,
                    'program_rektor' => nl2br($indikatorKinerja->programRektor->Nama),
                    'nama' => nl2br($indikatorKinerja->Nama),
                    'bobot' => number_format($indikatorKinerja->Bobot , 0, ',', '.') . '%',
                    'satuan' => $indikatorKinerja->satuan->Nama,
                    'harga_satuan' => 'Rp ' . number_format($indikatorKinerja->HargaSatuan, 0, ',', '.'),
                    'jumlah' => number_format($indikatorKinerja->Jumlah, 0, ',', '.'),
                    'meta_anggaran' => $metaAnggaranHtml,
                    'unit_terkait' => $unitTerkaitHtml,
                    'na' => $naBadge,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('indikatorKinerjas.index', compact('indikatorKinerjas', 'programRektors', 'units', 'selectedProgramRektor', 'selectedUnitIDs'));
    }

    public function exportExcel(Request $request)
    {
        // Base query with all necessary relationships
        $indikatorKinerjasQuery = IndikatorKinerja::with([
            'programRektor.programPengembangan.isuStrategis.pilar.renstra',
            'satuan',
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if programRektorID is provided
        if ($request->has('programRektorID') && $request->programRektorID) {
            $indikatorKinerjasQuery->where('ProgramRektorID', $request->programRektorID);
        }
        
        // Apply filter if unitID is provided
        if ($request->has('unitID') && $request->unitID) {
            $unitID = $request->unitID;
            $indikatorKinerjasQuery->where(function($query) use ($unitID) {
                $query->where('UnitTerkaitID', $unitID)
                      ->orWhere('UnitTerkaitID', 'LIKE', $unitID.',%')
                      ->orWhere('UnitTerkaitID', 'LIKE', '%,'.$unitID)
                      ->orWhere('UnitTerkaitID', 'LIKE', '%,'.$unitID.',%');
            });
        }
        
        // Get the filtered results
        $indikatorKinerjas = $indikatorKinerjasQuery->orderBy('IndikatorKinerjaID', 'desc')->get();
        
        return Excel::download(new IndikatorKinerjasExport($indikatorKinerjas), 'indikator_kinerjas.xlsx');
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
            'Nama' => 'required|string',
            'Bobot' => 'required|numeric',
            'HargaSatuan' => 'required|numeric',
            'Jumlah' => 'required|numeric',
            'MetaAnggaranID' => 'required|array',
            'UnitTerkaitID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        // Clean numeric inputs from formatting
        $bobot = str_replace('.', '', $request->Bobot);
        $hargaSatuan = str_replace('.', '', $request->HargaSatuan);
        $jumlah = str_replace('.', '', $request->Jumlah);

        $indikatorKinerja = new IndikatorKinerja();
        $indikatorKinerja->ProgramRektorID = $request->ProgramRektorID;
        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Bobot = $bobot;
        $indikatorKinerja->HargaSatuan = $hargaSatuan;
        $indikatorKinerja->Jumlah = $jumlah;
        $indikatorKinerja->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $indikatorKinerja->UnitTerkaitID = implode(',', $request->UnitTerkaitID);
        $indikatorKinerja->NA = $request->NA;
        $indikatorKinerja->DCreated = now();
        $indikatorKinerja->UCreated = Auth::id();
        $indikatorKinerja->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Indikator Kinerja berhasil ditambahkan']);
        }
        return redirect()->route('indikator-kinerjas.index')->with('success', 'Indikator Kinerja berhasil ditambahkan');
    }

    public function show($id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        $metaAnggarans = MetaAnggaran::whereIn('MetaAnggaranID', explode(',', $indikatorKinerja->MetaAnggaranID))->get();
        $unitTerkaits = Unit::whereIn('UnitID', explode(',', $indikatorKinerja->UnitTerkaitID))->get();
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.show', compact('indikatorKinerja', 'metaAnggarans', 'unitTerkaits'))->render();
        }
        return view('indikatorKinerjas.show', compact('indikatorKinerja', 'metaAnggarans', 'unitTerkaits'));
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
        $selectedUnitTerkaits = explode(',', $indikatorKinerja->UnitTerkaitID);
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'programRektors', 'satuans', 'metaAnggarans', 'units', 'users', 'selectedMetaAnggarans', 'selectedUnitTerkaits'))->render();
        }
        return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'programRektors', 'satuans', 'metaAnggarans', 'units', 'users', 'selectedMetaAnggarans', 'selectedUnitTerkaits'));
    }

    public function update(Request $request, $id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        
        $request->validate([
            'ProgramRektorID' => 'required|exists:program_rektors,ProgramRektorID',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'Nama' => 'required|string',
            'Bobot' => 'required|numeric',
            'HargaSatuan' => 'required|numeric',
            'Jumlah' => 'required|numeric',
            'MetaAnggaranID' => 'required|array',
            'UnitTerkaitID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        // Clean numeric inputs from formatting
        $bobot = str_replace('.', '', $request->Bobot);
        $hargaSatuan = str_replace('.', '', $request->HargaSatuan);
        $jumlah = str_replace('.', '', $request->Jumlah);

        $indikatorKinerja->ProgramRektorID = $request->ProgramRektorID;
        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Bobot = $bobot;
        $indikatorKinerja->HargaSatuan = $hargaSatuan;
        $indikatorKinerja->Jumlah = $jumlah;
        $indikatorKinerja->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $indikatorKinerja->UnitTerkaitID = implode(',', $request->UnitTerkaitID);
        $indikatorKinerja->NA = $request->NA;
        $indikatorKinerja->DEdited = now();
        $indikatorKinerja->UEdited = Auth::id();
        $indikatorKinerja->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Indikator Kinerja berhasil diupdate']);
        }
        return redirect()->route('indikator-kinerjas.index')->with('success', 'Indikator Kinerja berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $indikatorKinerja = IndikatorKinerja::findOrFail($id);
            $indikatorKinerja->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Indikator Kinerja berhasil dihapus']);
            }
            
            return redirect()->route('indikator-kinerjas.index')->with('success', 'Indikator Kinerja berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  indikator kinerja ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('indikator-kinerjas.index')
                    ->with('error', 'Tidak dapat menghapus  indikator kinerja ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('indikator-kinerjas.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
