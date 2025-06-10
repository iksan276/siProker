<?php

namespace App\Mail;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RabNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $kegiatan;
    public $sender;
    public $title;
    public $description;
    public $infoBoxType;

    public function __construct(Kegiatan $kegiatan, User $sender, string $title, string $description, string $infoBoxType)
    {
        $this->kegiatan = $kegiatan;
        $this->sender = $sender;
        $this->title = $title;
        $this->description = $description;
        $this->infoBoxType = $infoBoxType;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.rab-notification');
    }
}
