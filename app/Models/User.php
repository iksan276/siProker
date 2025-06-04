<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->level === 1;
    }
    
    /**
     * Check if user is regular user
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->level === 2;
    }
    
    /**
     * Check if user is super user
     *
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->level === 3;
    }
    
    /**
     * Check if user can receive notifications
     *
     * @return bool
     */
    public function canReceiveNotifications()
    {
        return in_array($this->level, [1, 3]); // admin or super user
    }
    
    /**
     * Scope for notification recipients
     */
    public function scopeNotificationRecipients($query)
    {
        return $query->whereIn('level', [1, 3]); // admin and super user
    }
    
    /**
     * Get user's role name
     */
    public function getRoleNameAttribute()
    {
        switch ($this->level) {
            case 1:
                return 'Admin';
            case 2:
                return 'User';
            case 3:
                return 'Super User';
            default:
                return 'Unknown';
        }
    }
}
