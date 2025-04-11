<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilar extends Model
{
    protected $table = 'pilars';
    protected $primaryKey = 'PilarID';
    public $timestamps = false;
    
    protected $fillable = [
        'RenstraID',
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function renstra()
    {
        return $this->belongsTo(Renstra::class, 'RenstraID', 'RenstraID');
    }
    
    public function isuStrategis()
    {
        return $this->hasMany(IsuStrategis::class, 'PilarID', 'PilarID');
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
