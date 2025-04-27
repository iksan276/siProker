<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $primaryKey = 'KegiatanID';
    public $timestamps = false;
    
    protected $fillable = [
        'ProgramRektorID',
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
    
    public function programRektor()
    {
        return $this->belongsTo(ProgramRektor::class, 'ProgramRektorID', 'ProgramRektorID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Helper method to get indikator kinerja through program rektor
    public function indikatorKinerja()
    {
        return $this->programRektor ? $this->programRektor->indikatorKinerja() : null;
    }
    
    // Helper method to get program pengembangan through program rektor
    public function programPengembangan()
    {
        return $this->programRektor ? $this->programRektor->programPengembangan() : null;
    }
    
    // Helper method to get isu strategis through program rektor -> program pengembangan
    public function isuStrategis()
    {
        return $this->programRektor && $this->programRektor->programPengembangan ? 
               $this->programRektor->programPengembangan->isuStrategis() : null;
    }
    
    // Helper method to get pilar through program rektor -> program pengembangan -> isu strategis
    public function pilar()
    {
        return $this->programRektor && $this->programRektor->programPengembangan && 
               $this->programRektor->programPengembangan->isuStrategis ? 
               $this->programRektor->programPengembangan->isuStrategis->pilar() : null;
    }
    
    // Helper method to get renstra through program rektor -> program pengembangan -> isu strategis -> pilar
    public function renstra()
    {
        return $this->programRektor && $this->programRektor->programPengembangan && 
               $this->programRektor->programPengembangan->isuStrategis && 
               $this->programRektor->programPengembangan->isuStrategis->pilar ? 
               $this->programRektor->programPengembangan->isuStrategis->pilar->renstra() : null;
    }
}
