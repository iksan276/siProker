<?php

namespace App\Http\Controllers;

use App\Models\IKUPT;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class IKUPTController extends Controller
{
    public function index(Request $request)
    {
        $ikupts = IKUPT::with(['createdBy', 'editedBy'])->orderBy('IKUPTID', 'asc')->get();
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($ikupts as $index => $ikupt) {
                // NA badge
                $naBadge = '';
                if ($ikupt->NA == 'Y') {
                    $naBadge = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($ikupt->NA == 'N') {
                    $naBadge = '<span class="badge badge-success">Aktif</span>';
                }
                
                // Actions buttons
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('ikupts.show', $ikupt->IKUPTID).'" data-title="Detail IKU PT">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('ikupts.edit', $ikupt->IKUPTID).'" data-title="Edit IKU PT">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-ikupt" data-id="'.$ikupt->IKUPTID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                $rowClass = $ikupt->NA == 'Y' ? 'bg-light text-muted' : '';
                
                $data[] = [
                    'DT_RowClass' => $rowClass,
                    'no' => $index + 1,
                    'key' => $ikupt->Key,
                    'nama' => $ikupt->Nama,
                    'na' => $naBadge,
                    'actions' => $actions
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('ikupts.index', compact('ikupts'));
    }

    public function create()
    {
        $users = User::all();
        
        if (request()->ajax()) {
            return view('ikupts.create', compact('users'))->render();
        }
        return view('ikupts.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Key' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $ikupt = new IKUPT();
        $ikupt->Nama = $request->Nama;
        $ikupt->Key = $request->Key;
        $ikupt->NA = $request->NA;
        $ikupt->DCreated = now();
        $ikupt->UCreated = Auth::id();
        $ikupt->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'IKU PT berhasil ditambahkan']);
        }
        return redirect()->route('ikupts.index')->with('success', 'IKU PT berhasil ditambahkan');
    }

    public function show($id)
    {
        $ikupt = IKUPT::with(['createdBy', 'editedBy'])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('ikupts.show', compact('ikupt'))->render();
        }
        return view('ikupts.show', compact('ikupt'));
    }

    public function edit($id)
    {
        $ikupt = IKUPT::findOrFail($id);
        $users = User::all();
        
        if (request()->ajax()) {
            return view('ikupts.edit', compact('ikupt', 'users'))->render();
        }
        return view('ikupts.edit', compact('ikupt', 'users'));
    }

    public function update(Request $request, $id)
    {
        $ikupt = IKUPT::findOrFail($id);
        
        $request->validate([
            'Nama' => 'required|string|max:255',
            'Key' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);

        $ikupt->Nama = $request->Nama;
        $ikupt->Key = $request->Key;
        $ikupt->NA = $request->NA;
        $ikupt->DEdited = now();
        $ikupt->UEdited = Auth::id();
        $ikupt->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'IKU PT berhasil diupdate']);
        }
        return redirect()->route('ikupts.index')->with('success', 'IKU PT berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $ikupt = IKUPT::findOrFail($id);
            $ikupt->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'IKU PT berhasil dihapus']);
            }
            
            return redirect()->route('ikupts.index')->with('success', 'IKU PT berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus IKU PT ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('ikupts.index')
                    ->with('error', 'Tidak dapat menghapus IKU PT ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('ikupts.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
