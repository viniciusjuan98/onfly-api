<?php

namespace App\Services;

use App\Exceptions\NotificationException;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Get all notifications for a user
     */
    public function getNotifications(int $userId): Collection
    {
        return DatabaseNotification::where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(string $notificationId, int $userId): DatabaseNotification
    {
        $notification = DatabaseNotification::where('id', $notificationId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $userId)
            ->first();

        if (!$notification) {
            throw NotificationException::notFound();
        }


        if ($notification->notifiable_id !== $userId) {
            throw NotificationException::unauthorized();
        }

        $notification->markAsRead();

        return $notification;
    }
}


