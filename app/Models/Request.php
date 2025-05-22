<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $table = 'requests';
    protected $primaryKey = 'RequestID';
    public $timestamps = false;
    
    protected $fillable = [
        'KegiatanID',
        'SubKegiatanID',
        'RABID',
        'Feedback',
        'DCreated',
        'UCreated'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'RequestID';
    }
    
    // Relationships
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'KegiatanID', 'KegiatanID');
    }
    
    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'SubKegiatanID', 'SubKegiatanID');
    }
    
    public function rab()
    {
        return $this->belongsTo(RAB::class, 'RABID', 'RABID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Helper method to get the entity type (Kegiatan, SubKegiatan, or RAB)
    public function getEntityTypeAttribute()
    {
        if ($this->KegiatanID && !$this->SubKegiatanID && !$this->RABID) {
            return 'Kegiatan';
        } elseif ($this->SubKegiatanID && !$this->RABID) {
            return 'SubKegiatan';
        } elseif ($this->RABID) {
            return 'RAB';
        } else {
            return 'Unknown';
        }
    }
    
    // Helper method to get the entity name
    public function getEntityNameAttribute()
    {
        if ($this->KegiatanID && !$this->SubKegiatanID && !$this->RABID) {
            return $this->kegiatan ? $this->kegiatan->Nama : 'Unknown Kegiatan';
        } elseif ($this->SubKegiatanID && !$this->RABID) {
            return $this->subKegiatan ? $this->subKegiatan->Nama : 'Unknown SubKegiatan';
        } elseif ($this->RABID) {
            return $this->rab ? $this->rab->Komponen : 'Unknown RAB';
        } else {
            return 'Unknown Entity';
        }
    }
}
