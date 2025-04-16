<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renstra extends Model
{
    protected $table = 'renstras';
    protected $primaryKey = 'RenstraID';
    public $timestamps = false;
    
    protected $fillable = [
        'Nama',
        'PeriodeMulai',
        'PeriodeSelesai',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function pilars()
    {
        return $this->hasMany(Pilar::class, 'RenstraID', 'RenstraID');
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
    
    // Scope untuk mendapatkan renstra aktif
    public function scopeActive($query)
    {
        return $query->where('NA', 'N');
    }
}
