<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Kegiatan;
use App\Models\User;
use App\Events\KegiatanStatusUpdated;
use App\Mail\KegiatanNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\RabNotification; 
use Illuminate\Support\Facades\Log;
use App\Models\RAB;
use App\Mail\SubKegiatanNotification;
use App\Models\SubKegiatan;


class NotificationService
{
    public function sendKegiatanNotification(Kegiatan $kegiatan, string $type)
    {
        $sender = auth()->user();
        
        // Get recipients based on notification type
        $recipients = collect();
        
        switch ($type) {
            case 'ajukan_kegiatan':
            case 'ajukan_tor':
                // Send to ALL admin and super users when user submits
                $recipients = User::whereIn('level', [1, 3])->get();
                break;
                
            case 'status_updated':
                // Send to the user who created the kegiatan when admin/super user updates status
                if ($kegiatan->UCreated) {
                    $kegiatanOwner = User::find($kegiatan->UCreated);
                    if ($kegiatanOwner && $kegiatanOwner->level == 2) { // Only regular users
                        $recipients = collect([$kegiatanOwner]);
                    }
                }
                break;
        }
        
        if ($recipients->isEmpty()) {
            Log::info("No recipients found for notification type: {$type}");
            return;
        }
        
        Log::info("Found {$recipients->count()} recipients for notification", [
            'type' => $type,
            'recipients' => $recipients->pluck('email')->toArray()
        ]);
        
        // Determine title and description based on type
        $title = '';
        $description = '';
        $infoBoxType='';
        
        switch ($type) {
            case 'ajukan_kegiatan':
                $title = 'Pengajuan Kegiatan Baru';
                $description = "Kegiatan <b>{$kegiatan->Nama}</b> telah diajukan oleh <b>{$sender->name}</b> dan menunggu persetujuan.";
                break;
            case 'ajukan_tor':
                $title = 'Pengajuan TOR Kegiatan';
                $description = "TOR untuk kegiatan <b>{$kegiatan->Nama}</b> telah diajukan oleh <b>{$sender->name}</b> dan menunggu persetujuan.";
                break;
            case 'status_updated':
                $statusText = $this->getStatusText($kegiatan->Status);
                $title = 'Status Kegiatan Diperbarui';
                $description = "Status kegiatan <b>{$kegiatan->Nama}</b> telah diperbarui menjadi {$statusText} oleh <b>{$sender->name}</b>.";
                $infoBoxType = $this->getInfoBoxTypeFromStatus($kegiatan->Status);
                break;
        }
        
        // Create notifications in database for ALL recipients
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
                
            } catch (\Exception $e) {
                Log::error("Failed to create notification for user {$recipient->id}: " . $e->getMessage());
            }
        }
        
        // Send email with CC for submission types only
        if (in_array($type, ['ajukan_kegiatan', 'ajukan_tor']) && $recipients->count() > 0) {
            $this->sendEmailWithCC($kegiatan, $sender, $title, $description, $recipients, $infoBoxType);
        } elseif ($type === 'status_updated' && $recipients->count() > 0) {
            // For status updates, send individual emails
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(
                        new KegiatanNotification($kegiatan, $sender, $title, $description, $infoBoxType)
                    );
                    Log::info("Successfully sent email notification to: {$recipient->email}");
                } catch (\Exception $e) {
                    Log::error("Failed to send email notification to {$recipient->email}: " . $e->getMessage());
                }
            }
        }
        
        // Broadcast real-time notification to ALL recipients
                try {
                event(new KegiatanStatusUpdated($kegiatan, $sender, $title, $description, $recipients->toArray(), $infoBoxType));
                Log::info("Broadcasted real-time notification to {$recipients->count()} recipients");
            } catch (\Exception $e) {
                Log::error("Failed to broadcast notification: " . $e->getMessage());
            }
    }
    
    private function sendEmailWithCC(Kegiatan $kegiatan, User $sender, string $title, string $description, $recipients, string $infoBoxType)
    {
        try {
            // Get the first admin as primary recipient
            $primaryRecipient = $recipients->where('level', 1)->first();
            
            // If no admin found, use the first super user
            if (!$primaryRecipient) {
                $primaryRecipient = $recipients->where('level', 3)->first();
            }
            
            // If still no recipient, use the first available
            if (!$primaryRecipient) {
                $primaryRecipient = $recipients->first();
            }
            
            // Get remaining recipients for CC
            $ccRecipients = $recipients->reject(function ($recipient) use ($primaryRecipient) {
                return $recipient->id === $primaryRecipient->id;
            });
            
            Log::info("Sending email with CC", [
                'primary' => $primaryRecipient->email,
                'cc_count' => $ccRecipients->count(),
                'cc_emails' => $ccRecipients->pluck('email')->toArray()
            ]);
            
            // Send email with CC
            Mail::to($primaryRecipient->email)
                ->cc($ccRecipients->pluck('email')->toArray())
                ->send(new KegiatanNotification($kegiatan, $sender, $title, $description, $infoBoxType));
                
            Log::info("Successfully sent email with CC to primary: {$primaryRecipient->email} and CC: " . $ccRecipients->pluck('email')->implode(', '));
            
        } catch (\Exception $e) {
            Log::error("Failed to send email with CC: " . $e->getMessage());
            
            // Fallback: send individual emails
            foreach ($recipients as $recipient) {
                try {
                    Mail::to($recipient->email)->send(
                        new KegiatanNotification($kegiatan, $sender, $title, $description, $infoBoxType)
                    );
                    Log::info("Fallback: Successfully sent individual email to: {$recipient->email}");
                } catch (\Exception $e) {
                    Log::error("Fallback: Failed to send email to {$recipient->email}: " . $e->getMessage());
                }
            }
        }
    }
    
    private function getStatusText($status)
    {
        $statusMap = [
            'N' => '<span class="badge badge-warning">Menunggu</span>',
            'Y' => '<span class="badge badge-success">Disetujui</span>',
            'T' => '<span class="badge badge-danger">Ditolak</span>',
            'R' => '<span class="badge badge-info">Revisi</span>',
            'P' => '<span class="badge badge-primary">Pengajuan</span>',
            'PT' => '<span class="badge badge-warning">Pengajuan TOR</span>',
            'YT' => '<span class="badge badge-success">TOR Disetujui</span>',
            'TT' => '<span class="badge badge-danger">TOR Ditolak</span>',
            'RT' => '<span class="badge badge-info">TOR Revisi</span>',
            'TP' => '<span class="badge badge-warning">Tunda Pencairan</span>'
        ];
            
        return $statusMap[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
    }

       private function getInfoBoxTypeFromStatus($status)
    {
        $statusMap = [
            'N' => 'warning',    // Menunggu
            'Y' => 'success',    // Disetujui
            'T' => 'danger',     // Ditolak
            'R' => 'info',       // Revisi
            'P' => 'primary',    // Pengajuan
            'PT' => 'warning',   // Pengajuan TOR
            'YT' => 'success',   // TOR Disetujui
            'TT' => 'danger',    // TOR Ditolak
            'RT' => 'info',      // TOR Revisi
            'TP' => 'warning'    // Tunda Pencairan
        ];
            
        return $statusMap[$status] ?? 'secondary';
    }


   public function sendRabNotification(RAB $rab, string $type)
{
    $sender = auth()->user();
    
    // Get the kegiatan - either directly or through subKegiatan
    $kegiatan = null;
    if ($rab->KegiatanID) {
        // RAB belongs directly to kegiatan
        $kegiatan = $rab->kegiatan;
    } elseif ($rab->SubKegiatanID && $rab->subKegiatan) {
        // RAB belongs to sub kegiatan
        $kegiatan = $rab->subKegiatan->kegiatan;
    }
    
    if (!$kegiatan || !$kegiatan->UCreated) {
        Log::info("No kegiatan or kegiatan owner found for RAB notification", [
            'rab_id' => $rab->RABID,
            'kegiatan_id' => $rab->KegiatanID,
            'sub_kegiatan_id' => $rab->SubKegiatanID
        ]);
        return;
    }
    
    $kegiatanOwner = User::find($kegiatan->UCreated);
    if (!$kegiatanOwner || $kegiatanOwner->level != 2) { // Only regular users
        Log::info("Kegiatan owner not found or not a regular user", [
            'kegiatan_owner_id' => $kegiatan->UCreated,
            'owner_level' => $kegiatanOwner ? $kegiatanOwner->level : 'null'
        ]);
        return;
    }
    
    $recipients = collect([$kegiatanOwner]);
    
    Log::info("Found recipient for RAB notification", [
        'type' => $type,
        'recipient' => $kegiatanOwner->email,
        'rab_id' => $rab->RABID,
        'kegiatan_id' => $kegiatan->KegiatanID,
        'sub_kegiatan_id' => $rab->SubKegiatanID
    ]);
    
    // Determine title and description based on RAB type (kegiatan or sub kegiatan)
    $title = 'Status RAB Diperbarui';
    $description = '';
    $statusText = $this->getStatusText($rab->Status);
    $infoBoxType='';
    
    if ($rab->SubKegiatanID && $rab->subKegiatan) {
        // RAB belongs to sub kegiatan
        $subKegiatan = $rab->subKegiatan;
        $infoBoxType = $this->getInfoBoxTypeFromStatus($rab->Status);
        $description = "Status RAB dengan komponen <b>{$rab->Komponen}</b> di sub kegiatan <b>{$subKegiatan->Nama}</b> pada kegiatan <b>{$kegiatan->Nama}</b> telah diperbarui statusnya menjadi {$statusText} oleh <b>{$sender->name}</b>.";
    } else {
        // RAB belongs directly to kegiatan
         $infoBoxType = $this->getInfoBoxTypeFromStatus($rab->Status);
        $description = "Status RAB dengan komponen <b>{$rab->Komponen}</b> di kegiatan <b>{$kegiatan->Nama}</b> telah diperbarui statusnya menjadi {$statusText} oleh <b>{$sender->name}</b>.";
    }
    
    // Create notification in database and send email
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
            
            Log::info("Created RAB notification for user: {$recipient->email}");
            
            // Send email directly
            try {
                Log::info("Attempting to send RAB email to: {$recipient->email}");
                
                Mail::to($recipient->email)->send(
                    new RabNotification($kegiatan, $sender, $title, $description, $infoBoxType)
                );
                
                Log::info("Successfully sent RAB email notification to: {$recipient->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send RAB email notification to {$recipient->email}: " . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to create RAB notification for user {$recipient->id}: " . $e->getMessage());
        }
    }
    
    // Broadcast real-time notification
   try {
    event(new KegiatanStatusUpdated($kegiatan, $sender, $title, $description, $recipients->toArray(), $infoBoxType));
    Log::info("Broadcasted real-time RAB notification to {$recipients->count()} recipients");
} catch (\Exception $e) {
    Log::error("Failed to broadcast RAB notification: " . $e->getMessage());
}
}

public function sendSubKegiatanNotification(SubKegiatan $subKegiatan, string $type)
{
    $sender = auth()->user();
    
    // Get the parent kegiatan
    $kegiatan = $subKegiatan->kegiatan;
    
    if (!$kegiatan || !$kegiatan->UCreated) {
        Log::info("No kegiatan or kegiatan owner found for SubKegiatan notification", [
            'sub_kegiatan_id' => $subKegiatan->SubKegiatanID,
            'kegiatan_id' => $subKegiatan->KegiatanID
        ]);
        return;
    }
    
    $kegiatanOwner = User::find($kegiatan->UCreated);
    if (!$kegiatanOwner || $kegiatanOwner->level != 2) { // Only regular users
        Log::info("Kegiatan owner not found or not a regular user", [
            'kegiatan_owner_id' => $kegiatan->UCreated,
            'owner_level' => $kegiatanOwner ? $kegiatanOwner->level : 'null'
        ]);
        return;
    }
    
    $recipients = collect([$kegiatanOwner]);
    
    Log::info("Found recipient for SubKegiatan notification", [
        'type' => $type,
        'recipient' => $kegiatanOwner->email,
        'sub_kegiatan_id' => $subKegiatan->SubKegiatanID,
        'kegiatan_id' => $kegiatan->KegiatanID
    ]);
    
    // Determine title and description
    $title = 'Status Sub Kegiatan Diperbarui';
    $statusText = $this->getStatusText($subKegiatan->Status);
    $description = "Status sub kegiatan '{$subKegiatan->Nama}' pada kegiatan '{$kegiatan->Nama}' telah diperbarui statusnya menjadi '{$statusText}' oleh {$sender->name}.";
     $infoBoxType = $this->getInfoBoxTypeFromStatus($subKegiatan->Status);
    // Create notification in database and send email
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
            
            Log::info("Created SubKegiatan notification for user: {$recipient->email}");
            
            // Send email directly
            try {
                Log::info("Attempting to send SubKegiatan email to: {$recipient->email}");
                
                Mail::to($recipient->email)->send(
                    new SubKegiatanNotification($kegiatan, $subKegiatan, $sender, $title, $description, $infoBoxType)
                );
                
                Log::info("Successfully sent SubKegiatan email notification to: {$recipient->email}");
            } catch (\Exception $e) {
                Log::error("Failed to send SubKegiatan email notification to {$recipient->email}: " . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to create SubKegiatan notification for user {$recipient->id}: " . $e->getMessage());
        }
    }
    
    // Broadcast real-time notification
        try {
            event(new KegiatanStatusUpdated($kegiatan, $sender, $title, $description, $recipients->toArray(), $infoBoxType));
            Log::info("Broadcasted real-time SubKegiatan notification to {$recipients->count()} recipients");
        } catch (\Exception $e) {
            Log::error("Failed to broadcast SubKegiatan notification: " . $e->getMessage());
        }
}


}
