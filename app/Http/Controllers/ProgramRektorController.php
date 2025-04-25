<?php

namespace App\Http\Controllers;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\IndikatorKinerja;
use App\Models\JenisKegiatan;
use App\Models\MetaAnggaran;
use App\Models\Satuan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramRektorsExport;
use Illuminate\Database\QueryException;

class ProgramRektorController extends Controller
{
    public function index(Request $request)
    {
        // Get all program pengembangans for the filter
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        
        // Base query
        $programRektorsQuery = ProgramRektor::with([
            'programPengembangan', 
            'indikatorKinerja', // Added indikatorKinerja relationship
            'jenisKegiatan', 
            'satuan', 
            'penanggungJawab', 
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            $programRektorsQuery->where('ProgramPengembanganID', $request->programPengembanganID);
        }
        
        // Apply filter if indikatorKinerjaID is provided
        if ($request->has('indikatorKinerjaID') && $request->indikatorKinerjaID) {
            $programRektorsQuery->where('IndikatorKinerjaID', $request->indikatorKinerjaID);
        }
        
        // Get the filtered results
        $programRektors = $programRektorsQuery->orderBy('DCreated', 'desc')->get();
        
        // Get the selected filter values
        $selectedProgramPengembangan = $request->programPengembanganID;
        $selectedIndikatorKinerja = $request->indikatorKinerjaID;
        
        // Get all indikator kinerjas for the filter
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($programRektors as $index => $program) {
                // Format the actions HTML
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('program-rektors.show', $program->ProgramRektorID).'" data-title="Detail Program Rektor">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('program-rektors.edit', $program->ProgramRektorID).'" data-title="Edit Program Rektor">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-program-rektor" data-id="'.$program->ProgramRektorID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                // Format the NA status
                $naStatus = '';
                if ($program->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($program->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                // Get meta anggaran names
                $metaAnggaranIds = explode(',', $program->MetaAnggaranID);
                $metaAnggaranNames = MetaAnggaran::whereIn('MetaAnggaranID', $metaAnggaranIds)
                    ->pluck('Nama')
                    ->implode(', ');
                
                // Get pelaksana names
                $pelaksanaIds = explode(',', $program->PelaksanaID);
                $pelaksanaNames = Unit::whereIn('UnitID', $pelaksanaIds)
                    ->pluck('Nama')
                    ->implode(', ');
                
                $data[] = [
                    'no' => $index + 1,
                    'program_pengembangan' => nl2br($program->programPengembangan->Nama),
                    'indikator_kinerja' => nl2br($program->indikatorKinerja->Nama), // Added indikator kinerja
                    'nama' => nl2br($program->Nama),
                    'jenis_kegiatan' => $program->jenisKegiatan->Nama,
                    'total' => 'Rp ' . number_format($program->Total, 0, ',', '.'),
                    'penanggung_jawab' => $program->penanggungJawab->Nama,
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $program->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('programRektors.index', compact('programRektors', 'programPengembangans', 'indikatorKinerjas', 'selectedProgramPengembangan', 'selectedIndikatorKinerja'));
    }

    public function create()
    {
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get(); // Added indikatorKinerjas
        $jenisKegiatans = JenisKegiatan::where('NA', 'N')->get();
        $metaAnggarans = MetaAnggaran::where('NA', 'N')->get();
        $satuans = Satuan::where('NA', 'N')->get();
        $units = Unit::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('programRektors.create', compact(
                'programPengembangans', 
                'indikatorKinerjas', // Added indikatorKinerjas
                'jenisKegiatans', 
                'metaAnggarans', 
                'satuans', 
                'units', 
                'users'
            ))->render();
        }
        
        return view('programRektors.create', compact(
            'programPengembangans', 
            'indikatorKinerjas', // Added indikatorKinerjas
            'jenisKegiatans', 
            'metaAnggarans', 
            'satuans', 
            'units', 
            'users'
        ));
    }

    public function exportExcel(Request $request)
    {
        // Base query with all necessary relationships
        $programRektorsQuery = ProgramRektor::with([
            'programPengembangan.isuStrategis.pilar.renstra',
            'indikatorKinerja', // Added indikatorKinerja relationship
            'jenisKegiatan',
            'satuan',
            'penanggungJawab',
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            $programRektorsQuery->where('ProgramPengembanganID', $request->programPengembanganID);
        }
        
        // Apply filter if indikatorKinerjaID is provided
        if ($request->has('indikatorKinerjaID') && $request->indikatorKinerjaID) {
            $programRektorsQuery->where('IndikatorKinerjaID', $request->indikatorKinerjaID);
        }
        
        // Get the filtered results
        $programRektors = $programRektorsQuery->orderBy('DCreated', 'desc')->get();
        
        return Excel::download(new ProgramRektorsExport($programRektors), 'program_rektors.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID', // Added validation
            'Nama' => 'required|string',
            'Output' => 'required|string',
            'Outcome' => 'required|string',
            'JenisKegiatanID' => 'required|exists:jenis_kegiatans,JenisKegiatanID',
            'MetaAnggaranID' => 'required|array',
            'JumlahKegiatan' => 'required|integer',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer',
            'Total' => 'required|integer',
            'PenanggungJawabID' => 'required|exists:units,UnitID',
            'PelaksanaID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        $programRektor = new ProgramRektor();
        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->IndikatorKinerjaID = $request->IndikatorKinerjaID; // Added IndikatorKinerjaID
        $programRektor->Nama = $request->Nama;
        $programRektor->Output = $request->Output;
        $programRektor->Outcome = $request->Outcome;
        $programRektor->JenisKegiatanID = $request->JenisKegiatanID;
        $programRektor->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $programRektor->JumlahKegiatan = $request->JumlahKegiatan;
        $programRektor->SatuanID = $request->SatuanID;
        $programRektor->HargaSatuan = $request->HargaSatuan;
        $programRektor->Total = $request->Total;
        $programRektor->PenanggungJawabID = $request->PenanggungJawabID;
        $programRektor->PelaksanaID = implode(',', $request->PelaksanaID);
        $programRektor->NA = $request->NA;
        $programRektor->DCreated = now();
        $programRektor->UCreated = Auth::id();
        $programRektor->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Rektor berhasil ditambahkan']);
        }
        return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil ditambahkan');
    }

    public function show(ProgramRektor $programRektor)
    {
        // Load relationships
        $programRektor->load([
            'programPengembangan',
            'indikatorKinerja', // Added indikatorKinerja relationship
            'jenisKegiatan',
            'satuan',
            'penanggungJawab',
        ]);
        
        // Get meta anggaran names
        $metaAnggaranIds = explode(',', $programRektor->MetaAnggaranID);
        $metaAnggarans = MetaAnggaran::whereIn('MetaAnggaranID', $metaAnggaranIds)->get();
        
        // Get pelaksana names
        $pelaksanaIds = explode(',', $programRektor->PelaksanaID);
        $pelaksanas = Unit::whereIn('UnitID', $pelaksanaIds)->get();
        
        if (request()->ajax()) {
            return view('programRektors.show', compact('programRektor', 'metaAnggarans', 'pelaksanas'))->render();
        }
        return view('programRektors.show', compact('programRektor', 'metaAnggarans', 'pelaksanas'));
    }

    public function edit(ProgramRektor $programRektor)
    {
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get(); // Added indikatorKinerjas
        $jenisKegiatans = JenisKegiatan::where('NA', 'N')->get();
        $metaAnggarans = MetaAnggaran::where('NA', 'N')->get();
        $satuans = Satuan::where('NA', 'N')->get();
        $units = Unit::where('NA', 'N')->get();
        $users = User::all();
        
        // Convert comma-separated IDs to arrays for select2 multiple
        $selectedMetaAnggarans = explode(',', $programRektor->MetaAnggaranID);
        $selectedPelaksanas = explode(',', $programRektor->PelaksanaID);
        
        if (request()->ajax()) {
            return view('programRektors.edit', compact(
                'programRektor',
                'programPengembangans', 
                'indikatorKinerjas', // Added indikatorKinerjas
                'jenisKegiatans', 
                'metaAnggarans', 
                'satuans', 
                'units', 
                'users',
                'selectedMetaAnggarans',
                'selectedPelaksanas'
            ))->render();
        }
        
        return view('programRektors.edit', compact(
            'programRektor',
            'programPengembangans', 
            'indikatorKinerjas', // Added indikatorKinerjas
            'jenisKegiatans', 
            'metaAnggarans', 
            'satuans', 
            'units', 
            'users',
            'selectedMetaAnggarans',
            'selectedPelaksanas'
        ));
    }

    public function update(Request $request, ProgramRektor $programRektor)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID', // Added validation
            'Nama' => 'required|string',
            'Output' => 'required|string',
            'Outcome' => 'required|string',
            'JenisKegiatanID' => 'required|exists:jenis_kegiatans,JenisKegiatanID',
            'MetaAnggaranID' => 'required|array',
            'JumlahKegiatan' => 'required|integer',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer',
            'Total' => 'required|integer',
            'PenanggungJawabID' => 'required|exists:units,UnitID',
            'PelaksanaID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->IndikatorKinerjaID = $request->IndikatorKinerjaID; // Added IndikatorKinerjaID
        $programRektor->Nama = $request->Nama;
        $programRektor->Output = $request->Output;
        $programRektor->Outcome = $request->Outcome;
        $programRektor->JenisKegiatanID = $request->JenisKegiatanID;
        $programRektor->MetaAnggaranID = implode(',', $request->MetaAnggaranID);
        $programRektor->JumlahKegiatan = $request->JumlahKegiatan;
        $programRektor->SatuanID = $request->SatuanID;
        $programRektor->HargaSatuan = $request->HargaSatuan;
        $programRektor->Total = $request->Total;
        $programRektor->PenanggungJawabID = $request->PenanggungJawabID;
        $programRektor->PelaksanaID = implode(',', $request->PelaksanaID);
        $programRektor->NA = $request->NA;
        $programRektor->DEdited = now();
        $programRektor->UEdited = Auth::id();
        $programRektor->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program Rektor berhasil diupdate']);
        }
        return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil diupdate');
    }

    public function destroy(ProgramRektor $programRektor)
    {
        try {
            $programRektor->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Program Rektor berhasil dihapus']);
            }
            
            return redirect()->route('program-rektors.index')->with('success', 'Program Rektor berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus program rektor ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('program-rektors.index')
                    ->with('error', 'Tidak dapat menghapus program rektor ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('program-rektors.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
