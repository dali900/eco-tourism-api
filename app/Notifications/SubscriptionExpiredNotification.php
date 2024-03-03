<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\App;

class SubscriptionExpiredNotification extends Notification
{
    use Queueable;

    private $subscription;
    private $app;

    /**
     * Create a new notification instance.
     */
    public function __construct($subscription, $app)
    {
        $this->subscription = $subscription;
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
            'end_date' => $this->subscription->getEndDateFormated()
        ];

        $emailView = "";
        if ($this->app === App::BZR_KEY) {
            $emailView = 'emails.bzr.subscriptionExpired';
        } else if ($this->app === App::EI_KEY) {
            $emailView = 'emails.ei.subscriptionExpired';
        } else if ($this->app === App::ZZS_KEY) {
            $emailView = 'emails.zzs.subscriptionExpired';
        }
        return (new MailMessage)
            ->from('info.bzrportal@actamedia.rs', $appName)
            ->subject("Pretplata")
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
