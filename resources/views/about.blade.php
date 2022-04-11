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
