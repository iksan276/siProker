<?php

namespace App\Jobs;

use App\Mail\KegiatanNotification;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kegiatan;
    protected $sender;
    protected $title;
    protected $description;
    protected $recipient;

    public $tries = 3;
    public $timeout = 60;

    public function __construct(Kegiatan $kegiatan, User $sender, string $title, string $description, User $recipient)
    {
        $this->kegiatan = $kegiatan;
        $this->sender = $sender;
        $this->title = $title;
        $this->description = $description;
        $this->recipient = $recipient;
    }

    public function handle(): void
    {
        try {
            if (!$this->recipient->email) {
                Log::warning("Recipient {$this->recipient->id} has no email address");
                return;
            }

            Mail::to($this->recipient->email)->send(
                new KegiatanNotification($this->kegiatan, $this->sender, $this->title, $this->description)
            );
            
            Log::info("Email notification sent successfully to {$this->recipient->email}");
            
        } catch (\Exception $e) {
            Log::error('Failed to send notification email: ' . $e->getMessage(), [
                'recipient_email' => $this->recipient->email ?? 'N/A',
                'kegiatan_id' => $this->kegiatan->KegiatanID,
                'error' => $e->getMessage()
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }
    
    public function failed(\Throwable $exception): void
    {
        Log::error('Email notification job failed permanently: ' . $exception->getMessage(), [
            'recipient_email' => $this->recipient->email ?? 'N/A',
            'kegiatan_id' => $this->kegiatan->KegiatanID
        ]);
    }
}
