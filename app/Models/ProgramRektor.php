<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramRektor extends Model
{
    protected $table = 'program_rektors';
    protected $primaryKey = 'ProgramRektorID';
    public $timestamps = false;
    
    protected $fillable = [
        'ProgramRektorID', // Tambahkan ini
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
    
    // Define a proper relationship for indikatorKinerja
    public function indikatorKinerja()
    {
        $ids = explode(',', $this->IndikatorKinerjaID);
        return IndikatorKinerja::whereIn('IndikatorKinerjaID', $ids);
    }
    
    // Keep the accessor for backward compatibility
    public function getIndikatorKinerjaAttribute()
    {
        if (empty($this->IndikatorKinerjaID)) {
            return collect();
        }
        
        $ids = explode(',', $this->IndikatorKinerjaID);
        return IndikatorKinerja::whereIn('IndikatorKinerjaID', $ids)->get();
    }
    
    public function jenisKegiatan()
    {
        return $this->belongsTo(JenisKegiatan::class, 'JenisKegiatanID', 'JenisKegiatanID');
    }
    
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'SatuanID', 'SatuanID');
    }
    
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class, 'ProgramRektorID', 'ProgramRektorID');
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
    
    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
    
    // Scope for filtering by program pengembangan
    public function scopeByProgramPengembangan($query, $programPengembanganId)
    {
        if (!$programPengembanganId) {
            return $query;
        }
        
        return $query->where('ProgramPengembanganID', $programPengembanganId);
    }
    
    // Scope for filtering by jenis kegiatan
    public function scopeByJenisKegiatan($query, $jenisKegiatanId)
    {
        if (!$jenisKegiatanId) {
            return $query;
        }
        
        return $query->where('JenisKegiatanID', $jenisKegiatanId);
    }
    
    // Scope for filtering by indikator kinerja
    public function scopeByIndikatorKinerja($query, $indikatorKinerjaId)
    {
        if (!$indikatorKinerjaId) {
            return $query;
        }
        
        return $query->where('IndikatorKinerjaID', $indikatorKinerjaId);
    }
    
    // Scope for filtering by isu strategis through program pengembangan
    public function scopeByIsuStrategis($query, $isuId)
    {
        if (!$isuId) {
            return $query;
        }
        
        return $query->whereHas('programPengembangan', function($q) use ($isuId) {
            $q->where('IsuID', $isuId);
        });
    }
    
    // Scope for filtering by pilar through program pengembangan -> isu strategis
    public function scopeByPilar($query, $pilarId)
    {
        if (!$pilarId) {
            return $query;
        }
        
        return $query->whereHas('programPengembangan.isuStrategis', function($q) use ($pilarId) {
            $q->where('PilarID', $pilarId);
        });
    }
    
    // Scope for filtering by renstra through program pengembangan -> isu strategis -> pilar
    public function scopeByRenstra($query, $renstraId)
    {
        if (!$renstraId) {
            return $query;
        }
        
        return $query->whereHas('programPengembangan.isuStrategis.pilar', function($q) use ($renstraId) {
            $q->where('RenstraID', $renstraId);
        });
    }
    
    // Scope for filtering by year through kegiatans
    public function scopeForYear($query, $year)
    {
        if (!$year) {
            return $query;
        }
        
        return $query->whereHas('kegiatans', function($q) use ($year) {
            $q->whereYear('TanggalMulai', $year)
              ->orWhereYear('TanggalSelesai', $year);
        });
    }
    // Add this scope method to the ProgramRektor class
    public function scopeByUnit($query, $unitId)
    {
        if (!$unitId) {
            return $query;
        }
        
        return $query->where(function($q) use ($unitId) {
            $q->where('PelaksanaID', $unitId)
            ->orWhere('PelaksanaID', 'LIKE', $unitId.',%')
            ->orWhere('PelaksanaID', 'LIKE', '%,'.$unitId.',%')
            ->orWhere('PelaksanaID', 'LIKE', '%,'.$unitId);
        });
    }


}
