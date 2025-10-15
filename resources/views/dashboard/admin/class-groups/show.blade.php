@extends('layouts.master')

@section('title', __('Class Group Details'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.admin-class-groups.index') }}">{{ __('Class Groups') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Class Group Details') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <div class="mb-1">
                    <span class="h5">{{ $classGroup->name }} </span>
                    <span class="badge bg-label-info">{{ $classGroup->subject->name }}</span>
                    <span class="badge bg-label-primary">{{ $classGroup->teacher->name }}</span>
                </div>
                <p class="mb-0">{{ $classGroup->created_at->format('d, M Y') }}</p>
            </div>
        </div>

        <!-- Order Details Table -->

        <div class="row">
            <div class="col-6 col-lg-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5>Students in {{ $classGroup->name }}</h5>
                    </div>
                    <div class="card-datatable">
                        <table class="datatables-users table border-top">
                            <thead>
                                <tr>
                                    <th>{{ __('Sr.') }}</th>
                                    <th>{{ __('Student') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center mb-6">
                                                <div class="avatar me-3">
                                                    <img src="{{ $student['child_image'] }}" alt="Avatar"
                                                        class="rounded-circle" />
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="#" class="text-body text-nowrap">
                                                        <h6 class="mb-0">{{ $student['child_name'] }}</h6>
                                                    </a>
                                                    <span>{{ $student['parent_name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-6">
                <div class="card mb-6">
                    <div class="card-header">
                        <h5 class="card-title m-0">Schedule</h5>
                    </div>
                    <div class="card-body pt-1">
                        <ul class="timeline pb-0 mb-0">
                            @if (isset($classGroupSchedules) && count($classGroupSchedules) > 0)
                                @foreach ($classGroupSchedules as $schedule)
                                    <li class="timeline-item timeline-item-transparent border-primary">
                                        <span class="timeline-point timeline-point-primary"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header">
                                                <h6 class="mb-0">{{ ucfirst($schedule->day) }}</h6>
                                                @if ($schedule->zoom_link)
                                                    <small class="text-body-secondary"><a href="{{ $schedule->zoom_link }}" class="btn btn-link">Join Now</a></small>
                                                @endif
                                            </div>
                                            <p class="mt-3">{{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('h:i A') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
