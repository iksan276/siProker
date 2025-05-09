<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    protected $table = 'indikator_kinerjas';
    protected $primaryKey = 'IndikatorKinerjaID';
    public $timestamps = false;
    
    protected $fillable = [
        'SatuanID',
        'Nama',
        'Baseline',
        'Tahun1',
        'Tahun2',
        'Tahun3',
        'Tahun4',
        'Tahun5',
        'MendukungIKU',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'IndikatorKinerjaID';
    }
    
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'SatuanID', 'SatuanID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'IndikatorKinerjaID', 'IndikatorKinerjaID');
    }
    
    public function programRektors()
    {
        return $this->hasMany(ProgramRektor::class, 'IndikatorKinerjaID', 'IndikatorKinerjaID');
    }
    
    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
    
    // Scope for filtering by year
    public function scopeForYear($query, $year)
    {
        // This would depend on how years are stored in your model
        // Assuming Tahun1, Tahun2, etc. correspond to specific years
        return $query;
    }
    
    // Scope for filtering by program pengembangan
    public function scopeByProgramPengembangan($query, $programPengembanganId)
    {
        if (!$programPengembanganId) {
            return $query;
        }
        
        return $query->whereHas('programRektors', function($q) use ($programPengembanganId) {
            $q->where('ProgramPengembanganID', $programPengembanganId);
        });
    }
}
