@php
    /* @var App\Models\ScheduleConfig $scheduleConfig */
@endphp

@push('cdn')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@1.1/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('header')
    {{__('Schedule')}}
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home')}}">{{__('Home')}}</a></li>
                <li class="breadcrumb-item"><a href="{{route('scheduleConfig.index')}}">{{__('Schedule')}}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('Edit')}}</li>
            </ol>
        </nav>
        @include('partial.success-card')

        <div class="card">
            <div class="card-body">
                <form action="{{route('scheduleConfig.update', $scheduleConfig)}}" method="POST">
                    @method('PUT')
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="intake_code">
                            {{__('Select your Intake Code')}}
                        </label>
                        <select name="intake_code" id="intake_code" onchange="renderGrouping(this)" class="form-select">
                            <option value="" hidden="" disabled="">{{__('Select your Intake Code')}}</option>
                            @foreach(\Chengkangzai\ApuSchedule\ApuSchedule::getIntakes() as $intakeCode)
                                <option
                                    value="{{$intakeCode}}" {{$intakeCode == $scheduleConfig->intake_code ? 'selected':''}}>
                                    {{$intakeCode}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="grouping">
                            {{__('Select your Grouping')}}
                        </label>
                        <select name="grouping" id="grouping" class="form-select">
                            @foreach($groupings as $grouping)
                                <option
                                    value="{{$grouping}}" {{$grouping == $scheduleConfig->grouping ? 'selected':''}}>
                                    {{$grouping}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="ignore_modules">{{__('Ignore these module')}}</label>
                        <select name="except[]" multiple id="ignore_modules" class="form-select">
                            @foreach($modules as $module)
                                <option
                                    value="{{$module}}"
                                    {{in_array($module, $scheduleConfig->except ?? []) ? 'selected':''}}
                                >
                                    {{$module}}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-check-label" id="is_subscribed">{{__('Sync my schedule weekly')}}</label>
                        <input type="checkbox" value="{{$scheduleConfig->is_subscribed ? '1':'0' }}"
                               name="is_subscribed" {{$scheduleConfig->is_subscribed ? 'checked':''}}
                               id="is_subscribed" class="form-check-input"/>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            {{__('Save Configuration')}}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        function renderGrouping(dom) {
            fetch('{{route('schedule.getGrouping')}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                },
                body: JSON.stringify({
                    intake_code: dom.value
                })
            })
                .then(res => res.json())
                .then(data => {
                    let select = document.querySelector('select[name=grouping]');
                    select.disabled = false;
                    select.innerHTML = '';
                    data.forEach(grouping => {
                        let option = document.createElement('option');
                        option.value = grouping;
                        option.innerText = grouping;
                        select.appendChild(option);
                    });
                })
        }

        document
            .querySelector('input[name=is_subscribed]')
            .addEventListener('change', function () {
                this.value = +this.checked;
            });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@1.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        new TomSelect('#ignore_modules');
    </script>
@endpush
