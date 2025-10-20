@extends('layouts.master')

@section('title', 'Dashboard')

@section('css')
@endsection

@section('breadcrumb-items')
    {{-- <li class="breadcrumb-item active">{{ __('Dashboard') }}</li> --}}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6">
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
                                        @if (Auth::user()->hasRole('parent'))
                                            <a href="{{ route('dashboard.children.index') }}" class="btn btn-primary">View
                                                Children</a>
                                        @else
                                            <a href="{{ route('profile.index') }}" class="btn btn-primary">View Profile</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-5 text-center text-sm-left">
                                    <div class="card-body pb-0 px-0 px-md-4">
                                        <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}"
                                            height="140" alt="view sales" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <div class="row">
                            @if (Auth::user()->hasRole('parent'))
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
                                                <h4 class="mb-0">2</h4>
                                            </div>
                                            <p class="mb-1">Total Children Enrolled</p>
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
                                                <h4 class="mb-0">8</h4>
                                            </div>
                                            <p class="mb-1">Total Courses Enrolled</p>
                                        </div>
                                    </div>
                                </div>
                            @else
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
                                                <h4 class="mb-0">42</h4>
                                            </div>
                                            <p class="mb-1">Total Parents</p>
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
                                                <h4 class="mb-0">8</h4>
                                            </div>
                                            <p class="mb-1">Total Enrolled Children</p>
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
                                                <h4 class="mb-0">27</h4>
                                            </div>
                                            <p class="mb-1">Total Teachers</p>
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
                                                <h4 class="mb-0">13</h4>
                                            </div>
                                            <p class="mb-1">Total Groups</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <!-- View sales -->
            @php
                use Carbon\Carbon;
            @endphp
            @if (Auth::user()->hasRole('student') || Auth::user()->hasRole('teacher'))
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
                                    $now = Carbon::now();
                                    $startTime = Carbon::parse($schedule->start_time);
                                    $endTime = Carbon::parse($schedule->end_time);
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
            @endif

            @if (Auth::user()->hasRole('parent'))
                <div class="col-12 col-xl-6 col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Popular Courses</h5>
                            </div>
                        </div>
                        <div class="px-5 py-4 border border-start-0 border-end-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-uppercase">Instructors</p>
                                <p class="mb-0 text-uppercase">Course</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/1.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Maven Analytics</h6>
                                            <small class="text-truncate text-body">Business Intelligence</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">English</h6>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/2.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Bentlee Emblin</h6>
                                            <small class="text-truncate text-body">Digital Marketing</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">Mathematics</h6>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/3.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Benedetto Rossiter</h6>
                                            <small class="text-truncate text-body">UI/UX Design</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">Physics</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-12 col-xl-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="card-title mb-0">
                                <h5 class="m-0 me-2">Popular Instructors</h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn text-body-secondary p-0" type="button" id="popularInstructors"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="popularInstructors">
                                    <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                </div>
                            </div>
                        </div>
                        <div class="px-5 py-4 border border-start-0 border-end-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-uppercase">Instructors</p>
                                <p class="mb-0 text-uppercase">courses</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/1.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Maven Analytics</h6>
                                            <small class="text-truncate text-body">Business Intelligence</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">33</h6>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/2.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Bentlee Emblin</h6>
                                            <small class="text-truncate text-body">Digital Marketing</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">52</h6>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/3.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Benedetto Rossiter</h6>
                                            <small class="text-truncate text-body">UI/UX Design</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">12</h6>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar me-4">
                                        <img src="../../assets/img/avatars/4.png" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <div>
                                            <h6 class="mb-0 text-truncate">Beverlie Krabbe</h6>
                                            <small class="text-truncate text-body">React Native</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">8</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-8">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Topic you are interested in</h5>
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="topic" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topic">
                                    <a class="dropdown-item" href="javascript:void(0);">Highest Views</a>
                                    <a class="dropdown-item" href="javascript:void(0);">See All</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body row g-3">
                            <div class="col-md-8">
                                <div id="horizontalBarChart"></div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-around align-items-center">
                                <div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-primary me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">UI Design</p>
                                            <h5>35%</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline my-12">
                                        <span class="text-success me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">Music</p>
                                            <h5>14%</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-danger me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">React</p>
                                            <h5>10%</h5>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-info me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">UX Design</p>
                                            <h5>20%</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline my-12">
                                        <span class="text-secondary me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">Animation</p>
                                            <h5>12%</h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-baseline">
                                        <span class="text-warning me-2"><i
                                                class="icon-base ti tabler-circle-filled icon-12px"></i></span>
                                        <div>
                                            <p class="mb-0">SEO</p>
                                            <h5>9%</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/js/app-academy-dashboard.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Countdown logic
            $('.countdown-btn').each(function() {
                const $button = $(this);
                const startTime = new Date($button.data('start')).getTime();
                const $timerSpan = $button.find('.countdown-timer');

                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = startTime - now;

                    if (distance <= 0) {
                        clearInterval(interval);
                        $button.removeClass('btn-warning').addClass('btn-success')
                            .prop('disabled', false).text('Join Now');
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

            // Attendance + Join logic
            $('.join-btn').on('click', function() {
                const button = $(this);
                const classGroupId = button.data('class-group-id');
                const zoomLink = button.data('zoom-link');

                button.prop('disabled', true).text('Marking attendance...');

                $.ajax({
                    url: "{{ route('dashboard.students.attendance.mark') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        class_group_id: classGroupId
                    },
                    success: function(response) {
                        if (response.success) {
                            button.text('Attendance Marked ✅');
                            window.open(zoomLink, '_blank');
                            setTimeout(() => {
                                button.text('Join Now').prop('disabled', false);
                            }, 5000);
                        } else {
                            button.text('Error, Try Again').prop('disabled', false);
                        }
                    },
                    error: function() {
                        button.text('Error, Try Again').prop('disabled', false);
                    }
                });
            });
        });
    </script>

@endsection
