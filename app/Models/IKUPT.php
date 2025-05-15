<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IKUPT extends Model
{
    protected $table = 'ikupts';
    protected $primaryKey = 'IKUPTID';
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
        return 'IKUPTID';
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
        return $this->belongsToMany(IndikatorKinerja::class, null, 'IKUPTID', 'IndikatorKinerjaID')
            ->using(function ($query) {
                $query->whereRaw("FIND_IN_SET(ikupts.IKUPTID, indikator_kinerjas.IKUPTID)");
            });
    }
    
    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
}
