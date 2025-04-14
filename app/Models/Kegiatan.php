<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $primaryKey = 'KegiatanID';
    public $timestamps = false;
    
    protected $fillable = [
        'IndikatorKinerjaID',
        'Nama',
        'TanggalMulai',
        'TanggalSelesai',
        'RincianKegiatan',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'KegiatanID';
    }
    
    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'IndikatorKinerjaID', 'IndikatorKinerjaID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
}
