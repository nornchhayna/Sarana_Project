<?php

namespace App\Jobs;

use App\Mail\LowStockAlert;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Stock $stock) {}

    public function handle(): void
    {
        $adminEmail = config('mail.admin_address', 'admin@phonestore.com');
        Mail::to($adminEmail)->send(new LowStockAlert($this->stock));
    }
}
