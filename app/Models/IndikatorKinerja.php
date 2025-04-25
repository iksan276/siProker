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
}
