<?php

namespace App\Http\Controllers;

use App\Actions\Payment\ConfirmPayment;
use App\Actions\Payment\GetPaymentAndPaymentIntent;
use App\Actions\Payment\PayForDonation;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function confirm(Request $request, ConfirmPayment $action)
    {
        $action->execute($request->toArray());

        return redirect()->route('payments.checkout');
    }

    public function checkout(GetPaymentAndPaymentIntent $action)
    {
        [$payment, $paymentIntent] = $action->execute(auth()->user());

        return view('checkout', compact('payment', 'paymentIntent'));
    }

    public function pay(Request $request, PayForDonation $action)
    {
        try {
            $action->execute($request->input('payment_id'), $request->input('payment_method'));

            return redirect()->route('about')->with('success', 'Payment Successful, Thank you for your donation!');
        } catch (Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }
    }
}
