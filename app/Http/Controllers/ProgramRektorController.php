<?php

namespace App\Http\Controllers;

use App\Models\ProgramRektor;
use App\Models\ProgramPengembangan;
use App\Models\IndikatorKinerja;
use App\Models\JenisKegiatan;
use App\Models\MataAnggaran;
use App\Models\Satuan;
use App\Models\Unit;
use App\Models\User;
use App\Models\Renstra;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramRektorsExport;
use Illuminate\Database\QueryException;

class ProgramRektorController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Get all active pilars for the filter
        $pilars = Pilar::where('NA', 'N')->get();
        
        // Get all active isu strategis for the filter
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        
        // Get all program pengembangans for the filter
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        
        // Get all indikator kinerjas for the filter
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        
        // Base query
        $programRektorsQuery = ProgramRektor::with([
            'programPengembangan.isuStrategis.pilar.renstra', 
            'indikatorKinerja', 
            'jenisKegiatan', 
            'satuan', 
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            // Filter pilars by renstraID
            $pilarIds = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->pluck('PilarID');
                
            // Filter isu strategis by pilar IDs
            $isuIds = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
            
            // Update pilars list based on selected renstra
            $pilars = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->get();
                
            // Update isu strategis list based on filtered pilars
            $isuStrategis = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->get();
                
            // Update program pengembangans list based on filtered isus
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if pilarID is provided
        if ($request->has('pilarID') && $request->pilarID) {
            // Filter isu strategis by pilarID
            $isuIds = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
            
            // Update isu strategis list based on selected pilar
            $isuStrategis = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->get();
                
            // Update program pengembangans list based on filtered isus
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if isuID is provided
        if ($request->has('isuID') && $request->isuID) {
            // Filter program pengembangans by isuID
            $programIds = ProgramPengembangan::where('IsuID', $request->isuID)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
            
            // Update program pengembangans list based on selected isu
            $programPengembangans = ProgramPengembangan::where('IsuID', $request->isuID)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if programPengembanganID is provided
        if ($request->has('programPengembanganID') && $request->programPengembanganID) {
            $programRektorsQuery->where('ProgramPengembanganID', $request->programPengembanganID);
        }
        
        // Apply filter if indikatorKinerjaID is provided
        if ($request->has('indikatorKinerjaID') && $request->indikatorKinerjaID) {
            $programRektorsQuery->where('IndikatorKinerjaID', $request->indikatorKinerjaID);
        }
        
        // Get the filtered results
        $programRektors = $programRektorsQuery->orderBy('ProgramRektorID', 'asc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedRenstra = $request->renstraID;
        $selectedPilar = $request->pilarID;
        $selectedIsu = $request->isuID;
        $selectedProgramPengembangan = $request->programPengembanganID;
        $selectedIndikatorKinerja = $request->indikatorKinerjaID;
        
        // Ambil SSO code dari session untuk API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        $units = [];
        if ($response->successful()) {
            $units = $response->json();
        }
        
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
                
                // Get mata anggaran names and format as ul/li
                $mataAnggaranIds = explode(',', $program->MataAnggaranID);
                $mataAnggaranItems = MataAnggaran::whereIn('MataAnggaranID', $mataAnggaranIds)->pluck('Nama')->toArray();
                $mataAnggaranHtml = '';
                if (count($mataAnggaranItems) > 0) {
                    $mataAnggaranHtml = '<ul class=" mb-0">';
                    foreach ($mataAnggaranItems as $item) {
                        $mataAnggaranHtml .= '<li>' . $item . '</li>';
                    }
                    $mataAnggaranHtml .= '</ul>';
                }
                
                // Get pelaksana names from API data and format as ul/li
                $pelaksanaIds = explode(',', $program->PelaksanaID);
                $pelaksanaItems = [];
                foreach ($units as $unit) {
                    if (in_array($unit['UnitID'], $pelaksanaIds)) {
                        $pelaksanaItems[] = $unit['Nama'];
                    }
                }
                
                $pelaksanaHtml = '';
                if (count($pelaksanaItems) > 0) {
                    $pelaksanaHtml = '<ul class=" mb-0">';
                    foreach ($pelaksanaItems as $item) {
                        $pelaksanaHtml .= '<li>' . $item . '</li>';
                    }
                    $pelaksanaHtml .= '</ul>';
                }
                
                // Find penanggung jawab name from API data
                $penanggungJawabNama = '';
                foreach ($units as $unit) {
                    if ($unit['UnitID'] == $program->PenanggungJawabID) {
                        $penanggungJawabNama = $unit['Nama'];
                        break;
                    }
                }
                
                // In the index method, modify the data array construction:
                $data[] = [
                    'no' => $index + 1,
                    'program_pengembangan' => nl2br($program->programPengembangan->Nama),
                    'indikator_kinerja' => nl2br($program->indikatorKinerja->Nama),
                    'nama' => nl2br($program->Nama),
                    'jenis_kegiatan' => $program->jenisKegiatan->Nama,
                    'jumlah_kegiatan' => number_format($program->JumlahKegiatan, 0, ',', '.'),
                    'satuan' => $program->satuan->Nama,
                    'harga_satuan' => 'Rp ' . number_format($program->HargaSatuan, 0, ',', '.'),
                    'mata_anggaran' => $mataAnggaranHtml,
                    'total' => 'Rp ' . number_format($program->Total, 0, ',', '.'),
                    'penanggung_jawab' => $penanggungJawabNama,
                    'pelaksana' => $pelaksanaHtml,
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $program->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('programRektors.index', compact(
            'programRektors', 
            'renstras', 
            'pilars', 
            'isuStrategis', 
            'programPengembangans', 
            'indikatorKinerjas', 
            'selectedRenstra', 
            'selectedPilar', 
            'selectedIsu', 
            'selectedProgramPengembangan', 
            'selectedIndikatorKinerja',
            'units'
        ));
    }

    public function create()
    {
        // Get all active renstras, pilars, and isu strategis
        $renstras = Renstra::where('NA', 'N')->get();
        $pilars = Pilar::where('NA', 'N')->get();
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        $jenisKegiatans = JenisKegiatan::where('NA', 'N')->get();
        $mataAnggarans = MataAnggaran::where('NA', 'N')->get();
        $satuans = Satuan::where('NA', 'N')->get();
        $users = User::all();
        
        // Ambil SSO code dari session untuk API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        $units = [];
        if ($response->successful()) {
            $units = $response->json();
        } else {
            if (request()->ajax()) {
                return response()->json(['error' => 'Gagal mengambil data unit dari API: ' . $response->status()], 500);
            }
            return redirect()->route('program-rektors.index')->with('error', 'Gagal mengambil data unit dari API: ' . $response->status());
        }
        
        // Get the selected filters from the request
        $selectedRenstra = request('renstraID');
        $selectedPilar = request('pilarID');
        $selectedIsu = request('isuID');
        $selectedProgramPengembangan = request('programPengembanganID');
        
        // If renstra is selected, filter pilars
        if ($selectedRenstra) {
            $pilars = Pilar::where('RenstraID', $selectedRenstra)
                ->where('NA', 'N')
                ->get();
                
            // Filter isu strategis by pilars from selected renstra
            $pilarIds = $pilars->pluck('PilarID')->toArray();
            $isuStrategis = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->get();
                
            // Filter program pengembangans by isu strategis from selected pilars
            $isuIds = $isuStrategis->pluck('IsuID')->toArray();
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // If pilar is selected, filter isu strategis
        if ($selectedPilar) {
            $isuStrategis = IsuStrategis::where('PilarID', $selectedPilar)
                ->where('NA', 'N')
                ->get();
                
            // Filter program pengembangans by isu strategis from selected pilar
            $isuIds = $isuStrategis->pluck('IsuID')->toArray();
            $programPengembangans = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->get();
        }
        
        // If isu strategis is selected, filter program pengembangans
        if ($selectedIsu) {
            $programPengembangans = ProgramPengembangan::where('IsuID', $selectedIsu)
                ->where('NA', 'N')
                ->get();
        }
        
        if (request()->ajax()) {
            return view('programRektors.create', compact(
                'renstras',
                'pilars',
                'isuStrategis',
                'programPengembangans', 
                'indikatorKinerjas',
                'jenisKegiatans', 
                'mataAnggarans', 
                'satuans',
                'units', 
                'users',
                'selectedRenstra',
                'selectedPilar',
                'selectedIsu',
                'selectedProgramPengembangan'
            ))->render();
        }
        
        return view('programRektors.create', compact(
            'renstras',
            'pilars',
            'isuStrategis',
            'programPengembangans', 
            'indikatorKinerjas',
            'jenisKegiatans', 
            'mataAnggarans', 
            'satuans', 
            'units', 
            'users',
            'selectedRenstra',
            'selectedPilar',
            'selectedIsu',
            'selectedProgramPengembangan'
        ));
    }

    public function exportExcel(Request $request)
    {
        // Base query with all necessary relationships
        $programRektorsQuery = ProgramRektor::with([
            'programPengembangan.isuStrategis.pilar.renstra',
            'indikatorKinerja',
            'jenisKegiatan',
            'satuan',
            'createdBy', 
            'editedBy'
        ]);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            // Filter pilars by renstraID
            $pilarIds = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->pluck('PilarID');
                
            // Filter isu strategis by pilar IDs
            $isuIds = IsuStrategis::whereIn('PilarID', $pilarIds)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
        }
        
        // Apply filter if pilarID is provided
        if ($request->has('pilarID') && $request->pilarID) {
            // Filter isu strategis by pilarID
            $isuIds = IsuStrategis::where('PilarID', $request->pilarID)
                ->where('NA', 'N')
                ->pluck('IsuID');
                
            // Filter program pengembangans by isu IDs
            $programIds = ProgramPengembangan::whereIn('IsuID', $isuIds)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
        }
        
        // Apply filter if isuID is provided
        if ($request->has('isuID') && $request->isuID) {
            // Filter program pengembangans by isuID
            $programIds = ProgramPengembangan::where('IsuID', $request->isuID)
                ->where('NA', 'N')
                ->pluck('ProgramPengembanganID');
                
            $programRektorsQuery->whereIn('ProgramPengembanganID', $programIds);
        }
        
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
        
        // Ambil SSO code dari session untuk API
        $ssoCode = session('sso_code');
        
        if ($ssoCode) {
            // Hit API untuk mendapatkan data unit
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $ssoCode,
            ])->get("https://webhook.itp.ac.id/api/units", [
                'order_by' => 'Nama',
                'sort' => 'asc',
                'limit' => 100
            ]);
            
            if ($response->successful()) {
                $units = $response->json();
                // Attach unit data to program rektors
                foreach ($programRektors as $program) {
                    // Find penanggung jawab
                    foreach ($units as $unit) {
                        if ($unit['UnitID'] == $program->PenanggungJawabID) {
                            $program->penanggungJawabNama = $unit['Nama'];
                            break;
                        }
                    }
                    
                    // Find pelaksana
                    $pelaksanaIds = explode(',', $program->PelaksanaID);
                    $pelaksanaNames = [];
                    foreach ($units as $unit) {
                        if (in_array($unit['UnitID'], $pelaksanaIds)) {
                            $pelaksanaNames[] = $unit['Nama'];
                        }
                    }
                    $program->pelaksanaNames = $pelaksanaNames;
                }
            }
        }
        
        return Excel::download(new ProgramRektorsExport($programRektors), 'program_rektors.xlsx');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID',
            'Nama' => 'required|string',
            'Output' => 'required|string',
            'Outcome' => 'required|string',
            'JenisKegiatanID' => 'required|exists:jenis_kegiatans,JenisKegiatanID',
            'MataAnggaranID' => 'required|array',
            'JumlahKegiatan' => 'required|integer',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer',
            'Total' => 'required|integer',
            'PenanggungJawabID' => 'required',
            'PelaksanaID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        // Verify that the PenanggungJawabID and PelaksanaID exist in the API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        if (!$response->successful()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengambil data unit dari API: ' . $response->status()], 500);
            }
            return redirect()->route('program-rektors.index')->with('error', 'Gagal mengambil data unit dari API: ' . $response->status());
        }
        
        $units = $response->json();
        $unitIds = array_column($units, 'UnitID');
        
        // Verify PenanggungJawabID exists
        if (!in_array($request->PenanggungJawabID, $unitIds)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Penanggung jawab yang dipilih tidak valid.'], 422);
            }
            return redirect()->back()->withInput()->withErrors(['PenanggungJawabID' => 'Penanggung jawab yang dipilih tidak valid.']);
        }
        
        // Verify all PelaksanaID exist
        foreach ($request->PelaksanaID as $pelaksanaId) {
            if (!in_array($pelaksanaId, $unitIds)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Salah satu pelaksana yang dipilih tidak valid.'], 422);
                }
                return redirect()->back()->withInput()->withErrors(['PelaksanaID' => 'Salah satu pelaksana yang dipilih tidak valid.']);
            }
        }

        $programRektor = new ProgramRektor();
        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->IndikatorKinerjaID = $request->IndikatorKinerjaID;
        $programRektor->Nama = $request->Nama;
        $programRektor->Output = $request->Output;
        $programRektor->Outcome = $request->Outcome;
        $programRektor->JenisKegiatanID = $request->JenisKegiatanID;
        $programRektor->MataAnggaranID = implode(',', $request->MataAnggaranID);
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
            'programPengembangan.isuStrategis.pilar.renstra',
            'indikatorKinerja',
            'jenisKegiatan',
            'satuan',
        ]);
        
        // Get mata anggaran names
        $mataAnggaranIds = explode(',', $programRektor->MataAnggaranID);
        $mataAnggarans = MataAnggaran::whereIn('MataAnggaranID', $mataAnggaranIds)->get();
        
        // Ambil SSO code dari session untuk API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        if (!$response->successful()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Gagal mengambil data unit dari API: ' . $response->status()], 500);
            }
            return redirect()->route('program-rektors.index')->with('error', 'Gagal mengambil data unit dari API: ' . $response->status());
        }
        
        $units = $response->json();
        
        // Get penanggung jawab from API data
        $penanggungJawab = null;
        foreach ($units as $unit) {
            if ($unit['UnitID'] == $programRektor->PenanggungJawabID) {
                $penanggungJawab = $unit;
                break;
            }
        }
        
        // Get pelaksana from API data
        $pelaksanaIds = explode(',', $programRektor->PelaksanaID);
        $pelaksanas = [];
        foreach ($units as $unit) {
            if (in_array($unit['UnitID'], $pelaksanaIds)) {
                $pelaksanas[] = $unit;
            }
        }
        
        if (request()->ajax()) {
            return view('programRektors.show', compact('programRektor', 'mataAnggarans', 'penanggungJawab', 'pelaksanas'))->render();
        }
        return view('programRektors.show', compact('programRektor', 'mataAnggarans', 'penanggungJawab', 'pelaksanas'));
    }

    public function edit(ProgramRektor $programRektor)
    {
        // Get all active renstras, pilars, and isu strategis
        $renstras = Renstra::where('NA', 'N')->get();
        $pilars = Pilar::where('NA', 'N')->get();
        $isuStrategis = IsuStrategis::where('NA', 'N')->get();
        $programPengembangans = ProgramPengembangan::where('NA', 'N')->get();
        $indikatorKinerjas = IndikatorKinerja::where('NA', 'N')->get();
        $jenisKegiatans = JenisKegiatan::where('NA', 'N')->get();
        $mataAnggarans = MataAnggaran::where('NA', 'N')->get();
        $satuans = Satuan::where('NA', 'N')->get();
        $users = User::all();
        
        // Ambil SSO code dari session untuk API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        if (!$response->successful()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Gagal mengambil data unit dari API: ' . $response->status()], 500);
            }
            return redirect()->route('program-rektors.index')->with('error', 'Gagal mengambil data unit dari API: ' . $response->status());
        }
        
        $units = $response->json();
        
        // Load the program's relationships to get the hierarchy
        $programRektor->load('programPengembangan.isuStrategis.pilar.renstra');
        
        // Get the selected values from the loaded relationships
        $selectedRenstra = $programRektor->programPengembangan->isuStrategis->pilar->renstra->RenstraID;
        $selectedPilar = $programRektor->programPengembangan->isuStrategis->pilar->PilarID;
        $selectedIsu = $programRektor->programPengembangan->isuStrategis->IsuID;
        $selectedProgramPengembangan = $programRektor->ProgramPengembanganID;
        
        // Filter pilars by selected renstra
        if ($selectedRenstra) {
            $pilars = Pilar::where('RenstraID', $selectedRenstra)
                ->where('NA', 'N')
                ->get();
        }
        
        // Filter isu strategis by selected pilar
        if ($selectedPilar) {
            $isuStrategis = IsuStrategis::where('PilarID', $selectedPilar)
                ->where('NA', 'N')
                ->get();
        }
        
        // Filter program pengembangans by selected isu
        if ($selectedIsu) {
            $programPengembangans = ProgramPengembangan::where('IsuID', $selectedIsu)
                ->where('NA', 'N')
                ->get();
        }
        
        // Convert comma-separated IDs to arrays for select2 multiple
        $selectedMataAnggarans = explode(',', $programRektor->MataAnggaranID);
        $selectedPelaksanas = explode(',', $programRektor->PelaksanaID);
        
        if (request()->ajax()) {
            return view('programRektors.edit', compact(
                'programRektor',
                'renstras',
                'pilars',
                'isuStrategis',
                'programPengembangans', 
                'indikatorKinerjas',
                'jenisKegiatans', 
                'mataAnggarans', 
                'satuans', 
                'units', 
                'users',
                'selectedRenstra',
                'selectedPilar',
                'selectedIsu',
                'selectedProgramPengembangan',
                'selectedMataAnggarans',
                'selectedPelaksanas'
            ))->render();
        }
        
        return view('programRektors.edit', compact(
            'programRektor',
            'renstras',
            'pilars',
            'isuStrategis',
            'programPengembangans', 
            'indikatorKinerjas',
            'jenisKegiatans', 
            'mataAnggarans', 
            'satuans', 
            'units', 
            'users',
            'selectedRenstra',
            'selectedPilar',
            'selectedIsu',
            'selectedProgramPengembangan',
            'selectedMataAnggarans',
            'selectedPelaksanas'
        ));
    }

    public function update(Request $request, ProgramRektor $programRektor)
    {
        $request->validate([
            'ProgramPengembanganID' => 'required|exists:program_pengembangans,ProgramPengembanganID',
            'IndikatorKinerjaID' => 'required|exists:indikator_kinerjas,IndikatorKinerjaID',
            'Nama' => 'required|string',
            'Output' => 'required|string',
            'Outcome' => 'required|string',
            'JenisKegiatanID' => 'required|exists:jenis_kegiatans,JenisKegiatanID',
            'MataAnggaranID' => 'required|array',
            'JumlahKegiatan' => 'required|integer',
            'SatuanID' => 'required|exists:satuans,SatuanID',
            'HargaSatuan' => 'required|integer',
            'Total' => 'required|integer',
            'PenanggungJawabID' => 'required',
            'PelaksanaID' => 'required|array',
            'NA' => 'required|in:Y,N',
        ]);

        // Verify that the PenanggungJawabID and PelaksanaID exist in the API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        if (!$response->successful()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengambil data unit dari API: ' . $response->status()], 500);
            }
            return redirect()->route('program-rektors.index')->with('error', 'Gagal mengambil data unit dari API: ' . $response->status());
        }
        
        $units = $response->json();
        $unitIds = array_column($units, 'UnitID');
        
        // Verify PenanggungJawabID exists
        if (!in_array($request->PenanggungJawabID, $unitIds)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Penanggung jawab yang dipilih tidak valid.'], 422);
            }
            return redirect()->back()->withInput()->withErrors(['PenanggungJawabID' => 'Penanggung jawab yang dipilih tidak valid.']);
        }
        
        // Verify all PelaksanaID exist
        foreach ($request->PelaksanaID as $pelaksanaId) {
            if (!in_array($pelaksanaId, $unitIds)) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Salah satu pelaksana yang dipilih tidak valid.'], 422);
                }
                return redirect()->back()->withInput()->withErrors(['PelaksanaID' => 'Salah satu pelaksana yang dipilih tidak valid.']);
            }
        }

        $programRektor->ProgramPengembanganID = $request->ProgramPengembanganID;
        $programRektor->IndikatorKinerjaID = $request->IndikatorKinerjaID;
        $programRektor->Nama = $request->Nama;
        $programRektor->Output = $request->Output;
        $programRektor->Outcome = $request->Outcome;
        $programRektor->JenisKegiatanID = $request->JenisKegiatanID;
        $programRektor->MataAnggaranID = implode(',', $request->MataAnggaranID);
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
