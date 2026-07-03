<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Create an in-app notification record.
     */
    public function create(User $user, string $title, string $body, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'read_at' => null,
        ]);
    }

    /**
     * Send a Firebase Cloud Messaging push notification.
     */
    public function pushToDevice(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        $serverKey = config('services.firebase.server_key');
        if (! $serverKey || ! $fcmToken) {
            return false;
        }

        $response = Http::withToken($serverKey)
            ->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $fcmToken,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => $data,
            ]);

        return $response->successful();
    }

    /**
     * Mark all unread notifications for a user as read.
     */
    public function markAllRead(User $user): void
    {
        Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
