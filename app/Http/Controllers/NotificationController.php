<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $notifications = Notification::with(['sender', 'kegiatan'])
                ->where('UserID', Auth::id())
                ->orderBy('DCreated', 'desc')
                ->limit(10) // Limit untuk performa
                ->get();
                
            // Format data untuk frontend
            $formattedNotifications = $notifications->map(function($notification) {
                return [
                    'NotificationID' => $notification->NotificationID,
                    'KegiatanID' => $notification->KegiatanID,
                    'Title' => $notification->Title,
                    'Description' => $notification->Description,
                    'read_at' => $notification->read_at,
                    'DCreated' => $notification->DCreated,
                    'formatted_date' => $notification->formatted_date,
                    'sender' => [
                        'id' => $notification->sender->id ?? null,
                        'name' => $notification->sender->name ?? 'System'
                    ],
                    'kegiatan' => [
                        'KegiatanID' => $notification->kegiatan->KegiatanID ?? null,
                        'Nama' => $notification->kegiatan->Nama ?? 'Kegiatan tidak ditemukan'
                    ]
                ];
            });
                
            return response()->json($formattedNotifications);
        } catch (\Exception $e) {
            Log::error('Error loading notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load notifications'], 500);
        }
    }
    
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('NotificationID', $id)
                ->where('UserID', Auth::id())
                ->first();
                
            if (!$notification) {
                return response()->json(['error' => 'Notification not found'], 404);
            }
            
            $notification->markAsRead();
            
            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notification as read'], 500);
        }
    }
    
    public function markAllAsRead()
    {
        try {
            Notification::where('UserID', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
                
            return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark all notifications as read'], 500);
        }
    }
    
    public function getUnreadCount()
    {
        try {
            $count = Notification::where('UserID', Auth::id())
                ->whereNull('read_at')
                ->count();
                
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get unread count'], 500);
        }
    }
}
