<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IsuStrategis extends Model
{
    protected $table = 'isu_strategis';
    protected $primaryKey = 'IsuID';
    public $timestamps = false;
    
    protected $fillable = [
        'PilarID',
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    // Add this method to tell Laravel which column to use for route model binding
    public function getRouteKeyName()
    {
        return 'IsuID';
    }
    
    public function pilar()
    {
        return $this->belongsTo(Pilar::class, 'PilarID', 'PilarID');
    }
    
    public function programPengembangans()
    {
        return $this->hasMany(ProgramPengembangan::class, 'IsuID', 'IsuID');
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
