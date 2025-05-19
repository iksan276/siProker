<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;
use App\Models\Kegiatan; // Add this import for the Kegiatan model
use App\Models\SubKegiatan; // Also add this for completeness
use Illuminate\Support\Facades\Http; 

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


public function updateKegiatanStatus(Request $request, $id)
{
    $kegiatan = Kegiatan::find($id);
    
    if (!$kegiatan) {
        return response()->json(['success' => false, 'message' => 'Kegiatan not found'], 404);
    }
    
    // Update the status to 'P' (Pengajuan)
    $kegiatan->Status = 'P';
    $kegiatan->save();
    
    return response()->json([
        'success' => true, 
        'message' => 'Status kegiatan berhasil diubah menjadi Pengajuan',
        'status' => $kegiatan->Status
    ]);
}

}
