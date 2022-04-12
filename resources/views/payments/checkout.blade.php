@php
/** @var \App\Models\Payment $payment */
@endphp
@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Donate') }}
        </div>
        <div class="card-body">
            <p>{{ __('Its not easy to develop and maintain this web application, donation is deeply appreciated.') }}</p>
            <div class="alert alert-danger d-none" id="card-error">
            </div>
            <form action="{{ route('payments.pay') }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="payment_method" id="payment-method" value="" />
                <input type="hidden" name="payment_id" value="{{ $payment->id }}" />
                <div class="col-md-6">
                    <div id="card-element"></div>
                    <button type="button" class="mt-4 btn btn-primary" id="payment-button">
                        Pay MYR {{ round($payment->donate->price / 100, 2) }}</button>
                    @if (session('error'))
                        <div class="alert alert-danger mt-4">{{ session('error') }}</div>
                    @endif
                    <div class="alert alert-danger mt-4 d-none" id="card-error"></div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements({
            locale: '{{ app()->getLocale() }}',
        });

        //create an instance of the card Element that look like bootstrap form
        const cardElement = elements.create('card', {
            style: {
                base: {
                    iconColor: '#666EE8',
                    color: '#31325F',
                    lineHeight: '30px',
                    fontWeight: 300,
                    fontSize: '15px',

                },
            },
        });
        cardElement.mount('#card-element');

        $('#payment-button').on('click', function() {
            $('#payment-button').attr('disabled', true);
            stripe
                .confirmCardSetup('{{ $paymentIntent->client_secret }}', {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: "{{ auth()->user()->name }}",
                        },
                    },
                })
                .then(function(result) {
                    if (result.error) {
                        $('#card-error').text(result.error.message).removeClass('d-none');
                        $('#payment-button').attr('disabled', false);
                    } else {
                        $('#payment-method').val(result.setupIntent.payment_method);
                        $('#payment-form').submit();
                    }
                });
        });
    </script>
@endpush
