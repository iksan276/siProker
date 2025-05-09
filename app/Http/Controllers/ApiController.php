<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;
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
    


}
