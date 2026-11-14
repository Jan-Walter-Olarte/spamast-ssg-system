<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    /**
     * A title, a message, and an optional link inside the system (like /announcements/5).
     */
    public function __construct(
        private string $title,
        private string $message,
        private ?string $url = null,
    ) {
    }

    /**
     * Store it in the database only (no email).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * What is saved in the notification's data column.
     *
     * @return array<string, string|null>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }
}
