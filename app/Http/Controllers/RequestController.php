<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\RAB;
use App\Models\User;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(HttpRequest $httpRequest)
    {
        // Base query with relationships
        $requestsQuery = Request::with([
            'kegiatan',
            'subKegiatan',
            'rab',
            'createdBy',
            'editedBy'
        ]);
        
        // Apply filters if provided
        if ($httpRequest->has('entity_type')) {
            switch ($httpRequest->entity_type) {
                case 'Kegiatan':
                    $requestsQuery->whereNotNull('KegiatanID')
                                 ->whereNull('SubKegiatanID')
                                 ->whereNull('RABID');
                    break;
                case 'SubKegiatan':
                    $requestsQuery->whereNotNull('SubKegiatanID')
                                 ->whereNull('RABID');
                    break;
                case 'RAB':
                    $requestsQuery->whereNotNull('RABID');
                    break;
            }
        }
        
        if ($httpRequest->has('kegiatan_id') && $httpRequest->kegiatan_id) {
            $requestsQuery->where('KegiatanID', $httpRequest->kegiatan_id);
        }
        
        if ($httpRequest->has('subkegiatan_id') && $httpRequest->subkegiatan_id) {
            $requestsQuery->where('SubKegiatanID', $httpRequest->subkegiatan_id);
        }
        
        if ($httpRequest->has('rab_id') && $httpRequest->rab_id) {
            $requestsQuery->where('RABID', $httpRequest->rab_id);
        }
        
        if ($httpRequest->has('user_id') && $httpRequest->user_id) {
            $requestsQuery->where(function($query) use ($httpRequest) {
                $query->where('UCreated', $httpRequest->user_id)
                      ->orWhere('UEdited', $httpRequest->user_id);
            });
        }
        
        // Get the filtered results
        $requests = $requestsQuery->orderBy('DCreated', 'desc')->paginate(15);
        
        // Get users for the filter dropdown
        $users = User::all();
        
        // Get entity IDs for the filter dropdowns
        $kegiatanIds = Kegiatan::pluck('KegiatanID', 'Nama');
        $subKegiatanIds = SubKegiatan::pluck('SubKegiatanID', 'Nama');
        $rabIds = RAB::pluck('RABID', 'Komponen');
        
        return view('requests.index', compact(
            'requests',
            'users',
            'kegiatanIds',
            'subKegiatanIds',
            'rabIds'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kegiatans = Kegiatan::all();
        $subKegiatans = SubKegiatan::all();
        $rabs = RAB::all();
        $users = User::all();
        
        return view('requests.create', compact(
            'kegiatans',
            'subKegiatans',
            'rabs',
            'users'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HttpRequest $httpRequest)
    {
        $httpRequest->validate([
            'entity_type' => 'required|in:Kegiatan,SubKegiatan,RAB',
            'entity_id' => 'required|integer',
            'Feedback' => 'required|string',
        ]);
        
        try {
            $request = new Request();
            
            // Set the appropriate entity ID based on the entity type
            switch ($httpRequest->entity_type) {
                case 'Kegiatan':
                    $request->KegiatanID = $httpRequest->entity_id;
                    break;
                case 'SubKegiatan':
                    $request->SubKegiatanID = $httpRequest->entity_id;
                    $subKegiatan = SubKegiatan::find($httpRequest->entity_id);
                    if ($subKegiatan) {
                        $request->KegiatanID = $subKegiatan->KegiatanID;
                    }
                    break;
                case 'RAB':
                    $request->RABID = $httpRequest->entity_id;
                    $rab = RAB::find($httpRequest->entity_id);
                    if ($rab) {
                        $request->KegiatanID = $rab->KegiatanID;
                        $request->SubKegiatanID = $rab->SubKegiatanID;
                    }
                    break;
            }
            
            $request->Feedback = $httpRequest->Feedback;
            $request->DCreated = now();
            $request->UCreated = Auth::id();
            $request->save();
            
            if ($httpRequest->ajax()) {
                return response()->json(['success' => true, 'message' => 'Request berhasil ditambahkan']);
            }
            
            return redirect()->route('requests.index')->with('success', 'Request berhasil ditambahkan');
        } catch (\Exception $e) {
            if ($httpRequest->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $request = Request::with([
            'kegiatan',
            'subKegiatan',
            'rab',
            'createdBy',
            'editedBy'
        ])->findOrFail($id);
        
        if (request()->ajax()) {
            return view('requests.show', compact('request'))->render();
        }
        
        return view('requests.show', compact('request'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $request = Request::findOrFail($id);
        $kegiatans = Kegiatan::all();
        $subKegiatans = SubKegiatan::all();
        $rabs = RAB::all();
        $users = User::all();
        
        if (request()->ajax()) {
            return view('requests.edit', compact(
                'request',
                'kegiatans',
                'subKegiatans',
                'rabs',
                'users'
            ))->render();
        }
        
        return view('requests.edit', compact(
            'request',
            'kegiatans',
            'subKegiatans',
            'rabs',
            'users'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HttpRequest $httpRequest, string $id)
    {
        $httpRequest->validate([
            'entity_type' => 'required|in:Kegiatan,SubKegiatan,RAB',
            'entity_id' => 'required|integer',
            'Feedback' => 'required|string',
        ]);
        
        try {
            $request = Request::findOrFail($id);
            
            // Reset all entity IDs
            $request->KegiatanID = null;
            $request->SubKegiatanID = null;
            $request->RABID = null;
            
            // Set the appropriate entity ID based on the entity type
            switch ($httpRequest->entity_type) {
                case 'Kegiatan':
                    $request->KegiatanID = $httpRequest->entity_id;
                    break;
                case 'SubKegiatan':
                    $request->SubKegiatanID = $httpRequest->entity_id;
                    $subKegiatan = SubKegiatan::find($httpRequest->entity_id);
                    if ($subKegiatan) {
                        $request->KegiatanID = $subKegiatan->KegiatanID;
                    }
                    break;
                case 'RAB':
                    $request->RABID = $httpRequest->entity_id;
                    $rab = RAB::find($httpRequest->entity_id);
                    if ($rab) {
                        $request->KegiatanID = $rab->KegiatanID;
                        $request->SubKegiatanID = $rab->SubKegiatanID;
                    }
                    break;
            }
            
            $request->Feedback = $httpRequest->Feedback;
            $request->DEdited = now();
            $request->UEdited = Auth::id();
            $request->save();
            
            if ($httpRequest->ajax()) {
                return response()->json(['success' => true, 'message' => 'Request berhasil diupdate']);
            }
            
            return redirect()->route('requests.index')->with('success', 'Request berhasil diupdate');
        } catch (\Exception $e) {
            if ($httpRequest->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $request = Request::findOrFail($id);
            $request->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Request berhasil dihapus']);
            }
            
            return redirect()->route('requests.index')->with('success', 'Request berhasil dihapus');
        } catch (QueryException $e) {
            // Check if it's a foreign key constraint error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Tidak dapat menghapus request ini karena dirujuk oleh baris di table lain.'
                    ], 422);
                }
                
                return redirect()->route('requests.index')
                    ->with('error', 'Tidak dapat menghapus request ini karena dirujuk oleh baris di table lain.');
            }
            
            // For other database errors
            if (request()->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Database error occurred: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('requests.index')
                ->with('error', 'Database error occurred: ' . $e->getMessage());
        }
    }
}
