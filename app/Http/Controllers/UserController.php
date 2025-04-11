<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Codedge\Fpdf\Fpdf\Fpdf;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
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
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('users.index')->with('success', 'User created successfully');
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
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
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
            $pdf->Cell(60,10, $user->name . " - " . $user->email, 0, 1);
        }
        $pdf->Output();
        exit;
    }
}
