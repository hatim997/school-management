@extends('layouts.master')

@section('title', __('Enrolled Subjects'))

@section('css')
@endsection


@section('breadcrumb-items')
    <li class="breadcrumb-item active">{{ __('Enrolled Subjects') }}</li>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="app-academy">
            <div class="card mb-6">
                <div class="card-header d-flex flex-wrap justify-content-between gap-4">
                    <div class="card-title mb-0 me-1">
                        <h5 class="mb-0">My Courses</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-6 mb-6">
                        @if (isset($classGroups) && count($classGroups) > 0)
                            @foreach ($classGroups as $classGroup)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-4">
                                            <a href="{{ route('dashboard.students.enrolled-subjects.show', $classGroup->id) }}">
                                                <img class="img-fluid" style="height: 180px; object-fit: cover;" src="{{ $classGroup->subject->image ? asset($classGroup->subject->image) : asset('uploads/subjects/subject-default-image.jpg') }}"
                                                    alt="{{ $classGroup->subject->name }}" /></a>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span class="badge bg-label-primary">{{ $classGroup->name }}</span>
                                                <p class="d-flex align-items-center mb-1">
                                                    <i class="icon-base ti ti-clock me-1"></i>{{ $classGroup->subject->duration }}
                                                    weeks
                                                </p>
                                            </div>
                                            <a href="{{ route('dashboard.students.enrolled-subjects.show', $classGroup->id) }}"
                                                class="h5">{{ $classGroup->subject->name }}</a>
                                            <div
                                                class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                                <a class="w-100 btn btn-info d-flex align-items-center"
                                                    href="{{ route('dashboard.students.enrolled-subjects.show', $classGroup->id) }}">
                                                    <span class="me-2">View Details
                                                    </span><i class="icon-base ti ti-chevron-right icon-xs lh-1 scaleX-n1-rtl"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
