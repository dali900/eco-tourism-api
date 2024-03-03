<?php

namespace App\Mail;

use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactConfirmationMAil extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        public $app
    ){}

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
        $appData = App::getData($this->app);
        $appName = $appData['name'];

        return $this->subject('Kontakt')
            ->from('noreply@actamedia.rs', $appName)
            ->view('emails.contactConfirmation');
    }
}