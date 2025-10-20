@extends('layouts.master')

@section('title', __('Enrolled Subject Details'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a
            href="{{ route('dashboard.students.enrolled-subjects.index') }}">{{ __('Enrolled Subjects') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Details') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <div class="mb-1">
                    <span class="h5">{{ $classGroup->name }} </span>
                    <span class="badge bg-label-info">{{ $classGroup->subject->name }}</span>
                </div>
                <p class="mb-0">{{ $classGroup->created_at->format('d, M Y') }}</p>
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title m-0">Schedule</h5>
                    </div>
                    <div class="card-body pt-1">
                        <ul class="timeline pb-0 mb-0">
                            @php
                                use Carbon\Carbon;
                                $now = Carbon::now();
                                $today = strtolower($now->format('l')); // e.g. monday, tuesday...
                            @endphp
                            @if (isset($classGroup->schedules) && count($classGroup->schedules) > 0)
                                @foreach ($classGroup->schedules as $schedule)
                                    @php
                                        $scheduleDay = strtolower($schedule->day);
                                        $startTime = Carbon::parse($schedule->start_time);
                                        $endTime = Carbon::parse($schedule->end_time);

                                        // Set time boundaries
                                        $activateAt = $startTime->copy()->subMinutes(15);
                                        $deactivateAt = $endTime->copy()->addMinutes(15);

                                        // Determine status
                                        $isToday = $scheduleDay === $today;
                                        $isActive = $isToday && $now->between($activateAt, $deactivateAt);
                                        $isUpcoming = $isToday && $now->lt($activateAt);
                                    @endphp
                                    <li class="timeline-item timeline-item-transparent border-primary">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">{{ ucfirst($schedule->day) }}</h6>
                                                @if ($schedule->zoom_link)
                                                    @if ($isActive)
                                                        {{-- ✅ Active Join button --}}
                                                        {{-- <a href="{{ $schedule->zoom_link }}" target="_blank"
                                                            class="btn btn-sm btn-success">
                                                            Join Now
                                                        </a> --}}
                                                        <button class="btn btn-sm btn-success join-btn"
                                                            data-class-group-id="{{ $classGroup->id }}"
                                                            data-zoom-link="{{ $schedule->zoom_link }}">
                                                            Join Now
                                                        </button>
                                                    @elseif ($isUpcoming)
                                                        {{-- ⏳ Countdown placeholder --}}
                                                        <button class="btn btn-sm btn-warning countdown-btn"
                                                            data-start="{{ $activateAt->format('Y-m-d H:i:s') }}" disabled>
                                                            Starts in <span class="countdown-timer mx-2">--:--:--</span>
                                                        </button>
                                                    @else
                                                        {{-- ❌ Inactive --}}
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            Not Available
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                            <p class="mt-3">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('h:i A') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-clock text-muted" style="font-size: 48px;"></i>
                                    <p class="mt-2 text-muted">No Schedule Available</p>
                                </div>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title m-0">Teacher Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-6">
                            <div class="avatar me-3">
                                <img src="{{ asset($classGroup->teacher->profile->profile_image ?? 'assets/img/default/user.png') }}"
                                    alt="Avatar" class="rounded-circle" />
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ $classGroup->teacher->name }}</h6>
                                </a>
                                <span>{{ $classGroup->teacher->profile->qualifications }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">Contact info</h6>
                        </div>
                        <p class="mb-1">Email: {{ $classGroup->teacher->email }}</p>
                        {{-- <p class="mb-0">Mobile: +1 (609) 972-22-22</p> --}}
                    </div>
                </div>

                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title m-0">Subject Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start align-items-center mb-6">
                            <div class="avatar me-3">
                                <img src="{{ asset($classGroup->subject->image ?? 'uploads/subjects/subject-default-image.jpg') }}"
                                    alt="{{ $classGroup->subject->name }}" class="rounded-circle" />
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-body text-nowrap">
                                    <h6 class="mb-0">{{ $classGroup->subject->name }}</h6>
                                </a>
                                <span>{{ $classGroup->subject->code }}</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1">Other info</h6>
                        </div>
                        <p class="mb-1">Duration: {{ $classGroup->subject->duration }} weeks</p>
                        <p class="mb-1">Total Enrolled: {{ $classGroup->subject->total_enrolled }}</p>
                        <p class="mb-1">Rating: {{ $classGroup->subject->rating }} <span class="text-warning"><i
                                    class="icon-base ti ti-star-filled icon-lg me-1 mb-1_5"></i></span></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Group Materials</h5>
                    </div>
                    <div class="card-body">
                        <div class="card-datatable table-responsive" style="overflow-x: hidden;">
                            @if (isset($classGroupMaterials) && count($classGroupMaterials) > 0)
                                <div class="row g-3">
                                    @foreach ($classGroupMaterials as $material)
                                        @php
                                            // Detect file type for icon
                                            $icon = 'ti ti-file';
                                            if (Str::contains($material->file_type, 'pdf')) {
                                                $icon = 'ti ti-file-text text-danger';
                                            } elseif (Str::contains($material->file_type, 'image')) {
                                                $icon = 'ti ti-photo text-success';
                                            } elseif (Str::contains($material->file_type, 'word')) {
                                                $icon = 'ti ti-file-description text-primary';
                                            } elseif (
                                                Str::contains($material->file_type, 'excel') ||
                                                Str::contains($material->file_type, 'spreadsheet')
                                            ) {
                                                $icon = 'ti ti-file-spreadsheet text-success';
                                            } elseif (Str::contains($material->file_type, 'video')) {
                                                $icon = 'ti ti-video text-warning';
                                            } elseif (Str::contains($material->file_type, 'audio')) {
                                                $icon = 'ti ti-music text-info';
                                            } elseif (Str::contains($material->file_type, 'zip')) {
                                                $icon = 'ti ti-file-zip text-secondary';
                                            }
                                        @endphp

                                        <div class="col-sm-6 col-lg-4">
                                            <div class="card p-3 h-100 border shadow-sm">
                                                <div class="text-center mb-3">
                                                    <i class="{{ $icon }}" style="font-size: 48px;"></i>
                                                </div>

                                                <div class="card-body p-2">
                                                    <h6 class="fw-bold text-primary mb-2 text-truncate">
                                                        {{ $material->file_name }}
                                                    </h6>

                                                    <ul class="list-unstyled small mb-3">
                                                        <li><strong>Type:</strong> {{ $material->file_type }}</li>
                                                        <li><strong>Size:</strong>
                                                            {{ \App\Helpers\Helper::humanReadableSize($material->file_size) }}
                                                        </li>
                                                        <li><strong>Uploaded By:</strong>
                                                            {{ $material->user->name ?? 'Unknown' }}
                                                        </li>
                                                        <li><strong>Date:</strong>
                                                            {{ $material->created_at->format('d M, Y') }}
                                                        </li>
                                                    </ul>

                                                    <a href="{{ asset($material->file) }}" target="_blank"
                                                        class="btn btn-sm btn-primary w-100">
                                                        <i class="ti ti-eye me-1"></i> Preview / Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="ti ti-folder-off text-muted" style="font-size: 48px;"></i>
                                    <p class="mt-2 text-muted">No Materials Available</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
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

                        const href = $button.data('href');
                        if (href) {
                            $button.off('click').on('click', function() {
                                window.open(href, '_blank');
                            });
                        }
                        return;
                    }

                    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    $timerSpan.text(
                        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds
                    .toString()
                    .padStart(2, '0')}`
                    );
                }, 1000);
            });

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
                            // Redirect to Zoom link after marking attendance
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
