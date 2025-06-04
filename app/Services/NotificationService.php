<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Kegiatan;
use App\Models\User;
use App\Events\KegiatanStatusUpdated;
use App\Mail\KegiatanNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendKegiatanNotification(Kegiatan $kegiatan, string $type)
    {
      
        $sender = auth()->user();
        
        // Get ALL admin and super users
        $recipients = User::whereIn('level', [1, 3])->get(); // 1 = admin, 3 = super user
        
        Log::info("Found {$recipients->count()} recipients for notification", [
            'recipients' => $recipients->pluck('email')->toArray()
        ]);
        
        // Determine title and description based on type
        $title = '';
        $description = '';
        
        switch ($type) {
            case 'ajukan_kegiatan':
                $title = 'Pengajuan Kegiatan Baru';
                $description = "Kegiatan '{$kegiatan->Nama}' telah diajukan oleh {$sender->name} dan menunggu persetujuan.";
                break;
            case 'ajukan_tor':
                $title = 'Pengajuan TOR Kegiatan';
                $description = "TOR untuk kegiatan '{$kegiatan->Nama}' telah diajukan oleh {$sender->name} dan menunggu persetujuan.";
                break;
        }
        
        // Create notifications in database and send emails for ALL recipients
        foreach ($recipients as $recipient) {
            try {
                // Create notification in database
                $notification = Notification::create([
                    'KegiatanID' => $kegiatan->KegiatanID,
                    'Title' => $title,
                    'Description' => $description,
                    'UserID' => $recipient->id,
                    'DCreated' => now(),
                    'UCreated' => $sender->id
                ]);
                
                Log::info("Created notification for user: {$recipient->email}");
                
                // Send email directly (no queue)
                try {
                    Log::info("Attempting to send email to: {$recipient->email}");
                    
                    Mail::to($recipient->email)->send(
                        new KegiatanNotification($kegiatan, $sender, $title, $description)
                    );
                    
                    Log::info("Successfully sent email notification to: {$recipient->email}");
                } catch (\Exception $e) {

                    dd("Failed to send email notification to {$recipient->email}: " . $e->getMessage());
                    Log::error("Failed to send email notification to {$recipient->email}: " . $e->getMessage());
                    Log::error("Email error trace: " . $e->getTraceAsString());
                }
                
            } catch (\Exception $e) {
                Log::error("Failed to create notification for user {$recipient->id}: " . $e->getMessage());
            }
        }
        
        // Broadcast real-time notification to ALL recipients
        try {
            event(new KegiatanStatusUpdated($kegiatan, $sender, $title, $description, $recipients->toArray()));
            Log::info("Broadcasted real-time notification to {$recipients->count()} recipients");
        } catch (\Exception $e) {
            Log::error("Failed to broadcast notification: " . $e->getMessage());
        }
    }
}
