<?php

namespace App\Notifications;

use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactConfirmationNotification extends Notification
{
    use Queueable;

    private $app;

    /**
     * Create a new notification instance.
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appData = App::getData($this->app);
        $appName = $appData['name'];
        
        return (new MailMessage)
            ->from('noreply@actamedia.rs', $appName)
            ->subject("Kontakt")
            ->view('emails.contactConfirmation');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
