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
                        @if (isset($studentSubjects) && count($studentSubjects) > 0)
                            @foreach ($studentSubjects as $studentSubject)
                                <div class="col-sm-6 col-lg-4">
                                    <div class="card p-2 h-100 shadow-none border">
                                        <div class="rounded-2 text-center mb-4">
                                            <a href="{{ route('dashboard.students.enrolled-subjects.show', $studentSubject->subject->id) }}"><img class="img-fluid"
                                                    src="{{ $studentSubject->subject->image ? asset($studentSubject->subject->image) : asset('uploads/subjects/subject-default-image.jpg') }}"
                                                    alt="{{ $studentSubject->subject->name }}" /></a>
                                        </div>
                                        <div class="card-body p-4 pt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <span
                                                    class="badge bg-label-primary">{{ $studentSubject->subject->code }}</span>
                                                <p
                                                    class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
                                                    {{ $studentSubject->subject->rating }}
                                                    <span class="text-warning"><i
                                                            class="icon-base ti ti-star-filled icon-lg me-1 mb-1_5"></i></span><span
                                                        class="fw-normal">({{ $studentSubject->subject->total_enrolled }})</span>
                                                </p>
                                            </div>
                                            <a href="app-academy-course-details.html"
                                                class="h5">{{ $studentSubject->subject->name }}</a>
                                            <p class="mt-1">{{ $studentSubject->subject->short_description }}</p>
                                            <p class="d-flex align-items-center mb-1">
                                                <i class="icon-base ti ti-clock me-1"></i>{{ $studentSubject->subject->duration }}
                                                weeks
                                            </p>
                                            <div
                                                class="d-flex flex-column flex-md-row gap-4 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                                                <a class="w-100 btn btn-info d-flex align-items-center"
                                                    href="{{ route('dashboard.students.enrolled-subjects.show', $studentSubject->subject->id) }}">
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
