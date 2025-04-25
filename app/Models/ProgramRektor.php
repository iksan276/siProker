<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRektor extends Model
{
    protected $table = 'program_rektors';
    protected $primaryKey = 'ProgramRektorID';
    public $timestamps = false;
    
    protected $fillable = [
        'ProgramPengembanganID',
        'IndikatorKinerjaID', // Added IndikatorKinerjaID
        'Nama',
        'Output',
        'Outcome',
        'JenisKegiatanID',
        'MataAnggaranID',
        'JumlahKegiatan',
        'SatuanID',
        'HargaSatuan',
        'Total',
        'PenanggungJawabID',
        'PelaksanaID',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function programPengembangan()
    {
        return $this->belongsTo(ProgramPengembangan::class, 'ProgramPengembanganID', 'ProgramPengembanganID');
    }
    
    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'IndikatorKinerjaID', 'IndikatorKinerjaID');
    }
    
    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'JenisKegiatanID', 'JenisKegiatanID');
    }
    
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'SatuanID', 'SatuanID');
    }
    
    public function penanggungJawab()
    {
        return $this->belongsTo(Unit::class, 'PenanggungJawabID', 'UnitID');
    }
    
    public function getMataAnggaransAttribute()
    {
        if (empty($this->MataAnggaranID)) {
            return collect();
        }
        
        $ids = explode(',', $this->MataAnggaranID);
        return MataAnggaran::whereIn('MataAnggaranID', $ids)->get();
    }
    
    public function getPelaksanasAttribute()
    {
        if (empty($this->PelaksanaID)) {
            return collect();
        }
        
        $ids = explode(',', $this->PelaksanaID);
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
}
