<?php

namespace App\Notifications;

use App\Models\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FreeTrialExpiredNotification extends Notification
{
    use Queueable;

    private $freeTrial;
    private $app;

    /**
     * Create a new notification instance.
     */
    public function __construct($freeTrial, $app)
    {
        $this->freeTrial = $freeTrial;
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
        $data = [
            'user' => [
                'first_name' => $notifiable->first_name,
                'last_name' => $notifiable->last_name,
            ],
            'user_name' => $notifiable->first_name.' '.$notifiable->last_name,
            'app_data' => $appData,
            'end_date' => $this->freeTrial->getEndDateFormated()
        ];

        $emailView = "";
        if ($this->app === App::BZR_KEY) {
            $emailView = 'emails.bzr.freeTrialExpired';
        } else if ($this->app === App::EI_KEY) {
            $emailView = 'emails.ei.freeTrialExpired';
        } else if ($this->app === App::ZZS_KEY) {
            $emailView = 'emails.zzs.freeTrialExpired';
        }
        return (new MailMessage)
            ->from('noreply@actamedia.rs', $appName)
            ->subject("Probni period")
            ->view(
                $emailView, $data
            );
        
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
