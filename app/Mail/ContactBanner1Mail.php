<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactBanner1Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $banner = "";
        if (isset($this->details['banner1_link']) && $this->details['banner1_link']) {
            $banner = " - Baner 1";
        }
        return $this->subject('Novi kontakt zahtev - Baner 1')
            ->from('noreply@actamedia.rs')
            ->view('emails.banners.contactBanner1Mail');
    }
}