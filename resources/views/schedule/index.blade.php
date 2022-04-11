@extends('layouts.app')

@push('cdn')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
@endpush

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{__('Schedule')}}</li>
        </ol>
    </nav>

    @if(!$config)
        <div class="card">
            <div class="card-header">
                {{__('Schedule')}}
            </div>
            <div class="card-body">
                <p class="alert alert-danger">
                    {{__('Hi, Looks like you are first time to use the system, please submit your detail')}}
                </p>
                <form action="{{route('scheduleConfig.store')}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="intake_code">{{__('Select your Intake Code')}}</label>
                        <select name="intake_code" id="intake_code" onchange="renderGrouping(this)" class="form-select">
                            <option value="" hidden="" disabled="">{{__('Select your Intake Code')}}</option>
                            @foreach(\Chengkangzai\ApuSchedule\ApuSchedule::getIntakes() as $intakeCode)
                                <option value="{{$intakeCode}}">{{$intakeCode}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="grouping">{{__('Select your Grouping')}}</label>
                        <select name="grouping" disabled class="form-select" id="grouping" required>
                            <option value="" hidden="" disabled="" selected>
                                {{__('Select your Grouping First')}}
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label" for="is_subscribed">{{__('Sync my schedule weekly')}}</label>
                        <input type="checkbox" value="1" name="is_subscribed" id="is_subscribed"
                               {{old('is_subscribed') ? 'checked':''}} class="form-check-input">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        {{__('Save Configuration')}}
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="text-center">
                    {{__('Hello :name, you are already setup your schedule and the schedule will be sync to your calendar whenever its updated',['name'=>auth()->user()->name ?? ''])}}
                </p>
                <div>
                    <a href="{{route('scheduleConfig.edit',$config)}}" class="btn btn-primary">{{__('Edit')}}</a>
                    <button type="button" class="btn btn-warning"
                            data-coreui-toggle="modal"
                            data-coreui-target="#calendarModal">
                        {{__('View Your Time Table')}}
                    </button>
                    <a href="{{route('schedule.syncNow')}}" class="btn btn-danger">{{__('Sync it NOW!')}}</a>
                </div>
                <table class="table table-striped my-2">
                    <tr>
                        <td>{{__('Intake Code')}} :</td>
                        <td>{{$config->intake_code}}</td>
                    </tr>
                    <tr>
                        <td>{{__('Grouping')}} :</td>
                        <td>{{$config->grouping}}</td>
                    </tr>
                    <tr>
                        <td>{{__('Ignored Module')}} :</td>
                        <td>
                            @forelse($config->except as $ignoredModule)
                                @if ($loop->last)
                                    {{$ignoredModule}}
                                @else
                                    {{$ignoredModule}},
                                @endif
                            @empty
                                {{__('None')}}
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td>{{__('Sync Weekly')}} :</td>
                        <td>{{$config->is_subscribed ? __('Yes'):__('No')}}</td>
                    </tr>
                    <tr>
                        <td>{{__('Linked to Microsoft ?')}} :</td>
                        <td>
                            @if(auth()->user()->msOauth()->exists())
                                {{__('Yes')}} ({{auth()->user()->msOauth()->first()->userEmail}})
                            @else
                                {{__('No')}}
                                (<a href="{{route('msOAuth.signin')}}">
                                    {{__('Click here to link your account')}}
                                </a>)
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($config)
            <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModal" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="calendarModalLabel">{{__('Your Time Table')}}</h5>
                            <button type="button" class="btn-close" data-coreui-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id='calendar' class="bg-white"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-coreui-dismiss="modal">{{__('Close')}}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
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
                });

        }
    </script>

    @if($config)
        <script>
            var myModalEl = document.getElementById('calendarModal')
            myModalEl.addEventListener('shown.coreui.modal', function (event) {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {center: 'dayGridMonth,timeGridWeek'}, // buttons for switching between views
                    initialView: 'timeGridWeek',
                    aspectRatio: 1.8,
                });

                @foreach($events as $event)
                calendar.addEvent({
                    title: '{{$event->MODID}}',
                    start: '{{$event->TIME_FROM_ISO}}',
                    end: '{{$event->TIME_TO_ISO}}',
                });
                @endforeach

                calendar.render();
            });
        </script>
    @endif
@endpush
