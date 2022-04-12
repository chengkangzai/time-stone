<?php

namespace App\Actions\Payment;

use App\Models\Payment;
use Laravel\Cashier\Exceptions\IncompletePayment;

class PayForDonation
{
    /**
     * @throws IncompletePayment
     */
    public function execute(mixed $paymentId, string $paymentMethod): void
    {
        $user = auth()->user();
        $donate = Payment::query()
            ->whereBelongsTo($user)
            ->findOrFail($paymentId);

        $user->createOrGetStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
        $user->invoiceFor('Donation', $donate->price);
    }
}
