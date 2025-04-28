<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerja;
use App\Models\Satuan;
use App\Models\User;
use App\Models\Renstra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndikatorKinerjasExport;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class IndikatorKinerjaController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for filters
        $renstras = Renstra::where('NA', 'N')->orderBy('PeriodeMulai', 'desc')->get();
        
        // Get the latest active renstra as default
        $defaultRenstra = $renstras->first();
        
        // Get selected renstra or use default
        $selectedRenstraID = $request->renstraID ?? ($defaultRenstra ? $defaultRenstra->RenstraID : null);
        $selectedRenstra = $selectedRenstraID ? Renstra::find($selectedRenstraID) : $defaultRenstra;
        
        // Get all satuans for form
        $satuans = Satuan::where('NA', 'N')->get();
        
        // Base query - don't filter by RenstraID
        $indikatorKinerjasQuery = IndikatorKinerja::with(['satuan', 'createdBy', 'editedBy']);
        
        // Get all results without filtering by RenstraID
        $indikatorKinerjas = $indikatorKinerjasQuery->orderBy('IndikatorKinerjaID', 'asc')->get();
        
        // Generate year labels based on selected renstra
        $yearLabels = [];
        if ($selectedRenstra) {
            $startYear = (int)$selectedRenstra->PeriodeMulai;
            $endYear = (int)$selectedRenstra->PeriodeSelesai;
            
            for ($year = $startYear; $year <= $endYear; $year++) {
                $yearLabels[] = $year;
            }
        } else {
            // Default year labels if no renstra is selected (2025-2028)
            $yearLabels = [2025, 2026, 2027, 2028];
        }
        
        // Store year labels in session for use in other views and exports
        session(['yearLabels' => $yearLabels]);
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($indikatorKinerjas as $index => $indikatorKinerja) {
                // NA badge
                $naBadge = '';
                if ($indikatorKinerja->NA == 'Y') {
                    $naBadge = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($indikatorKinerja->NA == 'N') {
                    $naBadge = '<span class="badge badge-success">Aktif</span>';
                }
                
                // MendukungIKU badge
                $mendukungIKUBadge = '';
                if ($indikatorKinerja->MendukungIKU == 'Y') {
                    $mendukungIKUBadge = '<span class="badge badge-success">Ya</span>';
                } else if ($indikatorKinerja->MendukungIKU == 'N') {
                    $mendukungIKUBadge = '<span class="badge badge-danger">Tidak</span>';
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
                    'nama' => nl2br($indikatorKinerja->Nama),
                    'satuan' => $indikatorKinerja->satuan->Nama,
                    'baseline' => nl2br($indikatorKinerja->Baseline),
                    'tahun1' => nl2br($indikatorKinerja->Tahun1),
                    'tahun2' => nl2br($indikatorKinerja->Tahun2),
                    'tahun3' => nl2br($indikatorKinerja->Tahun3),
                    'tahun4' => nl2br($indikatorKinerja->Tahun4),
                    'mendukung_iku' => $mendukungIKUBadge,
                    'na' => $naBadge,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data,
                'yearLabels' => $yearLabels
            ]);
        }
        
        return view('indikatorKinerjas.index', compact('indikatorKinerjas', 'satuans', 'renstras', 'selectedRenstraID', 'yearLabels'));
    }

    public function exportExcel(Request $request)
    {
        // Get the selected renstra ID from the request or session
        $selectedRenstraID = $request->renstraID ?? session('selectedRenstraID');
        
        // Get the year labels from session
        $yearLabels = session('yearLabels', [2025, 2026, 2027, 2028]);
        
        // If renstra ID is provided but not in session, fetch the year labels
        if ($selectedRenstraID && !session('yearLabels')) {
            $renstra = Renstra::find($selectedRenstraID);
            if ($renstra) {
                $startYear = (int)$renstra->PeriodeMulai;
                $endYear = (int)$renstra->PeriodeSelesai;
                
                $yearLabels = [];
                for ($year = $startYear; $year <= $endYear; $year++) {
                    $yearLabels[] = $year;
                }
            }
        }
        
        // Get all indikator kinerjas without filtering by RenstraID
        $indikatorKinerjas = IndikatorKinerja::with(['satuan', 'createdBy', 'editedBy'])
            ->orderBy('IndikatorKinerjaID', 'desc')
            ->get();
        
        return Excel::download(new IndikatorKinerjasExport($indikatorKinerjas, $yearLabels), 'indikator_kinerjas.xlsx');
    }

    public function create()
    {
        $satuans = Satuan::where('NA', 'N')->get();
        $users = User::all();
        
        // Get the year labels from the session or use default
        $yearLabels = session('yearLabels', [2025, 2026, 2027, 2028]);
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.create', compact('satuans', 'users', 'yearLabels'))->render();
        }
        return view('indikatorKinerjas.create', compact('satuans', 'users', 'yearLabels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'Nama' => 'required|string',
            'Baseline' => 'nullable|string',
            'Tahun1' => 'nullable|string',
            'Tahun2' => 'nullable|string',
            'Tahun3' => 'nullable|string',
            'Tahun4' => 'nullable|string',
            'MendukungIKU' => 'required|in:Y,N',
            'NA' => 'required|in:Y,N',
        ]);

        $indikatorKinerja = new IndikatorKinerja();
        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Baseline = $request->Baseline;
        $indikatorKinerja->Tahun1 = $request->Tahun1;
        $indikatorKinerja->Tahun2 = $request->Tahun2;
        $indikatorKinerja->Tahun3 = $request->Tahun3;
        $indikatorKinerja->Tahun4 = $request->Tahun4;
        $indikatorKinerja->MendukungIKU = $request->MendukungIKU;
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
        
        // Get the year labels from the session or use default
        $yearLabels = session('yearLabels', [2025, 2026, 2027, 2028]);
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.show', compact('indikatorKinerja', 'yearLabels'))->render();
        }
        return view('indikatorKinerjas.show', compact('indikatorKinerja', 'yearLabels'));
    }

    public function edit($id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        $satuans = Satuan::all();
        $users = User::all();
        
        // Get the year labels from the session or use default
        $yearLabels = session('yearLabels', [2025, 2026, 2027, 2028]);
        
        if (request()->ajax()) {
            return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'satuans', 'users', 'yearLabels'))->render();
        }
        return view('indikatorKinerjas.edit', compact('indikatorKinerja', 'satuans', 'users', 'yearLabels'));
    }

    public function update(Request $request, $id)
    {
        $indikatorKinerja = IndikatorKinerja::findOrFail($id);
        
        $request->validate([
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'Nama' => 'required|string',
            'Baseline' => 'nullable|string',
            'Tahun1' => 'nullable|string',
            'Tahun2' => 'nullable|string',
            'Tahun3' => 'nullable|string',
            'Tahun4' => 'nullable|string',
            'MendukungIKU' => 'required|in:Y,N',
            'NA' => 'required|in:Y,N',
        ]);

        $indikatorKinerja->SatuanID = $request->SatuanID;
        $indikatorKinerja->Nama = $request->Nama;
        $indikatorKinerja->Baseline = $request->Baseline;
        $indikatorKinerja->Tahun1 = $request->Tahun1;
        $indikatorKinerja->Tahun2 = $request->Tahun2;
        $indikatorKinerja->Tahun3 = $request->Tahun3;
        $indikatorKinerja->Tahun4 = $request->Tahun4;
        $indikatorKinerja->MendukungIKU = $request->MendukungIKU;
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
                        'message' => 'Tidak dapat menghapus indikator kinerja ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('indikator-kinerjas.index')
                    ->with('error', 'Tidak dapat menghapus indikator kinerja ini karena dirujuk oleh baris di table lain.');
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

public function getRenstraYears($id)
{
    $renstra = Renstra::findOrFail($id);
    
    // Generate year labels based on renstra
    $yearLabels = [];
    $startYear = (int)$renstra->PeriodeMulai;
    $endYear = (int)$renstra->PeriodeSelesai;
    
    for ($year = $startYear; $year <= $endYear; $year++) {
        $yearLabels[] = $year;
    }
    
    // Store year labels and selected renstra ID in session for use in other views and exports
    session(['yearLabels' => $yearLabels, 'selectedRenstraID' => $id]);
    
    return response()->json([
        'success' => true,
        'yearLabels' => $yearLabels,
        'renstra' => $renstra
    ]);
}
}
