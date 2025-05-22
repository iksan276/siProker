<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;
use App\Models\Kegiatan; // Add this import for the Kegiatan model
use App\Models\SubKegiatan; // Also add this for completeness
use App\Models\RAB; // Also add this for completeness
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getPilarsByRenstra(Request $request)
    {
        $renstraID = $request->renstraID;
        $pilars = Pilar::where('RenstraID', $renstraID)
                       ->where('NA', 'N')
                       ->get();
        return response()->json(['pilars' => $pilars]);
    }
    
    public function getIsusByPilar(Request $request)
    {
        $pilarID = $request->pilarID;
        $isus = IsuStrategis::where('PilarID', $pilarID)
                            ->where('NA', 'N')
                            ->get();
        return response()->json(['isus' => $isus]);
    }
    
    public function getProgramsByIsu(Request $request)
    {
        $isuID = $request->isuID;
        $programs = ProgramPengembangan::where('IsuID', $isuID)
                                       ->where('NA', 'N')
                                       ->get();
        return response()->json(['programs' => $programs]);
    }


    public function getProgramRektor(Request $request)
    {
        $programPengembanganID = $request->programPengembanganID;
        
        $programRektors = ProgramRektor::where('ProgramPengembanganID', $programPengembanganID)
                                ->where('NA', 'N')
                                ->get();
        
        return response()->json(['programRektors' => $programRektors]);
    }

    public function getProgramRektorDetails($id)
    {
        $programRektor = ProgramRektor::with('satuan')->find($id);
        
        if (!$programRektor) {
            return response()->json(['error' => 'Program Rektor not found'], 404);
        }
        
        // Get SSO code from session for API
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            return response()->json(['error' => 'Session expired. Please login again.'], 401);
        }
        
        // Get unit data from API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        $penanggungJawabName = '-';
        
        if ($response->successful()) {
            $units = $response->json();
            
            // Find penanggung jawab name from API data
            foreach ($units as $unit) {
                if (isset($unit['PosisiID']) && $unit['PosisiID'] == $programRektor->PenanggungJawabID) {
                    $penanggungJawabName = $unit['Nama'];
                    break;
                }
            }
        }
        
        return response()->json([
            'jumlahKegiatan' => $programRektor->JumlahKegiatan,
            'satuan' => $programRektor->satuan ? $programRektor->satuan->Nama : '-',
            'hargaSatuan' => $programRektor->HargaSatuan,
            'total' => $programRektor->Total,
            'penanggungJawab' => $penanggungJawabName
        ]);
    }
    

public function getKegiatanDetails($id)
{
    $kegiatan = Kegiatan::with(['programRektor', 'rabs', 'subKegiatans.rabs'])->find($id);
    
    if (!$kegiatan) {
        return response()->json(['error' => 'Kegiatan not found'], 404);
    }
    
    // Use the model's method for consistent calculation
    $totalAnggaran = $kegiatan->getTotalRABAmount();
    
    // Get the Program Rektor total budget (if available)
    $programRektorTotal = $kegiatan->programRektor ? $kegiatan->programRektor->Total : 0;
    
    // Calculate remaining budget
    $sisaAnggaran = $programRektorTotal - $totalAnggaran;
    
    // Format dates for display
    $tanggalMulai = $kegiatan->TanggalMulai ? date('d/m/Y', strtotime($kegiatan->TanggalMulai)) : '-';
    $tanggalSelesai = $kegiatan->TanggalSelesai ? date('d/m/Y', strtotime($kegiatan->TanggalSelesai)) : '-';
    
    return response()->json([
        'nama' => $kegiatan->Nama,
        'tanggalMulai' => $tanggalMulai,
        'tanggalSelesai' => $tanggalSelesai,
        'rincianKegiatan' => $kegiatan->RincianKegiatan,
        'totalAnggaran' => $totalAnggaran,
        'sisaAnggaran' => $sisaAnggaran,
        'programRektorID' => $kegiatan->ProgramRektorID,
        'programRektorNama' => $kegiatan->programRektor ? $kegiatan->programRektor->Nama : '-',
        'programRektorTotal' => $programRektorTotal
    ]);
}

public function getSubKegiatanDetails($id)
{
    $subKegiatan = SubKegiatan::with(['kegiatan.programRektor', 'rabs'])->find($id);
    
    if (!$subKegiatan) {
        return response()->json(['error' => 'Sub Kegiatan not found'], 404);
    }
    
    // Get the parent Kegiatan
    $kegiatan = $subKegiatan->kegiatan;
    
    if (!$kegiatan) {
        return response()->json(['error' => 'Parent Kegiatan not found'], 404);
    }
    
    // Use the model's method for consistent calculation
    $totalAnggaran = $kegiatan->getTotalRABAmount();
    
    // Get the Program Rektor total budget (if available)
    $programRektorTotal = $kegiatan->programRektor ? $kegiatan->programRektor->Total : 0;
    
    // Calculate remaining budget
    $sisaAnggaran = $programRektorTotal - $totalAnggaran;
    
    // Format dates for display
    $jadwalMulai = $subKegiatan->JadwalMulai ? date('d/m/Y', strtotime($subKegiatan->JadwalMulai)) : '-';
    $jadwalSelesai = $subKegiatan->JadwalSelesai ? date('d/m/Y', strtotime($subKegiatan->JadwalSelesai)) : '-';
    
    return response()->json([
        'nama' => $subKegiatan->Nama,
        'jadwalMulai' => $jadwalMulai,
        'jadwalSelesai' => $jadwalSelesai,
        'catatan' => $subKegiatan->Catatan,
        'totalAnggaran' => $totalAnggaran,
        'sisaAnggaran' => $sisaAnggaran,
        'kegiatanID' => $kegiatan->KegiatanID,
        'kegiatanNama' => $kegiatan->Nama,
        'programRektorID' => $kegiatan->ProgramRektorID,
        'programRektorNama' => $kegiatan->programRektor ? $kegiatan->programRektor->Nama : '-',
        'programRektorTotal' => $programRektorTotal
    ]);
}


/**
 * Update status for a Kegiatan
 */
/**
 * Update status for a Kegiatan
 */
/**
 * Update status for a Kegiatan
 */
public function updateKegiatanStatus(Request $request, $id)
{
    // Validate request
    $request->validate([
        'status' => 'required|in:N,Y,T,R,P,PT,YT,TT,RT,TP',
        'feedback' => 'nullable|string',
        'tanggal_pencairan' => 'nullable|date',
        'approve_all_rabs' => 'nullable|string',
    ]);

    try {
        $kegiatan = Kegiatan::findOrFail($id);
        $oldFeedback = $kegiatan->Feedback; // Store old feedback for comparison
        $kegiatan->Status = $request->status;
        
        // Only update feedback if provided
        if ($request->has('feedback')) {
            $kegiatan->Feedback = $request->feedback;
        }
        
        // Update tanggal pencairan if status is TP (Tunda Pencairan) or YT
        if (($request->status === 'TP' || $request->status === 'YT') && $request->has('tanggal_pencairan')) {
            $kegiatan->TanggalPencairan = $request->tanggal_pencairan;
        }
        
        $kegiatan->DEdited = now();
        $kegiatan->UEdited = Auth::id();
        $kegiatan->save();

        // Log the feedback change in the Request table if feedback was changed and not empty
        if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
            $requestLog = new \App\Models\Request();
            $requestLog->KegiatanID = $kegiatan->KegiatanID;
            $requestLog->Feedback = $request->feedback;
            $requestLog->DCreated = now();
            $requestLog->UCreated = Auth::id();
            $requestLog->save();
        }

        // If approve_all_rabs is true, update all RABs directly under this kegiatan
        if ($request->approve_all_rabs == 'true') {
            // Update direct RABs of this kegiatan
            RAB::where('KegiatanID', $kegiatan->KegiatanID)
                ->whereNull('SubKegiatanID')
                ->update([
                    'Status' => $request->status,
                    'DEdited' => now(),
                    'UEdited' => Auth::id()
                ]);
            
            // For subkegiatans, only update their RABs if the kegiatan status is not rejected
            if (!in_array($request->status, ['T', 'TT'])) {
                // Get all subkegiatans that are not rejected
                $subKegiatans = SubKegiatan::where('KegiatanID', $kegiatan->KegiatanID)
                    ->whereNotIn('Status', ['T', 'TT'])
                    ->get();
                
                foreach ($subKegiatans as $subKegiatan) {
                    // Update all RABs of this subkegiatan
                    RAB::where('SubKegiatanID', $subKegiatan->SubKegiatanID)
                        ->update([
                            'Status' => $request->status,
                            'DEdited' => now(),
                            'UEdited' => Auth::id()
                        ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status kegiatan berhasil diupdate'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate status kegiatan: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Update status for a SubKegiatan
 */
public function updateSubKegiatanStatus(Request $request, $id)
{
    // Validate request
    $request->validate([
        'status' => 'required|in:N,Y,T,R',
        'feedback' => 'nullable|string',
        'approve_all_rabs' => 'nullable|string',
    ]);

    try {
        $subKegiatan = SubKegiatan::findOrFail($id);
        $oldFeedback = $subKegiatan->Feedback; // Store old feedback for comparison
        $subKegiatan->Status = $request->status;
        
        // Only update feedback if provided
        if ($request->has('feedback')) {
            $subKegiatan->Feedback = $request->feedback;
        }
        
        $subKegiatan->DEdited = now();
        $subKegiatan->UEdited = Auth::id();
        $subKegiatan->save();

        // Log the feedback change in the Request table if feedback was changed and not empty
        if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
            $requestLog = new \App\Models\Request();
            $requestLog->SubKegiatanID = $subKegiatan->SubKegiatanID;
            $requestLog->Feedback = $request->feedback;
            $requestLog->DCreated = now();
            $requestLog->UCreated = Auth::id();
            $requestLog->save();
        }

        // If approve_all_rabs is true, update all RABs of this subkegiatan
        if ($request->approve_all_rabs == "true") {
            RAB::where('SubKegiatanID', $subKegiatan->SubKegiatanID)
                ->update([
                    'Status' => $request->status,
                    'DEdited' => now(),
                    'UEdited' => Auth::id()
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status sub kegiatan berhasil diupdate'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate status sub kegiatan: ' . $e->getMessage()
        ], 500);
    }
}


/**
 * Update status for a RAB
 */
public function updateRabStatus(Request $request, $id)
{
    // Validate request
    $request->validate([
        'status' => 'required|in:N,Y,T,R',
        'feedback' => 'nullable|string',
    ]);

    try {
        $rab = RAB::findOrFail($id);
        $oldFeedback = $rab->Feedback; // Store old feedback for comparison
        $rab->Status = $request->status;
        
        // Only update feedback if provided
        if ($request->has('feedback')) {
            $rab->Feedback = $request->feedback;
        }
        
        $rab->DEdited = now();
        $rab->UEdited = Auth::id();
        $rab->save();

        // Log the feedback change in the Request table if feedback was changed and not empty
        if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
            $requestLog = new \App\Models\Request();
            $requestLog->RABID = $rab->RABID;
            $requestLog->Feedback = $request->feedback;
            $requestLog->DCreated = now();
            $requestLog->UCreated = Auth::id();
            $requestLog->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status RAB berhasil diupdate'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate status RAB: ' . $e->getMessage()
        ], 500);
    }
}



}
