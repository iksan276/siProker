<?php

namespace App\Http\Controllers;

use App\Models\IsuStrategis;
use App\Models\Pilar;
use App\Models\Renstra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class IsuStrategisController extends Controller
{
    public function index(Request $request)
    {
        // Get all active renstras for the filter
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Get all active pilars for the filter
        $pilars = Pilar::where('NA', 'N')->get();
        
        // Base query
        $isuStrategisQuery = IsuStrategis::with(['pilar.renstra', 'createdBy', 'editedBy']);
        
        // Apply filter if renstraID is provided
        if ($request->has('renstraID') && $request->renstraID) {
            // Filter pilars by renstraID
            $pilarIds = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->pluck('PilarID');
                
            $isuStrategisQuery->whereIn('PilarID', $pilarIds);
            
            // Update pilars list based on selected renstra
            $pilars = Pilar::where('RenstraID', $request->renstraID)
                ->where('NA', 'N')
                ->get();
        }
        
        // Apply filter if pilarID is provided
        if ($request->has('pilarID') && $request->pilarID) {
            $isuStrategisQuery->where('PilarID', $request->pilarID);
        }
        
        // Get the filtered results
        $isuStrategis = $isuStrategisQuery->orderBy('IsuID', 'asc')->get();
        
        // Get the selected filter values (for re-populating the selects)
        $selectedRenstra = $request->renstraID;
        $selectedPilar = $request->pilarID;
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax() && $request->wantsJson()) {
            $data = [];
            foreach ($isuStrategis as $index => $isu) {
                // Format the actions HTML
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('isu-strategis.show', $isu->IsuID).'" data-title="Detail Isu Strategis">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('isu-strategis.edit', $isu->IsuID).'" data-title="Edit Isu Strategis">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-isu" data-id="'.$isu->IsuID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                // Format the NA status
                $naStatus = '';
                if ($isu->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($isu->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                $data[] = [
                    'no' => $index + 1,
                    'pilar' => nl2br($isu->pilar->Nama),
                    'nama' => nl2br($isu->Nama),
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $isu->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('isuStrategis.index', compact('isuStrategis', 'renstras', 'pilars', 'selectedRenstra', 'selectedPilar'));
    }

    public function create()
{
    $renstras = Renstra::where('NA', 'N')->get();
    $pilars = Pilar::where('NA', 'N')->get();
    $users = User::all();
    
    // Get the selected filters from the request
    $selectedRenstra = request('renstraID');
    $selectedPilar = request('pilarID');
    
    // If renstra is selected, filter pilars
    if ($selectedRenstra) {
        $pilars = Pilar::where('RenstraID', $selectedRenstra)
            ->where('NA', 'N')
            ->get();
    }
    
    if (request()->ajax()) {
        return view('isuStrategis.create', compact('pilars', 'renstras', 'users', 'selectedRenstra', 'selectedPilar'))->render();
    }
    return view('isuStrategis.create', compact('pilars', 'renstras', 'users', 'selectedRenstra', 'selectedPilar'));
}

    

    public function store(Request $request)
    {
        $request->validate([
            'PilarID' => 'required|exists:pilars,PilarID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $isuStrategis = new IsuStrategis();
        $isuStrategis->PilarID = $request->PilarID;
        $isuStrategis->Nama = $request->Nama;
        $isuStrategis->NA = $request->NA;
        $isuStrategis->DCreated = now();
        $isuStrategis->UCreated = Auth::id();
        $isuStrategis->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Isu Strategis berhasil ditambahkan']);
        }
        return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis berhasil ditambahkan');
    }

    public function show($id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        
        if (request()->ajax()) {
            return view('isuStrategis.show', compact('isuStrategis'))->render();
        }
        return view('isuStrategis.show', compact('isuStrategis'));
    }

    public function edit($id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        $renstras = Renstra::where('NA', 'N')->get();
        
        // Get the renstra ID from the pilar
        $renstraID = $isuStrategis->pilar->RenstraID;
        
        // Filter pilars by the renstra
        $pilars = Pilar::where('RenstraID', $renstraID)
            ->where('NA', 'N')
            ->get();
            
        $users = User::all();
        
        if (request()->ajax()) {
            return view('isuStrategis.edit', compact('isuStrategis', 'pilars', 'renstras', 'users', 'renstraID'))->render();
        }
        return view('isuStrategis.edit', compact('isuStrategis', 'pilars', 'renstras', 'users', 'renstraID'));
    }

    public function update(Request $request, $id)
    {
        $isuStrategis = IsuStrategis::findOrFail($id);
        
        $request->validate([
            'PilarID' => 'required|exists:pilars,PilarID',
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $isuStrategis->PilarID = $request->PilarID;
        $isuStrategis->Nama = $request->Nama;
        $isuStrategis->NA = $request->NA;
        $isuStrategis->DEdited = now();
        $isuStrategis->UEdited = Auth::id();
        $isuStrategis->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Isu Strategis berhasil diupdate']);
        }
        return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $isuStrategis = IsuStrategis::findOrFail($id);
            $isuStrategis->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Isu Strategis berhasil dihapus']);
            }
            
            return redirect()->route('isu-strategis.index')->with('success', 'Isu Strategis berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus  Isu Strategis ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('isu-strategis.index')
                    ->with('error', 'Tidak dapat menghapus  Isu Strategis ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('isu-strategis.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
    

}
