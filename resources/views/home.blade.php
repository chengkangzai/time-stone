@extends('layouts.app')

@push('cdn')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
@endpush

@section('content')
    @if ($events->isNotEmpty())
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Your Time Table') }}
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    @if ($events->isNotEmpty())
        <script>
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    center: 'dayGridMonth,timeGridWeek'
                }, // buttons for switching between views
                initialView: 'timeGridWeek',
                aspectRatio: 2.3,
            });

            @foreach ($events as $event)
                calendar.addEvent({
                    title: '{{ $event->MODID }}',
                    start: '{{ $event->TIME_FROM_ISO }}',
                    end: '{{ $event->TIME_TO_ISO }}',
                });
            @endforeach

            calendar.render();
        </script>
    @endif
@endpush
