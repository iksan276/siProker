<?php

namespace App\Http\Controllers;

use App\Models\KriteriaAkreditasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class KriteriaAkreditasiController extends Controller
{
    public function index(Request $request)
    {
        $kriteriaAkreditasis = KriteriaAkreditasi::with(['createdBy', 'editedBy'])->orderBy('KriteriaAkreditasiID', 'asc')->get();
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($kriteriaAkreditasis as $index => $kriteria) {
                // NA badge
                $naBadge = '';
                if ($kriteria->NA == 'Y') {
                    $naBadge = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($kriteria->NA == 'N') {
                    $naBadge = '<span class="badge badge-success">Aktif</span>';
                }
                
                // Actions buttons
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('kriteria-akreditasis.show', $kriteria->KriteriaAkreditasiID).'" data-title="Detail Kriteria Akreditasi">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('kriteria-akreditasis.edit', $kriteria->KriteriaAkreditasiID).'" data-title="Edit Kriteria Akreditasi">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-kriteria" data-id="'.$kriteria->KriteriaAkreditasiID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $rowClass = $kriteria->NA == 'Y' ? 'bg-light text-muted' : '';
                
                $data[] = [
                    'DT_RowClass' => $rowClass,
                    'no' => $index + 1,
                    'key' => $kriteria->Key,
                    'nama' => $kriteria->Nama,
                    'na' => $naBadge,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('kriteriaAkreditasis.index', compact('kriteriaAkreditasis'));
    }

    public function create()
    {
        $users = User::all();
        
        if (request()->ajax()) {
            return view('kriteriaAkreditasis.create', compact('users'))->render();
        }
        return view('kriteriaAkreditasis.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Key' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $kriteria = new KriteriaAkreditasi();
        $kriteria->Nama = $request->Nama;
        $kriteria->Key = $request->Key;
        $kriteria->NA = $request->NA;
        $kriteria->DCreated = now();
        $kriteria->UCreated = Auth::id();
        $kriteria->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Kriteria Akreditasi berhasil ditambahkan']);
        }
        return redirect()->route('kriteria-akreditasis.index')->with('success', 'Kriteria Akreditasi berhasil ditambahkan');
    }

    public function show($id)
    {
        $kriteria = KriteriaAkreditasi::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('kriteriaAkreditasis.show', compact('kriteria'))->render();
        }
        return view('kriteriaAkreditasis.show', compact('kriteria'));
    }

    public function edit($id)
    {
        $kriteria = KriteriaAkreditasi::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('kriteriaAkreditasis.edit', compact('kriteria', 'users'))->render();
        }
        return view('kriteriaAkreditasis.edit', compact('kriteria', 'users'));
    }

    public function update(Request $request, $id)
    {
        $kriteria = KriteriaAkreditasi::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Key' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $kriteria->Nama = $request->Nama;
        $kriteria->Key = $request->Key;
        $kriteria->NA = $request->NA;
        $kriteria->DEdited = now();
        $kriteria->UEdited = Auth::id();
        $kriteria->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Kriteria Akreditasi berhasil diupdate']);
        }
        return redirect()->route('kriteria-akreditasis.index')->with('success', 'Kriteria Akreditasi berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $kriteria = KriteriaAkreditasi::findOrFail($id);
            $kriteria->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Kriteria Akreditasi berhasil dihapus']);
            }
            
            return redirect()->route('kriteria-akreditasis.index')->with('success', 'Kriteria Akreditasi berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus Kriteria Akreditasi ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('kriteria-akreditasis.index')
                    ->with('error', 'Tidak dapat menghapus Kriteria Akreditasi ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('kriteria-akreditasis.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
