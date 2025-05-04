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
        'IndikatorKinerjaID',
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
    
    /**
     * Get penanggung jawab from API
     */
    public function getPenanggungJawabFromApi($ssoCode = null)
    {
        return Unit::getByIdFromApi($this->PenanggungJawabID, $ssoCode);
    }
    
    /**
     * Get pelaksana from API
     */
    public function getPelaksanaFromApi($ssoCode = null)
    {
        $pelaksanaIds = explode(',', $this->PelaksanaID);
        $units = Unit::getFromApi($ssoCode);
        
        $pelaksanas = [];
        foreach ($units as $unit) {
            if (in_array($unit['UnitID'], $pelaksanaIds)) {
                $pelaksanas[] = $unit;
            }
        }
        
        return $pelaksanas;
    }
    
    public function getMataAnggaransAttribute()
    {
        if (empty($this->MataAnggaranID)) {
            return collect();
        }
        
        $ids = explode(',', $this->MataAnggaranID);
        return MataAnggaran::whereIn('MataAnggaranID', $ids)->get();
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
