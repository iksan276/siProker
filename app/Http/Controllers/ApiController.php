<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\RAB;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
        
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            return response()->json(['error' => 'Session expired. Please login again.'], 401);
        }
        
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
        
        $totalAnggaran = $kegiatan->getTotalRABAmount();
        $programRektorTotal = $kegiatan->programRektor ? $kegiatan->programRektor->Total : 0;
        $sisaAnggaran = $programRektorTotal - $totalAnggaran;
        
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
        
        $kegiatan = $subKegiatan->kegiatan;
        
        if (!$kegiatan) {
            return response()->json(['error' => 'Parent Kegiatan not found'], 404);
        }
        
        $totalAnggaran = $kegiatan->getTotalRABAmount();
        $programRektorTotal = $kegiatan->programRektor ? $kegiatan->programRektor->Total : 0;
        $sisaAnggaran = $programRektorTotal - $totalAnggaran;
        
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

public function updateKegiatanStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:N,Y,T,R,P,PT,YT,TT,RT,TP',
        'feedback' => 'nullable|string',
        'tanggal_pencairan' => 'nullable|date',
        'approve_all_rabs' => 'nullable|string',
    ]);

    try {
        $kegiatan = Kegiatan::findOrFail($id);
        $oldStatus = $kegiatan->Status;
        $oldFeedback = $kegiatan->Feedback;
        
        Log::info("Updating kegiatan status", [
            'kegiatan_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'user_id' => Auth::id()
        ]);
        
        $kegiatan->Status = $request->status;
        
        if ($request->has('feedback')) {
            $kegiatan->Feedback = $request->feedback;
        }
        
        if (($request->status === 'TP' || $request->status === 'YT') && $request->has('tanggal_pencairan')) {
            $kegiatan->TanggalPencairan = $request->tanggal_pencairan;
        }
        
        $kegiatan->DEdited = now();
        $kegiatan->UEdited = Auth::id();
        $kegiatan->save();

        if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
            $requestLog = new \App\Models\Request();
            $requestLog->KegiatanID = $kegiatan->KegiatanID;
            $requestLog->Feedback = $request->feedback;
            $requestLog->DCreated = now();
            $requestLog->UCreated = Auth::id();
            $requestLog->save();
        }

        if (!in_array($request->status, ['T', 'TT'])) {
            SubKegiatan::where('KegiatanID', $kegiatan->KegiatanID)
                ->whereNotIn('Status', ['T', 'TT'])
                ->update([
                    'Status' => $request->status,
                    'DEdited' => now(),
                    'UEdited' => Auth::id()
                ]);
        }

        if ($request->approve_all_rabs == 'true') {
            RAB::where('KegiatanID', $kegiatan->KegiatanID)
                ->whereNull('SubKegiatanID')
                ->update([
                    'Status' => $request->status,
                    'DEdited' => now(),
                    'UEdited' => Auth::id()
                ]);
            
            if (!in_array($request->status, ['T', 'TT'])) {
                $subKegiatans = SubKegiatan::where('KegiatanID', $kegiatan->KegiatanID)
                    ->whereNotIn('Status', ['T', 'TT'])
                    ->get();
                
                foreach ($subKegiatans as $subKegiatan) {
                    RAB::where('SubKegiatanID', $subKegiatan->SubKegiatanID)
                        ->update([
                            'Status' => $request->status,
                            'DEdited' => now(),
                            'UEdited' => Auth::id()
                        ]);
                }
            }
        }

        // Send notification when status changes
        if ($oldStatus !== $request->status) {
            try {
                $currentUser = Auth::user();
                
                // Check if current user is admin or super user updating a regular user's kegiatan
                if (in_array($currentUser->level, [1, 3])) {
                    Log::info("Admin/Super user updating kegiatan status", [
                        'kegiatan_id' => $kegiatan->KegiatanID,
                        'kegiatan_name' => $kegiatan->Nama,
                        'old_status' => $oldStatus,
                        'new_status' => $request->status,
                        'admin_id' => $currentUser->id,
                        'admin_name' => $currentUser->name
                    ]);
                    
                    $this->notificationService->sendKegiatanNotification($kegiatan, 'status_updated');
                }
                
                // Original notification logic for user submissions
                if ($request->status === 'P') {
                    Log::info("Sending notification for kegiatan submission", [
                        'kegiatan_id' => $kegiatan->KegiatanID,
                        'kegiatan_name' => $kegiatan->Nama,
                        'sender_id' => Auth::id(),
                        'sender_name' => Auth::user()->name
                    ]);
                    $this->notificationService->sendKegiatanNotification($kegiatan, 'ajukan_kegiatan');
                } elseif ($request->status === 'PT') {
                    Log::info("Sending notification for TOR submission", [
                        'kegiatan_id' => $kegiatan->KegiatanID,
                        'kegiatan_name' => $kegiatan->Nama,
                        'sender_id' => Auth::id(),
                        'sender_name' => Auth::user()->name
                    ]);
                    $this->notificationService->sendKegiatanNotification($kegiatan, 'ajukan_tor');
                }
            } catch (\Exception $e) {
                Log::error('Failed to send notification', [
                    'kegiatan_id' => $kegiatan->KegiatanID,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the main operation if notification fails
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status kegiatan berhasil diupdate'
        ]);
    } catch (\Exception $e) {
        Log::error('Error updating kegiatan status', [
            'kegiatan_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupdate status kegiatan: ' . $e->getMessage()
        ], 500);
    }
}



    public function updateSubKegiatanStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:N,Y,T,R',
            'feedback' => 'nullable|string',
            'approve_all_rabs' => 'nullable|string',
        ]);

        try {
            $subKegiatan = SubKegiatan::findOrFail($id);
            $oldFeedback = $subKegiatan->Feedback;
            $subKegiatan->Status = $request->status;
            
            if ($request->has('feedback')) {
                $subKegiatan->Feedback = $request->feedback;
            }
            
            $subKegiatan->DEdited = now();
            $subKegiatan->UEdited = Auth::id();
            $subKegiatan->save();

            if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
                $requestLog = new \App\Models\Request();
                $requestLog->SubKegiatanID = $subKegiatan->SubKegiatanID;
                $requestLog->Feedback = $request->feedback;
                $requestLog->DCreated = now();
                $requestLog->UCreated = Auth::id();
                $requestLog->save();
            }

          
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
            Log::error('Error updating sub kegiatan status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status sub kegiatan: ' . $e->getMessage()
            ], 500);
        }
    }

       public function updateRabStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:N,Y,T,R',
            'feedback' => 'nullable|string',
        ]);

        try {
            $rab = RAB::with(['kegiatan', 'subKegiatan'])->findOrFail($id);
            $oldStatus = $rab->Status;
            $rab->Status = $request->status;
            $oldFeedback = $rab->Feedback;
            
            if ($request->has('feedback')) {
                $rab->Feedback = $request->feedback;
            }
            
            $rab->DEdited = now();
            $rab->UEdited = Auth::id();
            $rab->save();

            if ($request->has('feedback') && $oldFeedback !== $request->feedback && !empty($request->feedback)) {
                $requestLog = new \App\Models\Request();
                $requestLog->RABID = $rab->RABID;
                $requestLog->Feedback = $request->feedback;
                $requestLog->DCreated = now();
                $requestLog->UCreated = Auth::id();
                $requestLog->save();
            }

            // Send notification when status changes and current user is admin/super user
            if ($oldStatus !== $request->status) {
                try {
                    $currentUser = Auth::user();
                    
                    // Check if current user is admin or super user updating a regular user's RAB
                    if (in_array($currentUser->level, [1, 3])) {
                        Log::info("Admin/Super user updating RAB status", [
                            'rab_id' => $rab->RABID,
                            'rab_komponen' => $rab->Komponen,
                            'old_status' => $oldStatus,
                            'new_status' => $request->status,
                            'admin_id' => $currentUser->id,
                            'admin_name' => $currentUser->name
                        ]);
                        
                        $this->notificationService->sendRabNotification($rab, 'status_updated');
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send RAB notification', [
                        'rab_id' => $rab->RABID,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Don't fail the main operation if notification fails
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status RAB berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating RAB status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status RAB: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getSubKegiatansByKegiatan(Request $request)
    {
        $kegiatanID = $request->kegiatanID;
        $subKegiatans = SubKegiatan::where('KegiatanID', $kegiatanID)
                                   ->where('NA', 'N')
                                   ->get();
        return response()->json(['subKegiatans' => $subKegiatans]);
    }
}
