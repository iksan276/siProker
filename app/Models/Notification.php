<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'NotificationID';
    public $timestamps = false;
    
    protected $fillable = [
        'KegiatanID',
        'Title',
        'Description',
        'UserID',
        'read_at',
        'DCreated',
        'UCreated'
    ];
    
    protected $dates = [
        'read_at',
        'DCreated'
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'DCreated' => 'datetime'
    ];
    
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'KegiatanID', 'KegiatanID');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'id');
    }
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'UCreated', 'id');
    }
    
    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }
    
    public function isRead()
    {
        return !is_null($this->read_at);
    }
    
    // Accessor for formatted date
    public function getFormattedDateAttribute()
    {
        if (!$this->DCreated) {
            return '-';
        }
        
        $date = Carbon::parse($this->DCreated);
        $now = Carbon::now();
        
        // If today, show time
        if ($date->isToday()) {
            return $date->format('H:i');
        }
        
        // If yesterday
        if ($date->isYesterday()) {
            return 'Kemarin ' . $date->format('H:i');
        }
        
        // If this week
        if ($date->isCurrentWeek()) {
            return $date->locale('id')->format('l H:i');
        }
        
        // If this year
        if ($date->isCurrentYear()) {
            return $date->format('d M H:i');
        }
        
        // Default format
        return $date->format('d/m/Y H:i');
    }
    
    // Scope for unread notifications
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
    
    // Scope for user notifications
    public function scopeForUser($query, $userId)
    {
        return $query->where('UserID', $userId);
    }
}
