<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pilar;

class ApiController extends Controller
{
    public function getPilarsByRenstra(Request $request)
    {
        $renstraID = $request->renstraID;
        $pilars = Pilar::where('RenstraID', $renstraID)->get();
        return response()->json(['pilars' => $pilars]);
    }
}
