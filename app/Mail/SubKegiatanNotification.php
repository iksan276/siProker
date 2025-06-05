<?php

namespace App\Mail;

use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubKegiatanNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $kegiatan;
    public $subKegiatan;
    public $sender;
    public $title;
    public $description;

    public function __construct(Kegiatan $kegiatan, SubKegiatan $subKegiatan, User $sender, string $title, string $description)
    {
        $this->kegiatan = $kegiatan;
        $this->subKegiatan = $subKegiatan;
        $this->sender = $sender;
        $this->title = $title;
        $this->description = $description;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.sub-kegiatan-notification');
    }
}
