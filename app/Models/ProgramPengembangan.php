<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPengembangan extends Model
{
    protected $table = 'program_pengembangans';
    protected $primaryKey = 'ProgramPengembanganID';
    public $timestamps = false;
    
    protected $fillable = [
        'IsuID',
        'Nama',
        'NA',
        'DCreated',
        'UCreated',
        'DEdited',
        'UEdited'
    ];
    
    public function isuStrategis()
    {
        return $this->belongsTo(IsuStrategis::class, 'IsuID', 'IsuID');
    }
    
    public function programRektors()
    {
        return $this->hasMany(ProgramRektor::class, 'ProgramPengembanganID', 'ProgramPengembanganID');
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
