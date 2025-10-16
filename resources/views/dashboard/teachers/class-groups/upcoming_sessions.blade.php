@extends('layouts.master')

@section('title', __('Upcoming Sessions'))

@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Upcoming Sessions') }}</li>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-12">
            <div class="card mb-6">
                <div class="card-header">
                    <h5 class="card-title m-0">Today's Sessions</h5>
                </div>
                <div class="card-body pt-1">
                    @php
                        use Carbon\Carbon;
                        $now = Carbon::now();
                        $today = strtolower($now->format('l'));
                    @endphp
                    @if ($todayClasses->isNotEmpty())
                        <ul class="timeline pb-0 mb-0">
                            @foreach ($todayClasses as $class)
                                @foreach ($class->schedules as $schedule)
                                    @php
                                        $scheduleDay = strtolower($schedule->day);
                                        $startTime = Carbon::parse($schedule->start_time);
                                        $endTime = Carbon::parse($schedule->end_time);

                                        $activateAt = $startTime->copy()->subMinutes(30);
                                        $deactivateAt = $endTime->copy()->addMinutes(30);

                                        $isToday = $scheduleDay === $today;
                                        $isActive = $isToday && $now->between($activateAt, $deactivateAt);
                                        $isUpcoming = $isToday && $now->lt($activateAt);
                                    @endphp

                                    <li class="timeline-item timeline-item-transparent border-primary">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $class->subject->name }} — {{ $class->name }}</h6>
                                                {{-- <small class="text-body-secondary">
                                                    <strong>Teacher:</strong> {{ $class->teacher->name ?? 'N/A' }}
                                                </small> --}}
                                            </div>
                                            <p class="mt-3 mb-2">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('h:i A') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('h:i A') }}
                                            </p>

                                            {{-- ===== BUTTON LOGIC ===== --}}
                                            @if ($schedule->zoom_link)
                                                @if ($isActive)
                                                    <button class="btn btn-sm btn-success join-btn"
                                                        data-class-group-id="{{ $class->id }}"
                                                        data-zoom-link="{{ $schedule->zoom_link }}">
                                                        Join Now
                                                    </button>
                                                @elseif ($isUpcoming)
                                                    <button class="btn btn-sm btn-warning countdown-btn"
                                                        data-start="{{ $activateAt->format('Y-m-d H:i:s') }}" disabled>
                                                        Starts in <span class="countdown-timer mx-2">--:--:--</span>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        Not Available
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            @endforeach
                        </ul>
                    @else
                        <p>No sessions scheduled for today.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Countdown timer logic (same as enrolled_subject_details)
            $('.countdown-btn').each(function() {
                const $button = $(this);
                const startTime = new Date($button.data('start')).getTime();
                const $timerSpan = $button.find('.countdown-timer');

                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = startTime - now;

                    if (distance <= 0) {
                        clearInterval(interval);
                        $button
                            .removeClass('btn-warning')
                            .addClass('btn-success')
                            .prop('disabled', false)
                            .text('Join Now');
                        return;
                    }

                    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    $timerSpan.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
                    );
                }, 1000);
            });

            // Attendance + Join logic (same as enrolled_subject_details)
            $('.join-btn').on('click', function() {
                window.open(zoomLink, '_blank');
            });
        });
    </script>
@endsection
