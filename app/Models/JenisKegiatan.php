<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKegiatan extends Model
{
    use HasFactory;
    
    // Define the primary key
    protected $primaryKey = 'JenisKegiatanID';
    
    // Disable Laravel's timestamps
    public $timestamps = false;
    
    // Define fillable fields
    protected $fillable = [
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    /**
     * Get the user who created this record
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    /**
     * Get the user who last edited this record
     */
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    /**
     * Get the program rektors associated with this jenis kegiatan
     */
    public function programRektors()
    {
        return $this->hasMany(ProgramRektor::class, 'JenisKegiatanID', 'JenisKegiatanID');
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
        
        return $query->whereHas('programRektors', function($q) use ($programPengembanganId) {
            $q->where('ProgramPengembanganID', $programPengembanganId);
        });
    }
}
