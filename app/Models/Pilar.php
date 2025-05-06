<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilar extends Model
{
    protected $table = 'pilars';
    protected $primaryKey = 'PilarID';
    public $timestamps = false;
    
    protected $fillable = [
        'RenstraID',
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function renstra()
    {
        return $this->belongsTo(Renstra::class, 'RenstraID', 'RenstraID');
    }
    
    public function isuStrategis()
    {
        return $this->hasMany(IsuStrategis::class, 'PilarID', 'PilarID');
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
    
    // Scope for filtering by renstra
    public function scopeByRenstra($query, $renstraId)
    {
        if (!$renstraId) {
            return $query;
        }
        
        return $query->where('RenstraID', $renstraId);
    }
    
    // Get count of isu strategis
    public function getIsuStrategisCountAttribute()
    {
        return $this->isuStrategis()->count();
    }
    
    // Get count of program pengembangan
    public function getProgramPengembanganCountAttribute()
    {
        return ProgramPengembangan::whereHas('isuStrategis', function($query) {
            $query->where('PilarID', $this->PilarID);
        })->count();
    }
    
    // Get count of program rektor
    public function getProgramRektorCountAttribute()
    {
        return ProgramRektor::whereHas('programPengembangan.isuStrategis', function($query) {
            $query->where('PilarID', $this->PilarID);
        })->count();
    }
    
    // Get count of kegiatan
    public function getKegiatanCountAttribute()
    {
        return Kegiatan::whereHas('programRektor.programPengembangan.isuStrategis', function($query) {
            $query->where('PilarID', $this->PilarID);
        })->count();
    }
}
