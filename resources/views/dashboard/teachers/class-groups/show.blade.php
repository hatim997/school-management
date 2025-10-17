@extends('layouts.master')

@section('title', __('Class Group Details'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.class-groups.index') }}">{{ __('Class Groups') }}</a></li>
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
                                                    <small class="text-body-secondary"><a href="{{ $schedule->zoom_link }}"
                                                            class="btn btn-link">Join Now</a></small>
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
    {{-- <script src="{{asset('assets/js/app-user-list.js')}}"></script> --}}
    <script>
        $(document).ready(function() {
            //
        });
    </script>
@endsection
