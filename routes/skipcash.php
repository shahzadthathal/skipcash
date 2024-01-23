<?php

use Illuminate\Support\Facades\Route;

Route::get('/payment/generate-payment-link', [\Shahzadthathal\Skipcash\Http\Controllers\SkipCashController::class, 'generatePaymentLink']);
Route::any('/payment/gateway/response/skipcash', [\Shahzadthathal\Skipcash\Http\Controllers\SkipCashController::class, 'paymentGatewayResponseSkipcash']);
Route::any('/payment/gateway/response/skipcash/webhook', [\Shahzadthathal\Skipcash\Http\Controllers\SkipCashController::class, 'paymentGatewayResponseSkipcashWebhook']);

