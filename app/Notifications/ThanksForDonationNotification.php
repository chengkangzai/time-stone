<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThanksForDonationNotification extends Notification
{
    use Queueable;

    public function __construct(private string $receiptUrl)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Thank you for your donation!')
            ->line('This is a notification to let you know that your donation has been received')
            ->action('View Receipt', $this->receiptUrl)
            ->line('Best wishes.');

    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
