@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            {{ __('About') }}
        </div>
        <div class="card-body">
            <p>
                {{__('This web application is developed by')}} <a
                    href="https://github.com/chengkangzai/">{{__('CCK')}}</a>.
            </p>
            <p>
                {{__('Visit the source code at')}} <a
                    href="https://github.com/chengkangzai/time-stone">{{__('Github')}}</a>
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Donate') }}
        </div>
        <div class="card-body">
            <p>
                {{__('If this application is useful to you, please consider to donate to the developer.')}}
            </p>
            <form method="POST" action="{{route('payments.confirm')}}">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="donate">{{__('Donate Amount (MYR)')}}</label>
                        <input type="number" class="form-control" id="donate" name="price" value="10"
                               placeholder="{{__('Amount')}}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">{{__('Next')}}</button>
            </form>
        </div>
    </div>
@endsection

{{--@push('script')--}}
{{--    <script src="https://js.stripe.com/v3/"></script>--}}
{{--    <script>--}}
{{--        const stripe = Stripe('{{config('services.stripe.key')}}');--}}
{{--        const elements = stripe.elements({--}}
{{--            locale: '{{app()->getLocale()}}',--}}
{{--        });--}}

{{--        //create an instance of the card Element that look like bootstrap form--}}
{{--        const cardElement = elements.create('card', {--}}
{{--            style: {--}}
{{--                base: {--}}
{{--                    iconColor: '#666EE8',--}}
{{--                    color: '#31325F',--}}
{{--                    lineHeight: '30px',--}}
{{--                    fontWeight: 300,--}}
{{--                    fontSize: '15px',--}}

{{--                },--}}
{{--            },--}}
{{--        });--}}
{{--        cardElement.mount('#card-element');--}}

{{--        $('#payment-button').on('click', function () {--}}
{{--            $('#payment-button').attr('disabled', true);--}}
{{--            stripe--}}
{{--                .confirmCardSetup('{{ $paymentIntent->client_secret }}', {--}}
{{--                    payment_method: {--}}
{{--                        card: cardElement,--}}
{{--                        billing_details: {--}}
{{--                            name: "{{ auth()->user()->name }}",--}}
{{--                        },--}}
{{--                    },--}}
{{--                })--}}
{{--                .then(function (result) {--}}
{{--                    if (result.error) {--}}
{{--                        $('#card-error').text(result.error.message).removeClass('d-none');--}}
{{--                        $('#payment-button').attr('disabled', false);--}}
{{--                    } else {--}}
{{--                        $('#payment-method').val(result.setupIntent.payment_method);--}}
{{--                        $('#payment-form').submit();--}}
{{--                    }--}}
{{--                });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}
