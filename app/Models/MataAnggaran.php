<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataAnggaran extends Model
{
    protected $table = 'mata_anggarans';
    protected $primaryKey = 'MataAnggaranID';
    public $timestamps = false;
    
    protected $fillable = [
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
        return 'MataAnggaranID';
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
