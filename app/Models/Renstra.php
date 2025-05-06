<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renstra extends Model
{
    protected $table = 'renstras';
    protected $primaryKey = 'RenstraID';
    public $timestamps = false;
    
    protected $fillable = [
        'Nama',
        'PeriodeMulai',
        'PeriodeSelesai',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function pilars()
    {
        return $this->hasMany(Pilar::class, 'RenstraID', 'RenstraID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Scope untuk mendapatkan renstra aktif
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
    
    // Get count of pilars
    public function getPilarCountAttribute()
    {
        return $this->pilars()->count();
    }
    
    // Get count of isu strategis
    public function getIsuStrategisCountAttribute()
    {
        return IsuStrategis::whereHas('pilar', function($query) {
            $query->where('RenstraID', $this->RenstraID);
        })->count();
    }
    
    // Get count of program pengembangan
    public function getProgramPengembanganCountAttribute()
    {
        return ProgramPengembangan::whereHas('isuStrategis.pilar', function($query) {
            $query->where('RenstraID', $this->RenstraID);
        })->count();
    }
    
    // Get count of program rektor
    public function getProgramRektorCountAttribute()
    {
        return ProgramRektor::whereHas('programPengembangan.isuStrategis.pilar', function($query) {
            $query->where('RenstraID', $this->RenstraID);
        })->count();
    }
    
    // Get count of kegiatan
    public function getKegiatanCountAttribute()
    {
        return Kegiatan::whereHas('programRektor.programPengembangan.isuStrategis.pilar', function($query) {
            $query->where('RenstraID', $this->RenstraID);
        })->count();
    }
    
    // Get years covered by this renstra
    public function getYearsAttribute()
    {
        $years = [];
        $start = (int)$this->PeriodeMulai;
        $end = (int)$this->PeriodeSelesai;
        
        for ($i = $start; $i <= $end; $i++) {
            $years[] = $i;
        }
        
        return $years;
    }
}
