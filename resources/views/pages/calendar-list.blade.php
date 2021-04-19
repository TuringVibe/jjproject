@extends('layouts.master')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('lib/fullcalendar-5.6.0/lib/main.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/content.css') }}">
    <style>
        .label-list {
            display: inline-block;
            padding: .3rem;
            border-radius: 5px;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        @include('components.content-header',[
                'with_btn' => true,
                'btn_label' => 'Create Event',
                'action' => 'openModal()'
            ]
        )
        <div id="calendar"></div>
        @include('components.popup-event')
    </div>
@endsection

@push('ready-scripts')
var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
        start: 'prevYear prev',
        center: 'title',
        end: 'today next nextYear'
    },
    themeSystem: 'bootstrap',
    initialView: 'dayGridMonth',
    events: {
        url: @json(route('events.data')),
        method: 'GET',
        extraParams: {
            'tz': timezone
        },
        failure: 'Failed to retrieve data'
    },
    eventClick: function(info) {
        $('#popup-event').data('action','show');
        $('#popup-event').data('id',info.event.id);
        $('#popup-event').modal('show');
    },
    dateClick: function(info) {
        $('#popup-event').removeData();
        $('#popup-event').data('action','create');
        $('#popup-event').find('#startdatetime').val(info.dateStr+" 00:00:00");
        $('#popup-event').find('#enddatetime').val(info.dateStr+" 00:00:00");
        $('#popup-event').modal('show');
    }
});
calendar.render();
$('#calendar').on('mutated', function (e) {
    calendar.refetchEvents();
})
@endpush

@push('scripts')
    <script src="{{ asset('lib/rrule-tz.min.js') }}"></script>
    <script src="{{ asset('lib/fullcalendar-5.6.0/lib/main.min.js') }}"></script>
    <script src="{{ asset('lib/fullcalendar-5.6.0/lib/main.global.min.js') }}"></script>
    <script>
        var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        function openModal() {
            $('#popup-event').removeData();
            $('#popup-event').data('action','create');
            $('#popup-event').modal('show');
        }
    </script>
@endpush
