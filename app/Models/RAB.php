<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RAB extends Model
{
    protected $table = 'rabs';
    protected $primaryKey = 'RABID';
    public $timestamps = false;
    
    protected $fillable = [
        'KegiatanID',
        'SubKegiatanID',
        'Komponen',
        'Volume',
        'Satuan',
        'HargaSatuan',
        'Jumlah',
        'Status',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'RABID';
    }
    
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'KegiatanID', 'KegiatanID');
    }
    
    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'SubKegiatanID', 'SubKegiatanID');
    }
    
    public function satuanRelation()
    {
        return $this->belongsTo(Satuan::class, 'Satuan', 'SatuanID');
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
    
    // Format currency
    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format($this->HargaSatuan, 0, ',', '.');
    }
    
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->Jumlah, 0, ',', '.');
    }
}
