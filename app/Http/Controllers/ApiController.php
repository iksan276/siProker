<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;
use App\Models\IsuStrategis;
use App\Models\ProgramPengembangan;
use App\Models\ProgramRektor;

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

}
