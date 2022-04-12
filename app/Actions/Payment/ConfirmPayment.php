<?php

namespace App\Actions\Payment;

use App\Models\Donate;
use Validator;

class ConfirmPayment
{
    public function execute(array $data)
    {
        $data = Validator::make($data, [
            'price' => 'required|integer|gte:10',
        ])->validate();

        $donate = Donate::create([
            'price' => $data['price'] * 100,
        ]);

        auth()->user()->payments()->create([
            'donate_id' => $donate->id,
            'price' => $donate->price,
        ]);
    }
}
