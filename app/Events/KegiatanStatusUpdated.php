<?php

namespace App\Events;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class KegiatanStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $kegiatan;
    public $sender;
    public $title;
    public $description;
    public $recipients;
    public $infoBoxType;
    public $status; // Add this property


    public function __construct(Kegiatan $kegiatan, User $sender, string $title, string $description, array $recipients, string $infoBoxType = 'info')
    {
        $this->kegiatan = $kegiatan;
        $this->sender = $sender;
        $this->title = $title;
        $this->description = $description;
        $this->recipients = $recipients;
        $this->infoBoxType = $infoBoxType;
        $this->status = $kegiatan->Status; // ADD THIS L
         
        Log::info("KegiatanStatusUpdated event created for {$this->kegiatan->KegiatanID} with " . count($recipients) . " recipients");
    }

    public function broadcastOn()
    {
        $channels = [];
        foreach ($this->recipients as $recipient) {
            $userId = is_array($recipient) ? $recipient['id'] : $recipient->id;
            $channels[] = new PrivateChannel('notifications.' . $userId);
            Log::info("Broadcasting to channel: notifications.{$userId}");
        }
        return $channels;
    }

        public function broadcastWith()
        {
            return [
                'notification' => [
                    'title' => $this->title,
                    'description' => $this->description,
                    'kegiatan_id' => $this->kegiatan->KegiatanID,
                    'kegiatan_name' => $this->kegiatan->Nama,
                    'sender_name' => $this->sender->name,
                    'sender_id' => $this->sender->id,
                    'status' => $this->status, // Use the property
                    'info_box_type' => $this->infoBoxType ?? 'info',
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'timestamp' => now()->timestamp
                ]
            ];
        }


    public function broadcastAs()
    {
        return 'kegiatan.status.updated';
    }
}
