<?php

namespace App\Http\Controllers;

use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Models\Kegiatan;
use App\Models\ProgramRektor;
use App\Models\SubKegiatan;
use App\Models\RAB;

class PilarController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Base query
        $pilarsQuery = Pilar::with(['renstra', 'createdBy', 'editedBy', 'isuStrategis.programPengembangans.programRektors']);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            $pilarsQuery->where('RenstraID', $request->renstraID);
        }
        
              // Get the filtered results
        $pilars = $pilarsQuery->orderBy('PilarID', 'asc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedRenstra = $request->renstraID;
        $selectedTreeLevel = $request->treeLevel ?? 'pilar';
        $selectedStatus = $request->status;
        
        // If user is not admin, prepare tree grid data
        if (!auth()->user()->isAdmin()) {
            $userId = Auth::id();
            
            if ($request->ajax() && $request->wantsJson()) {
                $treeData = $this->buildTreeData($pilars, $userId, $selectedTreeLevel, $selectedStatus);
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            return view('pilars.user_index', compact('pilars', 'renstras', 'selectedRenstra', 'selectedTreeLevel', 'selectedStatus'));
        }
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax() && $request->wantsJson()) {
            // If format=tree is requested, return tree data even for admin
            if ($request->has('format') && $request->format === 'tree') {
                $treeData = $this->buildTreeData($pilars, Auth::id(), $selectedTreeLevel, $selectedStatus);
                return response()->json([
                    'data' => $treeData
                ]);
            }
            
            // Otherwise return regular datatable format
            $data = [];
            foreach ($pilars as $index => $pilar) {
                // Format the actions HTML
                $actions = '';
                if (auth()->user()->isAdmin()) {
                    $actions = '
                        <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('pilars.show', $pilar->PilarID).'" data-title="Detail Pilar">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('pilars.edit', $pilar->PilarID).'" data-title="Edit Pilar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-square btn-sm delete-pilar" data-id="'.$pilar->PilarID.'">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                }
                
                // Format the NA status
                $naStatus = '';
                if ($pilar->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($pilar->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                $data[] = [
                    'no' => $index + 1,
                    'renstra' => $pilar->renstra->Nama,
                    'nama' => nl2br($pilar->Nama),
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $pilar->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('pilars.index', compact('pilars', 'renstras', 'selectedRenstra'));
    }
    
private function buildTreeData($pilars, $userId, $startLevel = 'pilar', $statusFilter = null)
{
    $treeData = [];
    $rowIndex = 1;
    
    // Get the user's position ID from session
    $apiUserData = session('api_user_data');
    $userPositionId = $apiUserData['Posisi']['ID'] ?? null;
    
    // First, collect all valid program rektors based on user's position ID
    $validProgramRektorIds = [];
    $validProgramPengembanganIds = [];
    $validIsuIds = [];
    $validPilarIds = [];
    
    // First pass: identify valid program rektors and their ancestors
    foreach ($pilars as $pilar) {
        if ($pilar->NA == 'Y') continue; // Skip non-active pilars
        
        $pilarHasValidPrograms = false;
        
        foreach ($pilar->isuStrategis as $isu) {
            if ($isu->NA == 'Y') continue; // Skip non-active isu
            
            $isuHasValidPrograms = false;
            
            foreach ($isu->programPengembangans as $program) {
                if ($program->NA == 'Y') continue; // Skip non-active programs
                
                $programHasValidRektors = false;
                
                foreach ($program->programRektors as $rektor) {
                    if ($rektor->NA == 'Y') continue; // Skip non-active rektor programs
                    
                    // Check if user's position ID is in the PelaksanaID list
                    $pelaksanaIds = explode(',', $rektor->PelaksanaID);
                    if ($userPositionId && in_array($userPositionId, $pelaksanaIds)) {
                        // This is a valid program rektor for this user
                        $validProgramRektorIds[] = $rektor->ProgramRektorID;
                        $validProgramPengembanganIds[] = $program->ProgramPengembanganID;
                        $validIsuIds[] = $isu->IsuID;
                        $validPilarIds[] = $pilar->PilarID;
                        
                        $programHasValidRektors = true;
                        $isuHasValidPrograms = true;
                        $pilarHasValidPrograms = true;
                    }
                }
                
                // Store the flag for this program
                $program->hasValidRektors = $programHasValidRektors;
            }
            
            // Store the flag for this isu
            $isu->hasValidPrograms = $isuHasValidPrograms;
        }
        
        // Store the flag for this pilar
        $pilar->hasValidIsu = $pilarHasValidPrograms;
    }
    
    // Convert arrays to unique sets
    $validProgramRektorIds = array_unique($validProgramRektorIds);
    $validProgramPengembanganIds = array_unique($validProgramPengembanganIds);
    $validIsuIds = array_unique($validIsuIds);
    $validPilarIds = array_unique($validPilarIds);
    
    // Second pass: build the tree with only valid nodes
    foreach ($pilars as $pilar) {
        if ($pilar->NA == 'Y') continue; // Skip non-active pilars
        
        // Skip this pilar if it doesn't have any valid program rektors
        if (!in_array($pilar->PilarID, $validPilarIds)) {
            continue;
        }
        
        // Only add pilar node if startLevel is 'pilar'
        if ($startLevel == 'pilar') {
            $pilarNode = [
                'id' => 'pilar_' . $pilar->PilarID,
                'no' => $rowIndex++,
                'nama' => $pilar->Nama,
                'type' => 'pilar',
                'parent' => null,
                'level' => 0,
                'has_children' => count(array_filter($pilar->isuStrategis->toArray(), function($isu) use ($validIsuIds) {
                    return $isu['NA'] == 'N' && in_array($isu['IsuID'], $validIsuIds);
                })) > 0,
                'actions' => '',
                'row_class' => '',
                'tooltip' => 'Lihat isu strategis'
            ];
            
            $treeData[] = $pilarNode;
        }
        
        // Add Isu Strategis
        foreach ($pilar->isuStrategis as $isu) {
            if ($isu->NA == 'Y') continue; // Skip non-active isu
            
            // Skip this isu if it doesn't have any valid program rektors
            if (!in_array($isu->IsuID, $validIsuIds)) {
                continue;
            }
            
            // Set parent based on startLevel
            $isuParent = $startLevel == 'pilar' ? 'pilar_' . $pilar->PilarID : null;
            $isuLevel = $startLevel == 'pilar' ? 1 : 0;
            
            // Only add isu node if startLevel is 'pilar' or 'isu'
            if ($startLevel == 'pilar' || $startLevel == 'isu') {
                $isuNode = [
                    'id' => 'isu_' . $isu->IsuID,
                    'no' => $startLevel == 'isu' ? $rowIndex++ : '',
                    'nama' => $isu->Nama,
                    'type' => 'isu',
                    'parent' => $isuParent,
                    'level' => $isuLevel,
                    'has_children' => count(array_filter($isu->programPengembangans->toArray(), function($program) use ($validProgramPengembanganIds) {
                        return $program['NA'] == 'N' && in_array($program['ProgramPengembanganID'], $validProgramPengembanganIds);
                    })) > 0,
                    'actions' => '',
                    'row_class' => '',
                    'tooltip' => 'Lihat program pengembangan'
                ];
                
                $treeData[] = $isuNode;
            }
            
            // Add Program Pengembangan
            foreach ($isu->programPengembangans as $program) {
                if ($program->NA == 'Y') continue; // Skip non-active programs
                
                // Skip this program if it doesn't have any valid program rektors
                if (!in_array($program->ProgramPengembanganID, $validProgramPengembanganIds)) {
                    continue;
                }
                
                // Set parent and level based on startLevel
                $programParent = null;
                $programLevel = 0;
                
                if ($startLevel == 'pilar') {
                    $programParent = 'isu_' . $isu->IsuID;
                    $programLevel = 2;
                } else if ($startLevel == 'isu') {
                    $programParent = 'isu_' . $isu->IsuID;
                    $programLevel = 1;
                }
                
                // Only add program node if startLevel is 'pilar', 'isu', or 'program'
                if (in_array($startLevel, ['pilar', 'isu', 'program'])) {
                    $programNode = [
                        'id' => 'program_' . $program->ProgramPengembanganID,
                        'no' => $startLevel == 'program' ? $rowIndex++ : '',
                        'nama' => $program->Nama,
                        'type' => 'program',
                        'parent' => $programParent,
                        'level' => $programLevel,
                        'has_children' => count(array_filter($program->programRektors->toArray(), function($rektor) use ($validProgramRektorIds) {
                            return $rektor['NA'] == 'N' && in_array($rektor['ProgramRektorID'], $validProgramRektorIds);
                        })) > 0,
                        'actions' => '',
                        'row_class' => '',
                        'tooltip' => 'Lihat program rektor'
                    ];
                    
                    $treeData[] = $programNode;
                }
                
                // Add Program Rektor - FILTER BY USER POSITION ID
                foreach ($program->programRektors as $rektor) {
                    if ($rektor->NA == 'Y') continue; // Skip non-active rektor programs
                    
                    // Skip this rektor if it's not in the valid list
                    if (!in_array($rektor->ProgramRektorID, $validProgramRektorIds)) {
                        continue;
                    }
                    
                    // Get kegiatan query with status filter if applicable
                    $kegiatanQuery = Kegiatan::where('ProgramRektorID', $rektor->ProgramRektorID)
                                    ->where('UCreated', $userId);
                    
                    // Apply status filter if we're at kegiatan level and status is provided
                    if ($startLevel == 'kegiatan' && $statusFilter) {
                        $kegiatanQuery->where('Status', $statusFilter);
                    }
                    
                    // Get kegiatan count
                    $kegiatanCount = $kegiatanQuery->count();
                    
                    // Set parent and level based on startLevel
                    $rektorParent = null;
                    $rektorLevel = 0;
                    
                    if ($startLevel == 'pilar') {
                        $rektorParent = 'program_' . $program->ProgramPengembanganID;
                        $rektorLevel = 3;
                    } else if ($startLevel == 'isu') {
                        $rektorParent = 'program_' . $program->ProgramPengembanganID;
                        $rektorLevel = 2;
                    } else if ($startLevel == 'program') {
                        $rektorParent = 'program_' . $program->ProgramPengembanganID;
                        $rektorLevel = 1;
                    }
                    
                    // Only add rektor node if startLevel is 'pilar', 'isu', 'program', or 'rektor'
                    if (in_array($startLevel, ['pilar', 'isu', 'program', 'rektor'])) {
                        $rektorNode = [
                            'id' => 'rektor_' . $rektor->ProgramRektorID,
                            'no' => $startLevel == 'rektor' ? $rowIndex++ : '',
                            'nama' => $rektor->Nama,
                            'type' => 'rektor',
                            'parent' => $rektorParent,
                            'level' => $rektorLevel,
                            'has_children' => $kegiatanCount > 0,
                            'actions' => '
                                <button data-toggle="tooltip" title="Lihat Detail Program Rektor" class="btn btn-info btn-square btn-sm load-modal" 
                                    data-url="' . route('program-rektors.show', $rektor->ProgramRektorID) . '" 
                                    data-title="Detail Program Rektor">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button data-toggle="tooltip" title="Tambah Kegiatan" class="btn btn-primary btn-square btn-sm load-modal" 
                                    data-url="' . route('kegiatans.create') . '?program_rektor=' . $rektor->ProgramRektorID . '" 
                                    data-title="Tambah Kegiatan">
                                    <i class="fas fa-plus"></i>
                                </button>',
                            'row_class' => '',
                            'tooltip' => 'Lihat kegiatan'
                        ];
                        
                        $treeData[] = $rektorNode;
                    }
                    
                    // Add Kegiatan directly under Program Rektor
                    $kegiatanQuery = Kegiatan::where('ProgramRektorID', $rektor->ProgramRektorID)
                        ->where('UCreated', $userId);
                        
                    // Apply status filter if provided
                    if ($statusFilter) {
                        $kegiatanQuery->where('Status', $statusFilter);
                    }
                    
                    $kegiatans = $kegiatanQuery->get();
                        
                    foreach ($kegiatans as $kegiatan) {
                        // The rest of the code for kegiatan, subkegiatan, and RAB remains the same
                        // Set parent and level based on startLevel
                        $kegiatanParent = null;
                        $kegiatanLevel = 0;
                        
                        if ($startLevel == 'pilar') {
                            $kegiatanParent = 'rektor_' . $rektor->ProgramRektorID;
                            $kegiatanLevel = 4;
                        } else if ($startLevel == 'isu') {
                            $kegiatanParent = 'rektor_' . $rektor->ProgramRektorID;
                            $kegiatanLevel = 3;
                        } else if ($startLevel == 'program') {
                            $kegiatanParent = 'rektor_' . $rektor->ProgramRektorID;
                            $kegiatanLevel = 2;
                        } else if ($startLevel == 'rektor') {
                            $kegiatanParent = 'rektor_' . $rektor->ProgramRektorID;
                            $kegiatanLevel = 1;
                        } else if ($startLevel == 'kegiatan') {
                            $kegiatanParent = null;
                            $kegiatanLevel = 0;
                        }
                        
                        $statusBadge = '';
                        if ($kegiatan->Status == 'Y') {
                            $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                        } elseif ($kegiatan->Status == 'T') {
                            $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                        } elseif ($kegiatan->Status == 'R') {
                            $statusBadge = '<span class="badge badge-info">Revisi</span>';
                        } elseif ($kegiatan->Status == 'P') {
                            $statusBadge = '<span class="badge badge-primary">Pengajuan</span>';
                        } elseif ($kegiatan->Status == 'PT') {
                            $statusBadge = '<span class="badge badge-warning">Pengajuan TOR</span>';
                        } elseif ($kegiatan->Status == 'YT') {
                            $statusBadge = '<span class="badge badge-success">Pengajuan TOR Disetujui</span>';
                        } elseif ($kegiatan->Status == 'TT') {
                                                        $statusBadge = '<span class="badge badge-danger">Pengajuan TOR Ditolak</span>';
                        } elseif ($kegiatan->Status == 'RT') {
                            $statusBadge = '<span class="badge badge-info">Pengajuan TOR direvisi</span>';
                        }
                        
                        // Check if kegiatan is approved (Y or YT)
                        $isApproved = in_array($kegiatan->Status, ['Y', 'YT']);
                        
                        $ajukanButton = '';
                        if (!$isApproved && $kegiatan->Status != 'P') {
                            $ajukanButton = '
                                <button data-toggle="tooltip" title="Ajukan Kegiatan" class="btn btn-primary btn-square btn-sm ajukan-kegiatan" 
                                    data-id="' . $kegiatan->KegiatanID . '">
                                    <i class="fas fa-paper-plane"></i>
                                </button>';
                        }
                        
                        // Determine actions based on approval status
                        $kegiatanActions = '';
                        if ($isApproved) {
                            // Only show view button if approved
                            $kegiatanActions = '
                                <button data-toggle="tooltip" title="Lihat Detail Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                    data-url="' . route('kegiatans.show', $kegiatan->KegiatanID) . '" 
                                    data-title="Detail Kegiatan">
                                    <i class="fas fa-eye"></i>
                                </button>';
                        } else {
                            // Show all buttons if not approved
                            $kegiatanActions = $ajukanButton . '
                                <button data-toggle="tooltip" title="Tambah Sub Kegiatan" class="btn btn-primary btn-square btn-sm load-modal" 
                                    data-url="' . route('sub-kegiatans.create') . '?kegiatanID=' . $kegiatan->KegiatanID . '" 
                                    data-title="Tambah Sub Kegiatan">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button data-toggle="tooltip" title="Tambah RAB Kegiatan" class="btn btn-success btn-square btn-sm load-modal" 
                                    data-url="' . route('rabs.create') . '?kegiatanID=' . $kegiatan->KegiatanID . '" 
                                    data-title="Tambah RAB">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button data-toggle="tooltip" title="Lihat Detail Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                    data-url="' . route('kegiatans.show', $kegiatan->KegiatanID) . '" 
                                    data-title="Detail Kegiatan">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button data-toggle="tooltip" title="Edit Kegiatan" class="btn btn-warning btn-square btn-sm load-modal" 
                                    data-url="' . route('kegiatans.edit', $kegiatan->KegiatanID) . '" 
                                    data-title="Edit Kegiatan">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button data-toggle="tooltip" title="Hapus Kegiatan" type="button" class="btn btn-danger btn-square btn-sm delete-kegiatan" 
                                    data-id="' . $kegiatan->KegiatanID . '">
                                    <i class="fas fa-trash"></i>
                                </button>';
                        }
                        
                        $kegiatanNode = [
                            'id' => 'kegiatan_' . $kegiatan->KegiatanID,
                            'no' => $startLevel == 'kegiatan' ? $rowIndex++ : '',
                            'nama' => '<span data-toggle="tooltip" title="Ini adalah Kegiatan">' . $kegiatan->Nama . '</span> ' . '</span> <span data-toggle="tooltip" title="'. $kegiatan->Feedback .'">' . $statusBadge . '</span>',
                            'type' => 'kegiatan',
                            'parent' => $kegiatanParent,
                            'level' => $kegiatanLevel,
                            'has_children' => $kegiatan->subKegiatans->count() > 0 || $kegiatan->rabs->whereNull('SubKegiatanID')->count() > 0,
                            'actions' => $kegiatanActions,
                            'row_class' => '',
                            'tooltip' => 'Tanggal: ' . \Carbon\Carbon::parse($kegiatan->TanggalMulai)->format('d-m-Y') . ' s/d ' . 
                                        \Carbon\Carbon::parse($kegiatan->TanggalSelesai)->format('d-m-Y')
                        ];
                        
                        $treeData[] = $kegiatanNode;
                        
                        // Add Sub Kegiatans
                        foreach ($kegiatan->subKegiatans as $subKegiatan) {
                            $statusBadge = '';
                            if ($subKegiatan->Status == 'N') {
                                $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                            } elseif ($subKegiatan->Status == 'Y') {
                                $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                            } elseif ($subKegiatan->Status == 'T') {
                                $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                            } elseif ($subKegiatan->Status == 'R') {
                                $statusBadge = '<span class="badge badge-info">Revisi</span>';
                            }
                            
                            // Set parent and level based on startLevel
                            $subKegiatanParent = 'kegiatan_' . $kegiatan->KegiatanID;
                            $subKegiatanLevel = $kegiatanLevel + 1;
                            
                            // Determine actions based on parent kegiatan approval status
                            $subKegiatanActions = '';
                            if ($isApproved) {
                                // Only show view button if parent kegiatan is approved
                                $subKegiatanActions = '
                                    <button data-toggle="tooltip" title="Lihat Detail Sub Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                        data-url="' . route('sub-kegiatans.show', $subKegiatan->SubKegiatanID) . '" 
                                        data-title="Detail Sub Kegiatan">
                                        <i class="fas fa-eye"></i>
                                    </button>';
                            } else {
                                // Show all buttons if parent kegiatan is not approved
                                $subKegiatanActions = '
                                    <button data-toggle="tooltip" title="Tambah RAB Sub Kegiatan" class="btn btn-success btn-square btn-sm load-modal" 
                                        data-url="' . route('rabs.create') . '?subKegiatanID=' . $subKegiatan->SubKegiatanID . '" 
                                        data-title="Tambah RAB">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button data-toggle="tooltip" title="Lihat Detail Sub Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                        data-url="' . route('sub-kegiatans.show', $subKegiatan->SubKegiatanID) . '" 
                                        data-title="Detail Sub Kegiatan">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button data-toggle="tooltip" title="Edit Sub Kegiatan" class="btn btn-warning btn-square btn-sm load-modal" 
                                        data-url="' . route('sub-kegiatans.edit', $subKegiatan->SubKegiatanID) . '" 
                                        data-title="Edit Sub Kegiatan">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button data-toggle="tooltip" title="Hapus Sub Kegiatan" type="button" class="btn btn-danger btn-square btn-sm delete-sub-kegiatan" 
                                        data-id="' . $subKegiatan->SubKegiatanID . '">
                                        <i class="fas fa-trash"></i>
                                    </button>';
                            }
                            
                            $subKegiatanNode = [
                                'id' => 'subkegiatan_' . $subKegiatan->SubKegiatanID,
                                'no' => '',
                                'nama' =>'<span data-toggle="tooltip" title="Ini adalah Sub Kegiatan">' . $subKegiatan->Nama . '</span> <span data-toggle="tooltip" title="'. $subKegiatan->Feedback .'">' . $statusBadge . '</span>',
                                'type' => 'subkegiatan',
                                'parent' => $subKegiatanParent,
                                'level' => $subKegiatanLevel,
                                'has_children' => $subKegiatan->rabs->count() > 0,
                                'actions' => $subKegiatanActions,
                                'row_class' => '',
                                'tooltip' => 'Jadwal: ' . \Carbon\Carbon::parse($subKegiatan->JadwalMulai)->format('d-m-Y') . ' s/d ' . 
                                            \Carbon\Carbon::parse($subKegiatan->JadwalSelesai)->format('d-m-Y')
                            ];
                            
                            $treeData[] = $subKegiatanNode;
                            
                            // Add RABs for this Sub Kegiatan
                            foreach ($subKegiatan->rabs as $rab) {
                                $statusBadge = '';
                                if ($rab->Status == 'N') {
                                    $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                                } elseif ($rab->Status == 'Y') {
                                    $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                                } elseif ($rab->Status == 'T') {
                                    $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                                } elseif ($rab->Status == 'R') {
                                    $statusBadge = '<span class="badge badge-info">Revisi</span>';
                                }
                                
                                $total = $rab->Volume * $rab->HargaSatuan;
                                
                                // Determine actions based on parent kegiatan approval status
                                $rabActions = '';
                                if ($isApproved) {
                                    // Only show view button if parent kegiatan is approved
                                    $rabActions = '
                                        <button data-toggle="tooltip" title="Lihat RAB Sub Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                            data-url="' . route('rabs.show', $rab->RABID) . '" 
                                            data-title="Detail RAB">
                                            <i class="fas fa-eye"></i>
                                        </button>';
                                } else {
                                    // Show all buttons if parent kegiatan is not approved
                                    $rabActions = '
                                        <button data-toggle="tooltip" title="Lihat RAB Sub Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                            data-url="' . route('rabs.show', $rab->RABID) . '" 
                                            data-title="Detail RAB">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button data-toggle="tooltip" title="Edit RAB Sub Kegiatan" class="btn btn-warning btn-square btn-sm load-modal" 
                                            data-url="' . route('rabs.edit', $rab->RABID) . '" 
                                            data-title="Edit RAB">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button data-toggle="tooltip" title="Hapus RAB Sub Kegiatan" type="button" class="btn btn-danger btn-square btn-sm delete-rab" 
                                            data-id="' . $rab->RABID . '">
                                            <i class="fas fa-trash"></i>
                                        </button>';
                                }
                                
                                $rabNode = [
                                    'id' => 'rab_sub_' . $rab->RABID,
                                    'no' => '',
                                    'nama' =>'<span data-toggle="tooltip" title="Ini adalah RAB dari Sub Kegiatan">' . $rab->Komponen . '</span> <span data-toggle="tooltip" title="'. $rab->Feedback .'">' . $statusBadge . '</span>',
                                    'type' => 'rab',
                                    'parent' => 'subkegiatan_' . $subKegiatan->SubKegiatanID,
                                    'level' => $subKegiatanLevel + 1,
                                    'has_children' => false,
                                    'actions' => $rabActions,
                                    'row_class' => '',
                                    'tooltip' => 'Volume: ' . number_format($rab->Volume, 0, ',', '.') . ' ' . 
                                                ($rab->satuan ? $rab->satuan->Nama : '-') . ' x Rp ' . 
                                                number_format($rab->HargaSatuan, 0, ',', '.') . ' = Rp ' . 
                                                number_format($total, 0, ',', '.')
                                ];
                                
                                $treeData[] = $rabNode;
                            }
                        }
                        
                        // Add direct RABs for this Kegiatan (if no sub kegiatans)
                        $directRabs = $kegiatan->rabs()->whereNull('SubKegiatanID')->get();
                        
                        foreach ($directRabs as $rab) {
                            $statusBadge = '';
                            if ($rab->Status == 'N') {
                                $statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                            } elseif ($rab->Status == 'Y') {
                                $statusBadge = '<span class="badge badge-success">Disetujui</span>';
                            } elseif ($rab->Status == 'T') {
                                $statusBadge = '<span class="badge badge-danger">Ditolak</span>';
                                                        } elseif ($rab->Status == 'R') {
                                $statusBadge = '<span class="badge badge-info">Revisi</span>';
                            }
                            
                            $total = $rab->Volume * $rab->HargaSatuan;
                            
                            // Determine actions based on parent kegiatan approval status
                            $rabActions = '';
                            if ($isApproved) {
                                // Only show view button if parent kegiatan is approved
                                $rabActions = '
                                    <button data-toggle="tooltip" title="Lihat RAB Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                        data-url="' . route('rabs.show', $rab->RABID) . '" 
                                        data-title="Detail RAB">
                                        <i class="fas fa-eye"></i>
                                    </button>';
                            } else {
                                // Show all buttons if parent kegiatan is not approved
                                $rabActions = '
                                    <button data-toggle="tooltip" title="Lihat RAB Kegiatan" class="btn btn-info btn-square btn-sm load-modal" 
                                        data-url="' . route('rabs.show', $rab->RABID) . '" 
                                        data-title="Detail RAB">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button data-toggle="tooltip" title="Edit RAB Kegiatan" class="btn btn-warning btn-square btn-sm load-modal" 
                                        data-url="' . route('rabs.edit', $rab->RABID) . '" 
                                        data-title="Edit RAB">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button data-toggle="tooltip" title="Hapus RAB Kegiatan" type="button" class="btn btn-danger btn-square btn-sm delete-rab" 
                                        data-id="' . $rab->RABID . '">
                                        <i class="fas fa-trash"></i>
                                    </button>';
                            }
                            
                            $rabNode = [
                                'id' => 'rab_' . $rab->RABID,
                                'no' => '',
                                'nama' =>'<span data-toggle="tooltip" title="Ini adalah RAB dari Kegiatan">' . $rab->Komponen . '</span> <span data-toggle="tooltip" title="'. $rab->Feedback .'">' . $statusBadge . '</span>',
                                'type' => 'rab',
                                'parent' => 'kegiatan_' . $kegiatan->KegiatanID,
                                'level' => $kegiatanLevel + 1,
                                'has_children' => false,
                                'actions' => $rabActions,
                                'row_class' => '',
                                'tooltip' => 'Volume: ' . number_format($rab->Volume, 0, ',', '.') . ' ' . 
                                            ($rab->satuan ? $rab->satuan->Nama : '-') . ' x Rp ' . 
                                            number_format($rab->HargaSatuan, 0, ',', '.') . ' = Rp ' . 
                                            number_format($total, 0, ',', '.')
                            ];
                            
                            $treeData[] = $rabNode;
                        }
                    }
                }
            }
        }
    }
    
    // If we're starting at a specific level, we need to adjust the row numbers
    if ($startLevel != 'pilar') {
        $rowIndex = 1;
        foreach ($treeData as &$node) {
            if ($node['parent'] === null) {
                $node['no'] = $rowIndex++;
            }
        }
    }
    
    return $treeData;
}
    

    public function create()
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $users = User::all();
        
        // Get the selected Renstra from the request
        $selectedRenstra = request('renstraID');
        
        if (request()->ajax()) {
            return view('pilars.create', compact('renstras', 'users', 'selectedRenstra'))->render();
        }
        return view('pilars.create', compact('renstras', 'users', 'selectedRenstra'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'RenstraID' => 'required|exists:renstras,RenstraID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $pilar = new Pilar();
        $pilar->RenstraID = $request->RenstraID;
        $pilar->Nama = $request->Nama;
        $pilar->NA = $request->NA;
        $pilar->DCreated = now();
        $pilar->UCreated = Auth::id();
        $pilar->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pilar berhasil ditambahkan']);
        }
        return redirect()->route('pilars.index')->with('success', 'Pilar berhasil ditambahkan');
    }

    public function show(Pilar $pilar)
    {
        if (request()->ajax()) {
            return view('pilars.show', compact('pilar'))->render();
        }
        return view('pilars.show', compact('pilar'));
    }

    public function edit(Pilar $pilar)
    {
        $renstras = Renstra::where('NA', 'N')->get();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('pilars.edit', compact('pilar', 'renstras', 'users'))->render();
        }
        return view('pilars.edit', compact('pilar', 'renstras', 'users'));
    }

    public function update(Request $request, Pilar $pilar)
    {
        $request->validate([
            'RenstraID' => 'required|exists:renstras,RenstraID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $pilar->RenstraID = $request->RenstraID;
        $pilar->Nama = $request->Nama;
        $pilar->NA = $request->NA;
        $pilar->DEdited = now();
        $pilar->UEdited = Auth::id();
        $pilar->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Pilar berhasil diupdate']);
        }
        return redirect()->route('pilars.index')->with('success', 'Pilar berhasil diupdate');
    }

    public function destroy(Pilar $pilar)
    {
        try {
            $pilar->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Pilar berhasil dihapus']);
            }
            
            return redirect()->route('pilars.index')->with('success', 'Pilar berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus pilar ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('pilars.index')
                    ->with('error', 'Tidak dapat menghapus pilar ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('pilars.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
