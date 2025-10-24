<!-- View sales -->
<div class="col-xl-6">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-7">
                        <div class="card-body text-nowrap">
                            <h5 class="card-title mb-0">Welcome {{ Auth::user()->name }}! 🎉</h5>
                            <p class="mb-2">Here what's happening in your account today</p>
                            <a href="{{ route('profile.index') }}" class="btn btn-primary">View Profile</a>
                        </div>
                    </div>
                    <div class="col-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140"
                                alt="view sales" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="row">
                <!-- Total Parents -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-users icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['total_classes'] }}</h4>
                            </div>
                            <p class="mb-1">Total Classes</p>
                        </div>
                    </div>
                </div>

                <!-- Total Enrolled Children -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-warning h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="ti ti-school icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['total_students'] }}</h4>
                            </div>
                            <p class="mb-1">Total Students</p>
                        </div>
                    </div>
                </div>

                <!-- Total Teachers -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-danger h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-danger">
                                        <i class="ti ti-chalkboard icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['active_subjects'] }}</h4>
                            </div>
                            <p class="mb-1">Active Subjects</p>
                        </div>
                    </div>
                </div>

                <!-- Total Groups -->
                <div class="col-lg-6 col-sm-6 mt-2">
                    <div class="card card-border-shadow-info h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-4">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ti ti-users-group icon-28px"></i>
                                    </span>
                                </div>
                                <h4 class="mb-0">{{ $stats['active_groups'] }}</h4>
                            </div>
                            <p class="mb-1">Active Groups</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- View sales -->

<div class="col-12 col-xl-6 col-md-6">
    <div class="card h-100">
        <div class="card-body">
            <div class="bg-label-primary rounded-3 text-center mb-4 pt-6">
                <img class="img-fluid" src="{{ asset('assets/img/illustrations/girl-with-laptop.png') }}"
                    alt="Card girl image" width="140" />
            </div>

            <h5 class="mb-2">Upcoming Class</h5>
            @if ($todayClass && $todayClass->schedules->isNotEmpty())
                @php
                    $schedule = $todayClass->schedules->first();
                    $now = Carbon\Carbon::now();
                    $startTime = Carbon\Carbon::parse($schedule->start_time);
                    $endTime = Carbon\Carbon::parse($schedule->end_time);
                    $activateAt = $startTime->copy()->subMinutes(15);
                    $deactivateAt = $endTime->copy()->addMinutes(15);
                    $isActive = $now->between($activateAt, $deactivateAt);
                    $isUpcoming = $now->lt($activateAt);
                @endphp

                <p>Teacher: {{ $todayClass->teacher->name }}</p>

                <div class="row mb-4 g-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti ti-calendar-event icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-nowrap">{{ $todayClass->name }}</h6>
                                <small>{{ $todayClass->subject->name }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="icon-base ti ti-clock icon-28px"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0 text-nowrap">
                                    {{ $startTime->format('h:i A') }} - {{ $endTime->format('h:i A') }}
                                </h6>
                                <small>Class Time</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 text-center">
                    @if ($schedule->zoom_link)
                        @if ($isActive)
                            <button class="btn btn-success w-100 d-grid join-btn"
                                data-class-group-id="{{ $todayClass->id }}"
                                data-zoom-link="{{ $schedule->zoom_link }}">
                                Join Now
                            </button>
                        @elseif ($isUpcoming)
                            <button class="btn btn-warning w-100 d-grid countdown-btn"
                                data-start="{{ $activateAt->format('Y-m-d H:i:s') }}" disabled>
                                Starts in <span class="countdown-timer mx-2">--:--:--</span>
                            </button>
                        @else
                            <button class="btn btn-secondary w-100 d-grid" disabled>
                                Not Available
                            </button>
                        @endif
                    @endif
                </div>
            @else
                <div class="text-center py-4">
                    <i class="ti ti-school text-muted" style="font-size: 48px;"></i>
                    <p class="mt-2 text-muted">No Upcoming Class Today</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="col-md-12">
    {{-- Chart Section --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card h-100 p-3">
                <h5>Students Per Class</h5>
                <canvas id="studentsPerClassChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 p-3">
                <h5>Class Capacity Utilization</h5>
                <canvas id="classCapacityChart"></canvas>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="card h-100 p-3">
                <h5>Weekly Schedule Overview</h5>
                <canvas id="weeklyScheduleChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {

        // 📊 Students per Class
        const studentCtx = document.getElementById('studentsPerClassChart');
        new Chart(studentCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['students_per_class']->pluck('name')),
                datasets: [{
                    label: 'Students',
                    data: @json($charts['students_per_class']->pluck('total')),
                }]
            },
            options: {
                responsive: true
            }
        });

        // 📈 Class Capacity Utilization
        const capacityCtx = document.getElementById('classCapacityChart');
        new Chart(capacityCtx, {
            type: 'doughnut',
            data: {
                labels: @json($charts['class_capacity']->pluck('name')),
                datasets: [{
                    label: 'Utilization (%)',
                    data: @json($charts['class_capacity']->pluck('utilization')),
                }]
            },
            options: {
                responsive: true
            }
        });

        // 🗓️ Weekly Schedule
        const weeklyCtx = document.getElementById('weeklyScheduleChart');
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: @json($charts['weekly_schedule']->pluck('day')),
                datasets: [{
                    label: 'Classes Scheduled',
                    data: @json($charts['weekly_schedule']->pluck('count')),
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>
