<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Validator;

class PaymentController extends Controller
{
    public function confirm(Request $request)
    {
        $data = Validator::make($request->all(), [
            'price' => 'required|integer|gte:10',
        ]);

        $donate = Donate::create([
            'price' => $data->price * 100
        ]);

        auth()->user()->payments()->create([
            'donate_id' => $donate->id,
            'price' => $donate->price,
        ]);

        return redirect()->route('payments.checkout');
    }

    public function checkout()
    {
        $payment = Payment::with('donate')
            ->where('user_id', auth()->id())
            ->whereNull('paid_at')
            ->latest()
            ->firstOrFail();

        $paymentIntent = auth()->user()->createSetupIntent();

        return view('checkout', compact('payment', 'paymentIntent'));
    }

    public function pay(Request $request)
    {
        $user = auth()->user();
        $donate = Payment::query()
            ->whereBelongsTo($user)
            ->findOrFail($request->input('payment_id'));

        $paymentMethod = $request->input('payment_method');

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);
            $user->invoiceFor('Donation', $donate->price);
        } catch (\Exception $ex) {
            return back()->with('error', $ex->getMessage());
        }

        return redirect()->route('about')->with('success', 'Payment Successful, Thank you for your donation!');
    }
}
