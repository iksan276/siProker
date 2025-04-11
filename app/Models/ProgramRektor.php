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
        'Nama',
        'Tahun',
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
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function editedBy()
    {
        return $this->belongsTo(User::class, 'UEdited', 'id');
    }
}
