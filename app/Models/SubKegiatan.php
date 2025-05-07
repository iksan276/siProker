<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    protected $table = 'sub_kegiatans';
    protected $primaryKey = 'SubKegiatanID';
    public $timestamps = false;
    
    protected $fillable = [
        'KegiatanID',
        'Nama',
        'JadwalMulai',
        'JadwalSelesai',
        'Status',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'SubKegiatanID';
    }
    
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'KegiatanID', 'KegiatanID');
    }
    
    public function rabs()
    {
        return $this->hasMany(RAB::class, 'SubKegiatanID', 'SubKegiatanID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Status accessor
    public function getStatusLabelAttribute()
    {
        switch ($this->Status) {
            case 'N':
                return '<span class="badge badge-warning">Menunggu</span>';
            case 'Y':
                return '<span class="badge badge-success">Disetujui</span>';
            case 'T':
                return '<span class="badge badge-danger">Ditolak</span>';
            case 'R':
                return '<span class="badge badge-info">Revisi</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
        }
    }
    