<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatans';
    protected $primaryKey = 'KegiatanID';
    public $timestamps = false;
    
    protected $fillable = [
        'KegiatanID',
        'ProgramRektorID',
        'Nama',
        'TanggalMulai',
        'TanggalSelesai',
        'TanggalPencairan',
        'RincianKegiatan',
        'Feedback',
         'Status',
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

        // Add relationship to creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    // Add relationship to editor
    public function editor()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
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
    
    // Relationships with SubKegiatan and RAB
    public function subKegiatans()
    {
        return $this->hasMany(SubKegiatan::class, 'KegiatanID', 'KegiatanID');
    }
    
    public function rabs()
    {
        return $this->hasMany(RAB::class, 'KegiatanID', 'KegiatanID');
    }
    
    // Helper method to get indikator kinerja through program rektor
    public function indikatorKinerja()
    {
        return $this->programRektor ? $this->programRektor->indikatorKinerja : null;
    }
    
    // Helper method to get program pengembangan through program rektor
    public function programPengembangan()
    {
        return $this->programRektor ? $this->programRektor->programPengembangan : null;
    }
    
    // Helper method to get isu strategis through program rektor -> program pengembangan
    public function isuStrategis()
    {
        return $this->programRektor && $this->programRektor->programPengembangan ? 
               $this->programRektor->programPengembangan->isuStrategis : null;
    }
    
    // Helper method to get pilar through program rektor -> program pengembangan -> isu strategis
    public function pilar()
    {
        return $this->programRektor && $this->programRektor->programPengembangan && 
               $this->programRektor->programPengembangan->isuStrategis ? 
               $this->programRektor->programPengembangan->isuStrategis->pilar : null;
    }
    
    // Helper method to get renstra through program rektor -> program pengembangan -> isu strategis -> pilar
    public function renstra()
    {
        return $this->programRektor && $this->programRektor->programPengembangan && 
               $this->programRektor->programPengembangan->isuStrategis && 
               $this->programRektor->programPengembangan->isuStrategis->pilar ? 
               $this->programRektor->programPengembangan->isuStrategis->pilar->renstra : null;
    }
    
    // Helper method to get total RAB amount
   // Helper method to get total RAB amount
    public function getTotalRABAmount()
    {
        $directRABs = $this->rabs()->whereNull('SubKegiatanID')->whereIn('Status', ['Y', 'N'])->sum('Jumlah');
        $subKegiatanRABs = $this->subKegiatans()->with(['rabs' => function($query) {
            $query->whereIn('Status', ['Y', 'N']);
        }])->get()->sum(function($subKegiatan) {
            return $subKegiatan->rabs->sum('Jumlah');
        });
        
        return $directRABs + $subKegiatanRABs;
    }

    
    // Helper method to get formatted total RAB amount
    public function getFormattedTotalRABAmountAttribute()
    {
        return 'Rp ' . number_format($this->getTotalRABAmount(), 0, ',', '.');
    }
    
    // Scope for filtering by year
    public function scopeForYear($query, $year)
    {
        if (!$year) {
            return $query;
        }
        
        return $query->whereYear('TanggalMulai', $year)
                     ->orWhereYear('TanggalSelesai', $year);
    }
    
    // Scope for filtering by program rektor
    public function scopeByProgramRektor($query, $programRektorId)
    {
        if (!$programRektorId) {
            return $query;
        }
        
        return $query->where('ProgramRektorID', $programRektorId);
    }
    
    // Scope for filtering by program pengembangan
    public function scopeByProgramPengembangan($query, $programPengembanganId)
    {
        if (!$programPengembanganId) {
            return $query;
        }
        
        return $query->whereHas('programRektor', function($q) use ($programPengembanganId) {
            $q->where('ProgramPengembanganID', $programPengembanganId);
        });
    }
    
    // Scope for filtering by isu strategis
    public function scopeByIsuStrategis($query, $isuId)
    {
        if (!$isuId) {
            return $query;
        }
        
        return $query->whereHas('programRektor.programPengembangan', function($q) use ($isuId) {
            $q->where('IsuID', $isuId);
        });
    }
    
    // Scope for filtering by pilar
    public function scopeByPilar($query, $pilarId)
    {
        if (!$pilarId) {
            return $query;
        }
        
        return $query->whereHas('programRektor.programPengembangan.isuStrategis', function($q) use ($pilarId) {
            $q->where('PilarID', $pilarId);
        });
    }
    
    // Scope for filtering by renstra
    public function scopeByRenstra($query, $renstraId)
    {
        if (!$renstraId) {
            return $query;
        }
        
        return $query->whereHas('programRektor.programPengembangan.isuStrategis.pilar', function($q) use ($renstraId) {
            $q->where('RenstraID', $renstraId);
        });
    }
    
    // Scope for filtering by jenis kegiatan
    public function scopeByJenisKegiatan($query, $jenisKegiatanId)
    {
        if (!$jenisKegiatanId) {
            return $query;
        }
        
        return $query->whereHas('programRektor', function($q) use ($jenisKegiatanId) {
            $q->where('JenisKegiatanID', $jenisKegiatanId);
        });
    }

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
            case 'PT':
                return '<span class="badge badge-warning">Pengajuan TOR</span>';
            case 'TP':
                return '<span class="badge badge-warning">Tunda Pencairan</span>';
            case 'YT':
                return '<span class="badge badge-success">Pengajuan TOR Disetujui</span>';
            case 'TT':
                return '<span class="badge badge-danger">Pengajuan TOR Ditolak</span>';
            case 'RT':
                return '<span class="badge badge-info">Pengajuan TOR direvisi</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
        }

     
}
