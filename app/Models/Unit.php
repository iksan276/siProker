<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    protected $primaryKey = 'UnitID';
    public $timestamps = false;
    
    protected $fillable = [
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'UnitID';
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    public function indikatorKinerjas()
    {
        return $this->hasMany(IndikatorKinerja::class, 'UnitTerkaitID', 'UnitID');
    }
    
    /**
     * Get all units from the API
     * 
     * @param string|null $ssoCode
     * @return array
     */
    public static function getFromApi($ssoCode = null)
    {
        if (!$ssoCode) {
            $ssoCode = session('sso_code');
        }
        
        if (!$ssoCode) {
            return [];
        }
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units", [
            'order_by' => 'Nama',
            'sort' => 'asc',
            'limit' => 100
        ]);
        
        if (!$response->successful()) {
            return [];
        }
        
        return $response->json();
    }
    
    /**
     * Get a specific unit from the API by ID
     * 
     * @param int $unitId
     * @param string|null $ssoCode
     * @return array|null
     */
    public static function getByIdFromApi($unitId, $ssoCode = null)
    {
        if (!$ssoCode) {
            $ssoCode = session('sso_code');
        }
        
        if (!$ssoCode) {
            return null;
        }
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $ssoCode,
        ])->get("https://webhook.itp.ac.id/api/units/{$unitId}");
        
        if (!$response->successful()) {
            return null;
        }
        
        return $response->json();
    }
}
