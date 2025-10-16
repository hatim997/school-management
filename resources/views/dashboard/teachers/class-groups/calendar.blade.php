@extends('layouts.master')

@section('title', __('Calendar'))

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
@endsection

@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Calendar') }}</li>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-12">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title m-0">📅 My Session Schedule Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- FullCalendar (Global Build) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const events = @json($events);

            // 🎨 Dynamic color palette
            const colorPalette = [
                '#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#E91E63',
                '#00BCD4', '#8BC34A', '#FFC107', '#795548', '#607D8B'
            ];

            const subjectColorMap = {};
            let colorIndex = 0;

            // 🧩 Apply colors dynamically per subject
            const coloredEvents = events.map(event => {
                const subject = event.subject || 'Unknown';

                if (!subjectColorMap[subject]) {
                    subjectColorMap[subject] = colorPalette[colorIndex % colorPalette.length];
                    colorIndex++;
                }

                return {
                    ...event,
                    title: event.group_name,
                    backgroundColor: subjectColorMap[subject],
                    borderColor: subjectColorMap[subject],
                    textColor: '#fff',
                    display: 'block',
                    allDay: false
                };
            });

            // 🗓️ Initialize calendar
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                nowIndicator: true,
                events: coloredEvents,
                eventDidMount(info) {
                    const details = `
                        <div style="font-size: 13px;">
                            <strong>Group:</strong> ${info.event.title}<br>
                            <strong>Subject:</strong> ${info.event.extendedProps.subject}<br>
                            <strong>Teacher:</strong> ${info.event.extendedProps.teacher}<br>
                            <strong>Day:</strong> ${info.event.extendedProps.day}<br>
                            <strong>Time:</strong> ${info.event.extendedProps.start_time} - ${info.event.extendedProps.end_time}
                        </div>
                    `;
                    new bootstrap.Tooltip(info.el, {
                        title: details,
                        html: true,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: true
                }
            });

            calendar.render();
        });
    </script>
@endsection
