<?php

use App\Http\Controllers\KHPayWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/khpay', [KHPayWebhookController::class, 'handle'])
    ->name('webhooks.khpay')
    ->middleware(['throttle:60,1']);   // basic rate-limit; remove csrf via $except
