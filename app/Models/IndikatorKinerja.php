<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    protected $table = 'indikator_kinerjas';
    protected $primaryKey = 'IndikatorKinerjaID';
    public $timestamps = false;
    
    protected $fillable = [
        'ProgramRektorID',
        'SatuanID',
        'Nama',
        'Bobot',
        'HargaSatuan',
        'Jumlah',
        'MetaAnggaranID',
        'UnitTerkaitID',
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
    
    public function programRektor()
    {
        return $this->belongsTo(ProgramRektor::class, 'ProgramRektorID', 'ProgramRektorID');
    }
    
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'SatuanID', 'SatuanID');
    }
    
    public function unitTerkait()
    {
        $ids = explode(',', $this->UnitTerkaitID);
        return Unit::whereIn('UnitID', $ids)->get();
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
    
    public function metaAnggarans()
    {
        // Get the meta anggaran IDs as an array
        $ids = explode(',', $this->MetaAnggaranID);
        return MetaAnggaran::whereIn('MetaAnggaranID', $ids)->get();
    }
}
