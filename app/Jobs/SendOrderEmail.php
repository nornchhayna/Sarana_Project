<?php

namespace App\Jobs;

use App\Models\Order;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderShippedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Order $order, public string $notificationType = 'confirmed') {}

    public function handle(): void
    {
        $notification = match ($this->notificationType) {
            'shipped' => new OrderShippedNotification($this->order),
            'delivered' => new OrderDeliveredNotification($this->order),
            default => new OrderConfirmedNotification($this->order),
        };

        $this->order->user->notify($notification);
    }
}
