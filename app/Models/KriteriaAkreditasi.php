<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaAkreditasi extends Model
{
    protected $table = 'kriteria_akreditasis';
    protected $primaryKey = 'KriteriaAkreditasiID';
    public $timestamps = false;
    
    protected $fillable = [
        'Nama',
        'Key',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'KriteriaAkreditasiID';
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
        return $this->belongsToMany(IndikatorKinerja::class, null, 'KriteriaAkreditasiID', 'IndikatorKinerjaID')
            ->using(function ($query) {
                $query->whereRaw("FIND_IN_SET(kriteria_akreditasis.KriteriaAkreditasiID, indikator_kinerjas.KriteriaAkreditasiID)");
            });
    }
    
    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
}
