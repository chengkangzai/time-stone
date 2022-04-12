<?php

namespace App\Actions\Payment;

use App\Models\Payment;
use App\Models\User;

class GetPaymentAndPaymentIntent
{
    public function execute(User $user): array
    {
        $payment = Payment::with('donate')
            ->whereBelongsTo($user)
            ->whereNull('paid_at')
            ->latest()
            ->firstOrFail();

        $paymentIntent = $user->createSetupIntent();

        return [$payment, $paymentIntent];
    }
}
