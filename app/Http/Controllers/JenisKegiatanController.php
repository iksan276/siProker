<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\QueryException;

class JenisKegiatanController extends Controller
{
    public function index(Request $request)
    {
        // Base query
        $jenisKegiatansQuery = JenisKegiatan::with(['createdBy', 'editedBy']);
        
        // Get the filtered results
        $jenisKegiatans = $jenisKegiatansQuery->orderBy('JenisKegiatanID', 'asc')->get();
        
        // If it's an AJAX request, return JSON data for DataTable
        if ($request->ajax()) {
            $data = [];
            foreach ($jenisKegiatans as $index => $jenisKegiatan) {
                // Format the actions HTML
                $actions = '
                    <button class="btn btn-info btn-square btn-sm load-modal" data-url="'.route('jenis-kegiatans.show', $jenisKegiatan->JenisKegiatanID).'" data-title="Detail Jenis Kegiatan">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-warning btn-square btn-sm load-modal" data-url="'.route('jenis-kegiatans.edit', $jenisKegiatan->JenisKegiatanID).'" data-title="Edit Jenis Kegiatan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-square btn-sm delete-jenis-kegiatan" data-id="'.$jenisKegiatan->JenisKegiatanID.'">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                
                // Format the NA status
                $naStatus = '';
                if ($jenisKegiatan->NA == 'Y') {
                    $naStatus = '<span class="badge badge-danger">Non Aktif</span>';
                } else if ($jenisKegiatan->NA == 'N') {
                    $naStatus = '<span class="badge badge-success">Aktif</span>';
                }
                
                $data[] = [
                    'no' => $index + 1,
                    'nama' => nl2br($jenisKegiatan->Nama),
                    'na' => $naStatus,
                    'actions' => $actions,
                    'row_class' => $jenisKegiatan->NA == 'Y' ? 'bg-light text-muted' : ''
                ];
            }
            
            return response()->json([
                'data' => $data
            ]);
        }
        
        return view('jenisKegiatans.index', compact('jenisKegiatans'));
    }

    public function create()
    {
        $users = User::all();
        
        if (request()->ajax()) {
            return view('jenisKegiatans.create', compact('users'))->render();
        }
        return view('jenisKegiatans.create', compact('users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $jenisKegiatan = new JenisKegiatan();
        $jenisKegiatan->Nama = $request->Nama;
        $jenisKegiatan->NA = $request->NA;
        $jenisKegiatan->DCreated = now();
        $jenisKegiatan->UCreated = Auth::id();
        $jenisKegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jenis Kegiatan berhasil ditambahkan']);
        }
        return redirect()->route('jenis-kegiatans.index')->with('success', 'Jenis Kegiatan berhasil ditambahkan');
    }

    public function show(JenisKegiatan $jenisKegiatan)
    {
        if (request()->ajax()) {
            return view('jenisKegiatans.show', compact('jenisKegiatan'))->render();
        }
        return view('jenisKegiatans.show', compact('jenisKegiatan'));
    }

    public function edit(JenisKegiatan $jenisKegiatan)
    {
        $users = User::all();
        
        if (request()->ajax()) {
            return view('jenisKegiatans.edit', compact('jenisKegiatan', 'users'))->render();
        }
        return view('jenisKegiatans.edit', compact('jenisKegiatan', 'users'));
    }

    public function update(Request $request, JenisKegiatan $jenisKegiatan)
    {
        $request->validate([
            'Nama' => 'required|string',
            'NA' => 'required|in:Y,N',
        ]);

        $jenisKegiatan->Nama = $request->Nama;
        $jenisKegiatan->NA = $request->NA;
        $jenisKegiatan->DEdited = now();
        $jenisKegiatan->UEdited = Auth::id();
        $jenisKegiatan->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Jenis Kegiatan berhasil diupdate']);
        }
        return redirect()->route('jenis-kegiatans.index')->with('success', 'Jenis Kegiatan berhasil diupdate');
    }

    public function destroy(JenisKegiatan $jenisKegiatan)
    {
        try {
            $jenisKegiatan->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Jenis Kegiatan berhasil dihapus']);
            }
            
            return redirect()->route('jenis-kegiatans.index')->with('success', 'Jenis Kegiatan berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus jenis kegiatan ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('jenis-kegiatans.index')
                    ->with('error', 'Tidak dapat menghapus jenis kegiatan ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('jenis-kegiatans.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
