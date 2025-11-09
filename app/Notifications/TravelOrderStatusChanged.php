<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TravelOrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        private int $orderId,
        private string $destination,
        private string $oldStatus,
        private string $newStatus
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $statusMessages = [
            'solicitado' => 'Solicitado',
            'aprovado' => 'Aprovado',
            'cancelado' => 'Cancelado',
        ];

        return [
            'order_id' => $this->orderId,
            'destination' => $this->destination,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => sprintf(
                'Sua ordem de viagem para %s foi %s.',
                $this->destination,
                strtolower($statusMessages[$this->newStatus] ?? $this->newStatus)
            ),
        ];
    }
}
