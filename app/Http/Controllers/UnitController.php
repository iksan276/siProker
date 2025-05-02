<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        // Ambil SSO code dari session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Parameter pencarian
        $search = $request->input('search', '');
        $orderBy = $request->input('order_by', 'Nama');
        $sort = $request->input('sort', 'asc');
        $limit = $request->input('limit', 100);
        
        // Hit API untuk mendapatkan data unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("http://localhost:8000/api/units", [
            'order_by' => $orderBy,
            'sort' => $sort,
            'limit' => $limit,
            'search' => $search
        ]);
        
        if (!$response->successful()) {
            return view('units.index', [
                'units' => [],
                'error' => 'Gagal mengambil data dari API: ' . $response->status()
            ]);
        }
        
        $units = $response->json();
        
        // Jika request adalah AJAX, kembalikan hanya data untuk DataTable
        if ($request->ajax() && !$request->wantsJson()) {
            return view('units.index', compact('units'))->render();
        }
        
        // Jika request adalah JSON, kembalikan data JSON
        if ($request->wantsJson()) {
            return response()->json(['data' => $units]);
        }
        
        // Kembalikan view dengan data units
        return view('units.index', compact('units'));
    }

    public function create()
    {
        $users = User::all();
        if (request()->ajax()) {
            return view('units.create', compact('users'))->render();
        }
        return view('units.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);
        
        // Ambil SSO code dari session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk membuat unit baru
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->post("http://localhost:8000/api/units", [
            'Nama' => $request->Nama,
            'NA' => $request->NA,
            'DCreated' => now()->format('Y-m-d H:i:s'),
            'UCreated' => Auth::id()
        ]);
        
        if (!$response->successful()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menambahkan unit: ' . $response->body()
                ], $response->status());
            }
            return redirect()->route('units.index')->with('error', 'Gagal menambahkan unit: ' . $response->body());
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit berhasil ditambahkan']);
        }
        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan');
    }

    public function show($id)
    {
        // Ambil SSO code dari session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan detail unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("http://localhost:8000/api/units/{$id}");
        
        if (!$response->successful()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Unit tidak ditemukan'], 404);
            }
            return redirect()->route('units.index')->with('error', 'Unit tidak ditemukan');
        }
        
        $unit = $response->json();
        
        if (request()->ajax()) {
            return view('units.show', compact('unit'))->render();
        }
        return view('units.show', compact('unit'));
    }

    public function edit($id)
    {
        // Ambil SSO code dari session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk mendapatkan detail unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("http://localhost:8000/api/units/{$id}");
        
        if (!$response->successful()) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Unit tidak ditemukan'], 404);
            }
            return redirect()->route('units.index')->with('error', 'Unit tidak ditemukan');
        }
        
        $unit = $response->json();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('units.edit', compact('unit', 'users'))->render();
        }
        return view('units.edit', compact('unit', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
            'NA' => 'required|in:Y,N',
        ]);
        
        // Ambil SSO code dari session
        $ssoCode = session('sso_code');
        
        if (!$ssoCode) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
        }
        
        // Hit API untuk update unit
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->put("http://localhost:8000/api/units/{$id}", [
            'Nama' => $request->Nama,
            'NA' => $request->NA,
            'DEdited' => now()->format('Y-m-d H:i:s'),
            'UEdited' => Auth::id()
        ]);
        
        if (!$response->successful()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal mengupdate unit: ' . $response->body()
                ], $response->status());
            }
            return redirect()->route('units.index')->with('error', 'Gagal mengupdate unit: ' . $response->body());
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Unit berhasil diupdate']);
        }
        return redirect()->route('units.index')->with('success', 'Unit berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            // Ambil SSO code dari session
            $ssoCode = session('sso_code');
            
            if (!$ssoCode) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Sesi login telah berakhir. Silakan login kembali.'], 401);
                }
                return redirect('/login')->with('error', 'Sesi login telah berakhir. Silakan login kembali.');
            }
            
            // Hit API untuk menghapus unit
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $ssoCode,
            ])->delete("http://localhost:8000/api/units/{$id}");
            
            if (!$response->successful()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Gagal menghapus unit: ' . $response->body()
                    ], $response->status());
                }
                return redirect()->route('units.index')->with('error', 'Gagal menghapus unit: ' . $response->body());
            }
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Unit berhasil dihapus']);
            }
            
            return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus');
        } catch (\Exception $e) {
            // Handle exceptions
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('units.index')
                ->with('error', 'Error occurred: ' . $e->getMessage());
        }
    }
}
