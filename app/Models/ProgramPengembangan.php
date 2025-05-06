<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPengembangan extends Model
{
    protected $table = 'program_pengembangans';
    protected $primaryKey = 'ProgramPengembanganID';
    public $timestamps = false;
    
    protected $fillable = [
        'IsuID',
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function isuStrategis()
    {
        return $this->belongsTo(IsuStrategis::class, 'IsuID', 'IsuID');
    }
    
    public function programRektors()
    {
        return $this->hasMany(ProgramRektor::class, 'ProgramPengembanganID', 'ProgramPengembanganID');
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
    
    // Scope for filtering by isu strategis
    public function scopeByIsuStrategis($query, $isuId)
    {
        if (!$isuId) {
            return $query;
        }
        
        return $query->where('IsuID', $isuId);
    }
    
    // Scope for filtering by pilar through isu strategis
    public function scopeByPilar($query, $pilarId)
    {
        if (!$pilarId) {
            return $query;
        }
        
        return $query->whereHas('isuStrategis', function($q) use ($pilarId) {
            $q->where('PilarID', $pilarId);
        });
    }
    
    // Scope for filtering by renstra through isu strategis -> pilar
    public function scopeByRenstra($query, $renstraId)
    {
        if (!$renstraId) {
            return $query;
        }
        
        return $query->whereHas('isuStrategis.pilar', function($q) use ($renstraId) {
            $q->where('RenstraID', $renstraId);
        });
    }
}
