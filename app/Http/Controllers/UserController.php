<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    public function __construct()
    {
        // Restrict access to admin only
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
                }
                return redirect()->route('dashboard')->with('error', 'Unauthorized access');
            }
            return $next($request);
        });
    }
    
    public function index()
    {
        $users = User::orderBy('ID', 'asc')->get();
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        if (request()->ajax()) {
            return view('users.create')->render();
        }
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'level' => 'required|in:1,2',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan']);
        }
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        if (request()->ajax()) {
            return view('users.show', compact('user'))->render();
        }
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (request()->ajax()) {
            return view('users.edit', compact('user'))->render();
        }
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'level' => 'required|in:1,2',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->level = $request->level;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User berhasil diupdate']);
        }
        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
            }
            
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus user ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('users.index')
                    ->with('error', 'Tidak dapat menghapus user ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('users.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        // Get all users with any necessary relationships
        $users = User::orderBy('created_at', 'desc')->get();
        
        // Generate a filename with timestamp
        $filename = 'users_' . date('YmdHis') . '.xlsx';
        
        return Excel::download(new UsersExport($users), $filename);
    }

    public function exportPdf()
    {
        $users = User::all();
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(40,10,'User List');
        $pdf->Ln();
        $pdf->SetFont('Arial','',12);
        foreach ($users as $user) {
            $pdf->Cell(60,10, $user->name . " - " . $user->email . " - Level: " . ($user->level == 1 ? 'Admin' : 'User'), 0, 1);
        }
        $pdf->Output();
        exit;
    }
}
