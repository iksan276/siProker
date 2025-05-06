<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IsuStrategis extends Model
{
    protected $table = 'isu_strategis';
    protected $primaryKey = 'IsuID';
    public $timestamps = false;
    
    protected $fillable = [
        'PilarID',
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
        return 'IsuID';
    }
    
    public function pilar()
    {
        return $this->belongsTo(Pilar::class, 'PilarID', 'PilarID');
    }
    
    public function programPengembangans()
    {
        return $this->hasMany(ProgramPengembangan::class, 'IsuID', 'IsuID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
    
    // Scope for filtering by pilar
    public function scopeByPilar($query, $pilarId)
    {
        if (!$pilarId) {
            return $query;
        }
        
        return $query->where('PilarID', $pilarId);
    }
    
    // Scope for filtering by renstra through pilar
    public function scopeByRenstra($query, $renstraId)
    {
        if (!$renstraId) {
            return $query;
        }
        
        return $query->whereHas('pilar', function($q) use ($renstraId) {
            $q->where('RenstraID', $renstraId);
        });
    }
}
