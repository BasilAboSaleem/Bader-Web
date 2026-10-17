<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent when the team gives a sponsor a dashboard account: a link to choose their own password.
 */
class SponsorInvitation extends Notification
{
    public function __construct(public string $link) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('sponsors.invitation.subject'))
            ->greeting(__('sponsors.invitation.greeting', ['name' => $notifiable->name]))
            ->line(__('sponsors.invitation.intro'))
            ->action(__('sponsors.invitation.action'), $this->link)
            ->line(__('sponsors.invitation.expiry'))
            ->salutation(__('sponsors.invitation.salutation'));
    }
}
